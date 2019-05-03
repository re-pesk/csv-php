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

function convertValue($value, bool $withNull) {
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
    if ($withNull && preg_match(EMPTY_PATTERN, $value) > 0) {
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

function checkRecords(array $records, bool $withHeader) : bool {
    if (count($records) < 1) {
        return false;
    }
    $fieldCount = count($records[0]);

    array_walk($records, function(array $record, $recordNo) use ($fieldCount, $withHeader) {
        array_walk($record, function(array $field, $fieldNo) use ($recordNo) {
            if ($field[3][0] !== '') {
                $replaced = preg_replace(['/\r/', '/\n/'], ['\\r', '\\n'], [$field[0][0], $field[3][0]]);
                throw new \UnexpectedValueException("Record {$recordNo}, field {$fieldNo}: '{$replaced[0]}' has corrupted ending '{$replaced[1]}' at position {$field[3][1]}!");
            };
        });
        if ($withHeader && $recordNo < 1) {
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

function recordsToDataTree(array $records, bool $withNull = false) : array
{
    $tree = array_map(function($record) use ($withNull){
        return array_map(function($field) use ($withNull){
            return convertValue($field[2][0], $withNull);
        }, $record);
    }, $records);
    return $tree;
};

class CsvParser implements Parser
{
    private $with_header = false;
    private $with_null = false;
    private $auto_check = false;

    public function __construct(bool $with_header = false, bool $with_null = false, bool $auto_check = false)
    {
        $this->withHeader($with_header);
        $this->withNull($with_null);
        $this->autoCheck($auto_check);
    }

    public static function inputType() : string
    {
        return 'csv';
    }

    public function withHeader(bool $with_header)
    {
        $this->with_header = $with_header;
        return $this;
    }

    public function withNull(bool $with_null)
    {
        $this->with_null = $with_null;
        return $this;
    }

    public function autoCheck(bool $autocheck_null)
    {
        $this->auto_check = $autocheck_null;
        return $this;
    }

    public function __get($key)
    {
        switch($key){
            case 'withHeader': return $this->with_header;
            case 'withNull': return $this->with_null;
            case 'autoCheck': return $this->auto_check;
            default: { 
                throw new \InvalidArgumentException(
                    "\n" . __METHOD__ . '.args["key"]: ' . "'{$key}' is not a valid property name\n"
                );
            }
        }
    }

    public function __set($key, $value)
    {
        switch($key){
            case 'withHeader': $this->withHeader($value); break;
            case 'withNull': $this->withNull($value); break;
            case 'withNull': $this->autoCheck($value); break;
            default: { 
                throw new \InvalidArgumentException(
                    "\n" . __METHOD__ . '.args["key"]: ' . "'{$key}' is not a valid property name\n"
                );
            }
        }
    }

    public function makeRecords(string $data)
    {
        $tokens = tokenize($data);
        $records = tokensToRecords($tokens);
        if ($this->autoCheck){
            checkRecords($records, $this->withHeader);
        }
        return $records;
    }

    public function makeDataTree(string $data)
    {
        $records = $this->makeRecords($data);
        $tree = recordsToDataTree($records, $this->withNull);
        return $tree;
    }

}