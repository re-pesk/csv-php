<?php

require __DIR__ . '/vendor/autoload.php';

use function CsvConverter\{DataHolder, CsvParser, JsonConverter};

$csvParser = CsvParser()->withHeader(true)->withNull(true)->autoCheck(true);

$csvList = [
  1 => "a,b,c\r\nzzz,\",\r\n\"\r\n2,,",
  0 => "a,b,c\r\nzzz,,\",\r\n\"2,,",
  2 => "a,b,c\r\nzzz,,,\",\r\n\"\r\n2,,",
  3 => "a,b,c\r\nzzz,,\",\r\n\"\r\n2,",
  4 => "a,b,c\r\nzzz,, \"\n\"\r\n2,,",
  5 => "a,b,c\r\nzzz,,\"\r\"\n\r\n2,,",
];

array_walk($csvList, function($csv, $i) use ($csvParser) {
  $i++;
  echo "Test #{$i}:\n\n";
  echo "Input:\n\n"; var_export($csv); echo "\n\n";
  try {
    $str = var_export($csvParser->makeDataTree($csv), true);
    echo "Data Tree:\n\n", $str, "\n\n";
  } catch (Exception $e) {
    echo "Error:\n\n", $e->getMessage(), "\n\n";
  }
});

$csv = 'field_name_1,"Field
Name 2",\rfield_name_3\n 
aaa,"b 
,bb","ccc""ddd"
zzz,,""
1,2.2,
,3,
';

echo "\n";
echo "Input:\n\n"; var_export($csv); echo "\n\n";

$csvParser = CsvParser()->withHeader(true)->withNull(true)->autoCheck(true);
$dataHolder = DataHolder(CsvParser()->withHeader(true)->withNull(true)->autoCheck(true), JsonConverter());
$dataHolder->csv = $csv;

echo "Records:\n\n"; array_walk($csvParser->getRecords($csv), function($record){
  var_dump($record);
}); echo "\n\n";
echo "Data Tree:\n\n"; var_dump($dataHolder->dataTree); echo "\n\n";
echo "JSON:\n\n";      var_dump($dataHolder->json); //    echo "\n\n"; $dataHolder->json(JSON_PRETTY_PRINT)

$parser = CsvParser()->withHeader(true);
echo '$parser->withHeader: ', $parser->withHeader ? 'true' : 'false', "\n\n";
echo '$parser->withNull: ', $parser->withNull ? 'true' : 'false', "\n\n";

$parser->withHeader = true;
$parser->withNull = true;

echo "Data Tree:\n\n"; var_dump($parser->makeDataTree($csv)); echo "\n\n";


