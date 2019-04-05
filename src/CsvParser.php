<?php 

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

const NON_ESCAPED = '(?:' . CR_NOT_LF . '|' . TEXTDATA . ')' . '+';

const ESCAPED = DQUOTE . '(?:' . DOUBLE_DQUOTE . '|' . TEXTDATA . '|' .  COMMA . '|' . CR . '|' . LF . ')*' . DQUOTE;
const HEAD = '(?:' . CRLF . '|' . COMMA . '|' . START . ')';
const TAIL = '(?:' . DQUOTE . '|' . CR_NOT_LF . '|[^' . CR . COMMA . '])*';
const BODY = '(?:' . ESCAPED . '|' . NON_ESCAPED . '|)';

const CSV_PATTERN = '/(' . HEAD . ')(' . BODY . ')(' . TAIL . ')/mx';

const SIGN = '[+-]?';
const DIGITS = '[0-9]+';
const INT_PATTERN = '/^' . SIGN . DIGITS . '$/';
const FLOAT_PATTERN = '/^' . SIGN . DIGITS . '\.' . DIGITS . '$/';

const EMPTY_PATTERN = '/^$/';
const OUTER_QUOTES = '/^"|"$/';
const INNER_QUOTES = '/""/';

function tokenize($data)
{
    $data = preg_replace('/' . CRLF . '\h*$/', "", $data);
    preg_match_all(CSV_PATTERN, $data, $tokens, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

    return $tokens;
}

function convertValue($value, $withNull) {
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

function tokensToDataTree($tokens, $withHeader, $withNull)
{
    $records = [];
    $branch = $withHeader ? 'header' : 'first record';
    $fieldsCount = 0;
    array_walk($tokens, function($token) use (&$records, &$fieldsCount, $withNull, $branch) {
        static $fieldNo = 0;
        if ($token[3][0] !== '') {
            throw new \UnexpectedValueException("'{$token[3][0]}': corrupted end of field '{$token[0][0]}' starting at {$token[3][1]} character!");
        }
        $recordsCount = count($records);
        if($token[1][0] !== ','){
            if ($recordsCount == 1) {
                $fieldsCount = count($records[0]);
            }
            if ($recordsCount > 1 && count($records[$recordsCount - 1]) < $fieldsCount) {
                throw new RangeException(`Error occured before field '{$token[0][0]}' started at {$token[0][0]} character: last record has less fields than {$branch}!`);
            }
            array_push($records, []);
            $recordsCount++;
            $fieldNo = 1;
        }

        if ($recordsCount > 1) {
            if ($fieldNo > $fieldsCount) {
              throw new RangeException(`Index of curent field '{$token[0][0]}' started at {$token[0][1]} character is greater then number of fields in {$branch}!`);
            }
        }

        $value = null;
        $value = convertValue($token[2][0], $withNull);
        $records[$recordsCount - 1][] = $value;
        $fieldNo += 1;
    });
    if (count($records[count($records) - 1]) < $fieldsCount) {
        throw new RangeError(`Last record has less fields than ${branch}!`);
    }
    
    $tree = [];
    if ($withHeader) {
    $tree['header'] = array_shift($records);
    }
    if (count($records) > 0) {
    $tree['records'] = $records;
    }
    
    return $tree;
}


class CsvParser implements Parser
{
    private $with_header = false;
    private $with_null = false;

    public function __construct(bool $with_header = false, bool $with_null = false)
    {
        $this->withHeader($with_header);
        $this->withNull($with_null);
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

    public function __get($key)
    {
        switch($key){
            case 'withHeader': return $this->with_header;
            case 'withNull': return $this->with_null;
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
            default: { 
                throw new \InvalidArgumentException(
                    "\n" . __METHOD__ . '.args["key"]: ' . "'{$key}' is not a valid property name\n"
                );
            }
        }
    }

    public function makeDataTree(string $data)
    {
        $tokens = tokenize($data);
        $dataTree = tokensToDataTree($tokens, $this->withHeader, $this->withNull);
        return $dataTree;
    }

}