<?php

require __DIR__ . '/vendor/autoload.php';

use function CsvConverter\{DataHolder, CsvParser, JsonConverter};

$csv = 'field_name_1,"Field
Name 2",field_name_3 
"aaa","b 
,bb","ccc""ddd"
zzz,,""
1,2.2,
,3,
';

echo "\n";
echo "Input:\n\n"; var_export($csv); echo "\n\n";

// CsvParser($with_header = true, $with_null = false)

$dataHolder = DataHolder(CsvParser()->withHeader(true), JsonConverter());
$dataHolder->csv = $csv;

echo "Data Tree:\n\n"; var_dump($dataHolder->dataTree); echo "\n\n";
echo "JSON:\n\n";      var_dump($dataHolder->json);     echo "\n\n";

$parser = CsvParser()->withHeader(true);
echo '$parser->withHeader: ', $parser->withHeader ? 'true' : 'false', "\n\n";
echo '$parser->withNull: ', $parser->withNull ? 'true' : 'false', "\n\n";

$parser->withHeader = false;
$parser->withNull = true;

echo "Data Tree:\n\n"; var_dump($parser->makeDataTree($csv)); echo "\n\n";


