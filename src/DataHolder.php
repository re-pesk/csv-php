<?php

namespace CsvConverter;

use CsvConverter\CsvParser;

class DataHolder {
    private $parser_list = [];
    private $converter_list = [];
    private $data_tree = null;
    private $converter = null;

    public function __construct(?Parser $parser = null, ?Converter $converter = null)
    {
        if(!is_null($parser)){
            $this->addParser($parser);
        }

        if(!is_null($converter)){
            $this->addConverter($converter);
        }
    }

    public function addParser(Parser $parser) : void
    {
        $this->parser_list[$parser->inputType()] = $parser;
    }

    public function removeParser(string $name) : void
    {
        unset($this->parser_list[$name]);
    }

    public function addConverter(Converter $converter) : void
    {
        $this->converter_list[$converter->outputType()] = $converter;
    }

    public function removeConverter(string $name) : void
    {
        unset($this->converter_list[$name]);
    }

    public function __get($key)
    {
        if($key == "dataTree") {
            return $this->data_tree;
        } elseif(isset($this->converter_list[$key])){
            return $this->converter_list[$key]->convert($this);
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    '"%s" is not a valid preperty name',
                    $key
                )
            );
        }
        return null;
    }

    public function __set($key, $value) 
    {
        if(isset($this->parser_list[$key])){
            $this->data_tree = $this->parser_list[$key]->makeDataTree($value);
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    '"%s" is not a valid preperty name',
                    $key
                )
            );
        }
    }
  
    public function inputCsv($data, $with_header = false)
    {
        
        $this->data_tree = $this->converter->makeDataTree($data, $with_header);
    }

    public function inputJson($data)
    {
        $this->data_tree = json_decode($data);
    }

    private function tree2json(){
        return json_encode($this->data_tree, JSON_PRETTY_PRINT);
    }
  
    private function tree2csv(){
        return $this->data_tree;
    }

}