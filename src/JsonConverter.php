<?php 
declare(strict_types=1);

namespace CsvConverter;

class JsonConverter implements Converter 
{
    public function convert(array $dataTree, $flags = 0): string
    {
        return json_encode($dataTree, $flags);
    }

    public function outputType(): string
    {
        return 'json';
    }
}