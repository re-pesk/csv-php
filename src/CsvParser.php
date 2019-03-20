<?php 

namespace CsvConverter;

class CsvParser 
{

    const CR = '\x0D';
    const LF = '\x0A';
    const COMMA = '\x2C';
    const DQUOTE = '\x22';
    const TEXTDATA = '[\x20-\x21\x23-\x2B\x2D-\x7E]';
    
    const CRLF = self::CR . self::LF;
    const EOL = '(?:' . self::CRLF . '|' . self::CR . '|' . self::LF . ')';
    const DOUBLE_DQUOTE = self::DQUOTE . '{2}';
    
    const NON_ESCAPED = self::TEXTDATA . '+';
    const ESCAPED = self::DQUOTE . '(?:' . self::TEXTDATA . '|' .  self::COMMA . '|' . self::EOL . '|' . self::DOUBLE_DQUOTE . ')*' . self::DQUOTE;
    const CSV_PATTERN = '/(' . self::COMMA . '|' . self::EOL . '|^)((?:' . self::ESCAPED . '|' . self::NON_ESCAPED . ')?)/m';

    private function tokenize($data)
    {
        $data = preg_replace('/[\n\r]+$/', "", $data);
        preg_match_all(self::CSV_PATTERN, $data, $tokens, PREG_SET_ORDER);

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

    public function makeDataTree($data, $with_header = false)
    {
        $tokens = $this->tokenize($data);
        $dataTree = $this->tokensToDataTree($tokens, $with_header);
        return $dataTree;
    }

}