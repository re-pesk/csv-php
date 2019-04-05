<?php 

namespace CsvConverter;

class JsonConverter implements Converter 
{
    public function convert(array $dataTree, $flags = 0){
        return json_encode($dataTree, $flags);
    }

    public function outputType(): string
    {
        return 'json';
    }
}