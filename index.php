<?php

require __DIR__ . '/vendor/autoload.php';

use function CsvConverter\{DataHolder, CsvParser, JsonConverter};
use CsvConverter\JsonConverter;

$jsonConverter = new JsonConverter();
echo "\$jsonConverter->dataType() === '{$jsonConverter->dataType()}'\n\n";

$csvParser = CsvParser()->parameters([ 'hasHeader' => true, 'convertToNull' => true, 'ignoreInvalidChars' => true ]);

$csvList = [
  0 => "a,b,c\r\nzzz,,\",\r\n\"2,,",
  1 => "a,b,c\r\nzzz,\",\r\n\"\r\n2,,",
  2 => "a,b,c\r\nzzz,,,\",\r\n\"\r\n2,,",
  3 => "a,b,c\r\nzzz,,\",\r\n\"\r\n2,",
  4 => "a,b,c\r\nzzz,, \"\n\"\r\n2,,",
  5 => "a,b,c\r\nzzz,,\"\r\"\n\r\n2,,",
];

array_walk(
    $csvList,
    function ($csv, $i) use ($csvParser) {
        $i++;
        echo "Test #{$i}:\n\n";
        echo "Input:\n\n";
        var_export($csv);
        echo "\n\n";
        try {
            $str = var_export($csvParser->makeDataTree($csv), true);
            echo "Data Tree:\n\n", $str, "\n\n";
        } catch (Exception $e) {
            echo "Error:\n\n", $e->getMessage(), "\n\n";
        }
    }
);

$csvParser = CsvParser()->hasHeader(true)->convertToNull(true);
$csvParser->parameters = [ 'hasHeader' => true ];
$dataHolder = DataHolder($csvParser, JsonConverter());
echo '$dataHolder->hasParser($csvParser->dataType()) === ', $dataHolder->hasParser($csvParser->dataType()), "\n";
// $dataHolder->removeParser('csv');
// echo '$dataHolder->hasParser($csvParser->dataType()) ===', $dataHolder->hasParser($csvParser->dataType());

$dataHolder->csv = 'a,b,"x""y"';
echo '$dataHolder->dataTree === ';
var_export($dataHolder->dataTree);
echo "\n\n";
echo '$dataHolder->json ===', $dataHolder->json, "\n";
echo '$dataHolder->json() ===', $dataHolder->json(), "\n";

echo "\n\n";
echo "Data Tree:\n\n";
var_export($dataHolder->dataTree);
echo "\n\n";
echo "JSON:\n\n";
var_dump($dataHolder->json); //    echo "\n\n"; $dataHolder->json(JSON_PRETTY_PRINT)

$parser = CsvParser()->hasHeader(true);
echo '$parser->hasHeader: ', $parser->hasHeader ? 'true' : 'false', "\n\n";
echo '$parser->convertToNull: ', $parser->convertToNull ? 'true' : 'false', "\n\n";
echo '$parser->ignoreInvalidChars: ', $parser->ignoreInvalidChars ? 'true' : 'false', "\n\n";

$parser->parameters = [ 'hasHeader' => true, 'ignoreInvalidChars' => true ];

$parser->hasHeader = true;
$parser->convertToNull = true;

$csv = 'field_name_1,"Field
Name 2",\rfield_name_3\n 
aaa,"b 
,bb","ccc""ddd"\r
zzz,,""
1,2.2,
,3,
';

echo "\n";
echo "Input:\n\n";
var_export($csv);
echo "\n\n";


echo "-------------------\n";

$csvParser->parameters = [ 'hasHeader' => true, 'ignoreInvalidChars' => true ];
$records = $csvParser->makeRecords($csv);

echo "Records:\n\n"; array_walk(
    $records,
    function ($record) {
        var_dump($record);
    }
);

echo "Data Tree:\n\n";
var_dump($parser->makeDataTree($csv));
echo "\n\n";
