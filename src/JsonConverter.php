<?php 

namespace CsvConverter;

class JsonConverter implements Converter 
{
    public function convert(DataHolder $dataHolder){
        return json_encode($dataHolder->dataTree, JSON_PRETTY_PRINT);
    }

    public function outputType(): string
    {
        return 'json';
    }
}