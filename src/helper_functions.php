<?php
declare(strict_types=1);

namespace CsvConverter;
use CsvConverter\DataHolder;
use CsvConverter\CsvParser;
use CsvConverter\JsonConverter;

function DataHolder(?Parser $parser = null, ?Converter $converter = null)
{
    return new DataHolder($parser, $converter);
}

function CsvParser(bool $with_header = false, bool $with_null = false)
{
    return new CsvParser($with_header, $with_null);
}

function JsonConverter()
{
    return new JsonConverter();
}