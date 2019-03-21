<?php

require __DIR__ . '/vendor/autoload.php';

use function CsvConverter\{DataHolder, CsvParser, JsonConverter};

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

$dataHolder = DataHolder(CsvParser(true), JsonConverter());
$dataHolder->csv = $csv;

echo "Data Tree:\n\n"; var_export($dataHolder->dataTree); echo "\n\n";
echo "JSON:\n\n";      var_export($dataHolder->json);     echo "\n\n";
