<?php

$csv = 'field_name_1,"Field
Name 2",field_name_3 
"aaa","b 
,bb","ccc""ddd"
zzz,,""
1,2,
,3,
';

$csv = preg_replace('/[\n\r]+$/', "", $csv );

$cr = '\r';
$lf = '\n';
$comma = '\x2c';
$dquote = '\x22';
$allowed_chars = '[\x20-\x21\x23-\x2B\x2D-\x7E]';

$eol = '(?:' . $cr . $lf . '|' . $cr . '|' . $lf . ')';
$double_dquote = $dquote . '{2}';
$non_escaped = $allowed_chars . '+';
$escaped = $dquote . '(?:' . $allowed_chars . '|' .  $comma . '|' . $eol . '|' . $double_dquote . ')*' . $dquote;
$pattern = '/(' . $comma . '|' . $eol . '|^)((?:' . $escaped . '|' . $non_escaped . ')?)/m';
preg_match_all($pattern, $csv, $out, PREG_SET_ORDER);

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

echo '$tree = '; var_export($tree); echo "\n\n";
