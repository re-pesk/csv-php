<?php 

namespace CsvConverter;

const CR = '\r';
const LF = '\n';
const COMMA = ',';
const DQUOTE = '"';
const TEXTDATA = '[ -!]|[#-+]|[--~]';

const CRLF = CR . LF;
const EOL = CRLF . '|' . CR . '|' . LF;
const DOUBLE_DQUOTE = DQUOTE . '{2}';

const NON_ESCAPED = TEXTDATA . '+';
const ESCAPED = DQUOTE . '(?:' . TEXTDATA . '|' .  COMMA . '|' . CR . '|' . LF . '|' . DOUBLE_DQUOTE . ')*' . DQUOTE;
const CSV_PATTERN = '/(' . COMMA . '|' . EOL . '|^)((?:' . ESCAPED . '|' . NON_ESCAPED . ')?)/m';

const SIGN = '[+-]?';
const DIGITS = '[0-9]+';
const INT_PATTERN = SIGN . DIGITS;

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

    private function tokenize($data)
    {
        $data = preg_replace('/[\n\r]+$/', "", $data);
        preg_match_all(CSV_PATTERN, $data, $tokens, PREG_SET_ORDER);

        return $tokens;
    }

    private function tokensToDataTree($tokens)
    {
        $tree = [];
        $with_header = $this->with_header;
        $with_null = $this->with_null;
        array_walk($tokens, function($each) use (&$tree, &$with_header, $with_null) {
            static $record_no = -1;
            if($each[1] !== ','){
                if($with_header){
                    $with_header = !$with_header;
                } else {
                    $record_no++;
                }
            }

            $value = null;

            if(is_numeric($each[2])){
                if(preg_match('/^' . INT_PATTERN . '$/', $each[2])){
                    $value = intval($each[2]);
                } else {
                    $value = floatval($each[2]);
                }
            } elseif(is_string($each[2])){
                if(!$with_null || !preg_match('/^$/', $each[2])){
                    $value = preg_replace(['/^"/', '/"$/', '/""/'], ['', '', '"'], $each[2]);
                }
            } else {
                $value = $each[2];
            }
            if($record_no < 0){
                $tree['header'][] = $value;
            } else {
                $tree['records'][$record_no][] = $value;
            }
        });
        return $tree;
    }

    public function makeDataTree(string $data)
    {
        $tokens = $this->tokenize($data);
        $dataTree = $this->tokensToDataTree($tokens);
        return $dataTree;
    }

}