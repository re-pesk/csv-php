<?php

$csv = 'field_name_1,"Field
Name 2",field_name_3 
"aaa","b 
,bb","ccc""ddd"
zzz,,""
1,2,
,3,
';

echo "\n";
echo "Input:\n\n"; var_export($csv); echo "\n\n\n";

$csv = preg_replace('/[\n\r]+$/', "", $csv);

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

preg_match_all(CSV_PATTERN, $csv, $out, PREG_SET_ORDER);

//echo '$out = '; var_export($out); echo "\n\n";

function createTree($out, $with_header = false)
{
    $tree = [];
    array_walk($out, function($each) use (&$tree, &$with_header) {
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

$tree = createTree($out, true);

echo "Output:\n\n"; var_export($tree); echo "\n\n";
