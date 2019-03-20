<?php

require __DIR__ . '/vendor/autoload.php';

use CsvConverter\CsvConverter;

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

$csvConverter = new CsvConverter();
$csvConverter->inputCsv($csv, true);

$tree = $csvConverter->dataTree;

echo "Data Tree:\n\n"; var_export($tree); echo "\n\n";

$json = $csvConverter->json;

echo "JSON:\n\n"; var_export($json); echo "\n\n";
