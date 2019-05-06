<?php 
declare(strict_types=1);

namespace CsvConverter;

const CR = '\r'; // '\x0D'
const LF = '\n'; // '\x0A'
const START = '^';
const COMMA = ',';
const DQUOTE = '"';
const CHARS = '[\x20-\xFE]';
const TEXTDATA = '(?:(?![' . DQUOTE . COMMA . '\x7F' . '])' . CHARS . ')';

const CRLF = CR . LF;
const CR_NOT_LF = CR . '(?!' . LF . ')';
const EOL = CRLF . '|' . CR . '|' . LF;
const DOUBLE_DQUOTE = DQUOTE . '{2}';

const NON_ESCAPED = '(?:' . CR_NOT_LF . '|' . LF . '|' .TEXTDATA . ')' . '+';

const ESCAPED = DQUOTE . '(?:' . DOUBLE_DQUOTE . '|' . TEXTDATA . '|' .  COMMA . '|' . CR . '|' . LF . ')*' . DQUOTE;
const HEAD = '(?:' . CRLF . '|' . COMMA . '|' . START . ')';
const TAIL = '(?:' . DQUOTE . '|' . CR_NOT_LF . '|[^' . CR . COMMA . '])*';
const BODY = '(?:' . ESCAPED . '|' . NON_ESCAPED . '|)';

const CSV_PATTERN = '/(?:' . HEAD . ')(?:' . BODY . ')(?:' . TAIL . ')/x';
const RECORD_PATTERN = '/^(' . HEAD . ')(' . BODY . ')(' . TAIL . ')$/x';

const SIGN = '[+-]?';
const DIGITS = '[0-9]+';
const INT_PATTERN = '/^' . SIGN . DIGITS . '$/';
const FLOAT_PATTERN = '/^' . SIGN . DIGITS . '\.' . DIGITS . '$/';

const EMPTY_PATTERN = '/^$/';
const OUTER_QUOTES = '/^"|"$/';
const INNER_QUOTES = '/""/';

function splitTokenToParts($token)
{
    preg_match(RECORD_PATTERN, $token[0][0], $parts, PREG_OFFSET_CAPTURE);
    $parts[0][1] = $token[0][1];
    return $parts;
}

function tokenize($data)
{
    $data = preg_replace('/' . CRLF . '\h*$/', "", $data);
    preg_match_all(CSV_PATTERN, $data, $tokens, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    $tokens = array_map('CsvConverter\splitTokenToParts', $tokens);
    return $tokens;
}

function convertValue($value, bool $convertToNull) {
    if (is_null($value)){
        return $value;
    }
    if (!is_string($value)){
        return $value;
    }
    if (preg_match(FLOAT_PATTERN, $value) > 0) {
        return floatval($value);
    }
    if (preg_match(INT_PATTERN, $value) > 0) {
        return intval($value);
    }
    if ($convertToNull && preg_match(EMPTY_PATTERN, $value) > 0) {
        return null;
    }
    $value = preg_replace(OUTER_QUOTES, '', $value);
    return preg_replace(INNER_QUOTES, '"', $value);
}  

function tokensToRecords(array $tokens)
{
    $records = [];
 
    array_walk($tokens, function(&$token) use (&$records) {
        if($token[1][0] !== ','){
            array_push($records, [$token]);
        } else {
            $records[count($records) - 1][] = $token;
        }
    });


    return $records;
}

function checkRecords(array $records, bool $hasHeader) : bool {
    if (count($records) < 1) {
        return false;
    }
    $fieldCount = count($records[0]);

    array_walk($records, function(array $record, $recordNo) use ($fieldCount, $hasHeader) {
        array_walk($record, function(array $field, $fieldNo) use ($recordNo) {
            if ($field[3][0] !== '') {
                $replaced = preg_replace(['/\r/', '/\n/'], ['\\r', '\\n'], [$field[0][0], $field[3][0]]);
                throw new \UnexpectedValueException("Record {$recordNo}, field {$fieldNo}: '{$replaced[0]}' has corrupted ending '{$replaced[1]}' at position {$field[3][1]}!");
            };
        });
        if ($hasHeader && $recordNo < 1) {
            array_walk($record, function($field, $fieldNo) {
                if ($field[2][0] === '') {
                    throw new \UnexpectedValueException("Header of field {$fieldNo} is empty!");
                }
                if ($field[2][0] === '""') {
                    throw new \UnexpectedValueException("Header of field {$fieldNo} is escaped empty string!");
                }
            });
        }
        if ($recordNo > 0){
            $currentFieldCount = count($record);
            if ($currentFieldCount > $fieldCount){
                throw new \RangeException("#{$recordNo} record has more fields than first record!");
            } elseif (($currentFieldCount < $fieldCount)) {
                throw new \RangeException("#{$recordNo} record has less fields than first record!");
            }
        }
    });
    return true;
};

function recordsToDataTree(array $records, bool $convertToNull = false) : array
{
    $tree = array_map(function($record) use ($convertToNull){
        return array_map(function($field) use ($convertToNull){
            return convertValue($field[2][0], $convertToNull);
        }, $record);
    }, $records);
    return $tree;
};

class CsvParser implements Parser
{
    private $parameters = [
        'hasHeader' => false, 
        'convertToNull' => false, 
        'convertToNumber' => false, 
        'preserveEmptyLine' => false, 
        'ignoreInvalidChars' => false 
    ];

    public function __construct(array $parameters = [])
    {
        $this->setParameters($parameters);
    }

    public static function dataType() : string
    {
        return 'csv';
    }

    private function getBooleanParameter($key) 
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
        throw new \InvalidArgumentException(
            "\n" . __METHOD__ . ".args['key']: '{$key}' is not a valid parameter's name!\n"
        );
    }

    private function setBooleanParameter(string $key, $value) 
    {
        if (!isset($this->parameters[$key])) {
            throw new \InvalidArgumentException(
                "\n" . __METHOD__ . ".args['key']: '{$key}' is not a valid parameter's name!\n"
            );
        }
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(
                "\n" . __METHOD__ . ".args['value']: '{$key}' accepts only values of boolean type!\n"
            );
        }
        $this->parameters[$key] = $value;
    }

    private function setParameters(array $values)
    {   
        if (count($values) < 1) {
            return;
        }
        forEach($this->parameters as $key => $value) {
            if (isset($values[$key]) && $values[$key] !== $value) {
                $this->setBooleanParameter($key, $values[$key]);
            }
        }
    }

    public function __get($key)
    {
        switch($key){
            case 'parameters': return $this->parameters;
            default: { 
                return $this->getBooleanParameter($key);
            }
        }
    }

    public function __set($key, $value)
    {
        switch($key){
            case 'parameters': $this->setParameters($value); break;
            default: { 
                $this->setBooleanParameter($key, $value);
            }
        }
    }

    public function __call($key, $args)
    {
        $this->__set($key, (count($args) < 1 || is_null($args[0])) ? 0 : $args[0]);
        return $this;
    }

    public function makeRecords(string $data)
    {
        $tokens = tokenize($data);
        $records = tokensToRecords($tokens);
        if (!$this->ignoreInvalidChars){
            checkRecords($records, $this->hasHeader);
        }
        return $records;
    }

    public function makeDataTree(string $data)
    {
        $records = $this->makeRecords($data);
        $tree = recordsToDataTree($records, $this->convertToNull);
        return $tree;
    }

}