<?php

require __DIR__ . '/vendor/autoload.php';

use function CsvConverter\{DataHolder, CsvParser, JsonConverter};

$csv = 'field_name_1,"Field
Name 2",\rfield_name_3\n 
aaa,"b 
,bb","ccc""ddd"
zzz,,""
1,2.2,
,3,
';

$tokens = [];

const CR = '\r'; // '\x0D'
const LF = '\n'; // '\x0A'
const START = '^';
const COMMA = ',';
const DQUOTE = '"';
const CHARS = '[\x20-\xFE]';
const TEXTDATA = '(?:(?![' . DQUOTE . COMMA . '\x7F' . '])' . CHARS . ')';

const CRLF = CR . LF;
const CR_NOT_LF = CR . '(?!' . LF . ')';
const EOL = CRLF . '|' . CR . '|' . LF;
const DOUBLE_DQUOTE = DQUOTE . '{2}';

const NON_ESCAPED = '(?:' . CR_NOT_LF . '|' . TEXTDATA . ')' . '+';

const ESCAPED = DQUOTE . '(?:' . DOUBLE_DQUOTE . '|' . TEXTDATA . '|' .  COMMA . '|' . CR . '|' . LF . ')*' . DQUOTE;
const HEAD = '(?:' . CRLF . '|' . COMMA . '|' . START . ')';
const TAIL = '(?:' . DQUOTE . '|' . CR_NOT_LF . '|[^' . CR . COMMA . '])*';
const BODY = '(?:' . ESCAPED . '|' . NON_ESCAPED . '|)';

const CSV_PATTERN = '/(' . HEAD . ')(' . BODY . ')(' . TAIL . ')/x';

preg_match_all('/(' . HEAD . ')(' . BODY . ')(' . TAIL . ')/', $csv, $tokens, PREG_SET_ORDER);
echo "tokens:\n\n"; var_dump($tokens); echo "\n\n";

echo "\n";
echo "Input:\n\n"; var_export($csv); echo "\n\n";

// CsvParser($with_header = true, $with_null = false)

preg_match_all(CSV_PATTERN, $csv, $tokens, PREG_SET_ORDER);
// echo "tokens:\n\n"; var_dump($tokens); echo "\n\n";


$dataHolder = DataHolder(CsvParser()->withHeader(true)->withNull(true), JsonConverter());
$dataHolder->csv = $csv;

echo "Data Tree:\n\n"; var_dump($dataHolder->dataTree); echo "\n\n";
echo "JSON:\n\n";      var_dump($dataHolder->json); //    echo "\n\n"; $dataHolder->json(JSON_PRETTY_PRINT)

$parser = CsvParser()->withHeader(true);
echo '$parser->withHeader: ', $parser->withHeader ? 'true' : 'false', "\n\n";
echo '$parser->withNull: ', $parser->withNull ? 'true' : 'false', "\n\n";

$parser->withHeader = true;
$parser->withNull = true;

echo "Data Tree:\n\n"; var_dump($parser->makeDataTree($csv)); echo "\n\n";


