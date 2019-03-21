<?php 

namespace CsvConverter;

const CR = '\x0D';
const LF = '\x0A';
const COMMA = '\x2C';
const DQUOTE = '\x22';
const TEXTDATA = '[\x20-\x21\x23-\x2B\x2D-\x7E]';

const CRLF = CR . LF;
const EOL = '(?:' . CRLF . '|' . CR . '|' . LF . ')';
const DOUBLE_DQUOTE = DQUOTE . '{2}';

const NON_ESCAPED = TEXTDATA . '+';
const ESCAPED = DQUOTE . '(?:' . TEXTDATA . '|' .  COMMA . '|' . EOL . '|' . DOUBLE_DQUOTE . ')*' . DQUOTE;
const CSV_PATTERN = '/(' . COMMA . '|' . EOL . '|^)((?:' . ESCAPED . '|' . NON_ESCAPED . ')?)/m';

class CsvParser implements Parser
{
    private $with_header = false; 

    public function __construct(bool $with_header = false)
    {
        $this->withHeader($with_header);
    }

    public static function inputType() : string
    {
        return 'csv';
    }

    public function withHeader(bool $with_header)
    {
        $this->with_header = $with_header;
    }

    private function tokenize($data)
    {
        $data = preg_replace('/[\n\r]+$/', "", $data);
        preg_match_all(CSV_PATTERN, $data, $tokens, PREG_SET_ORDER);

        return $tokens;
    }

    private function tokensToDataTree($tokens, $with_header = false)
    {
        $tree = [];
        array_walk($tokens, function($each) use (&$tree, &$with_header) {
            static $record_no = -1;
            if($each[1] !== ','){
                if($with_header){
                    $with_header = !$with_header;
                } else {
                    $record_no++;
                }
            }
            if($record_no < 0){
                $tree['header'][] = $each[2];
            } else {
                $tree['records'][$record_no][] = $each[2];
            }
        });
        return $tree;
    }

    public function makeDataTree(string $data)
    {
        $tokens = $this->tokenize($data);
        $dataTree = $this->tokensToDataTree($tokens, $this->with_header);
        return $dataTree;
    }

}