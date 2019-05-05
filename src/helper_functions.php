<?php
declare(strict_types=1);

namespace CsvConverter;
use CsvConverter\{DataHolder, CsvParser, JsonConverter};

function CsvParser(array $parameters = [])
{
    return new CsvParser($parameters);
}

function DataHolder(?Parser $parser = null, ?Converter $converter = null)
{
    return new DataHolder($parser, $converter);
}

function JsonConverter()
{
    return new JsonConverter();
}