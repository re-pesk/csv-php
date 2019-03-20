<?php

require __DIR__ . '/vendor/autoload.php';

use CsvConverter\DataHolder;

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

$dataHolder = new DataHolder();
$dataHolder->inputCsv($csv, true);

$tree = $dataHolder->dataTree;

echo "Data Tree:\n\n"; var_export($tree); echo "\n\n";

$json = $dataHolder->json;

echo "JSON:\n\n"; var_export($json); echo "\n\n";
