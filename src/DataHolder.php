<?php

namespace CsvConverter;

use CsvConverter\CsvParser;

class DataHolder {
    private $data_tree = null;
    private $converter = null;

    public function __construct()
    {
        $this->converter = new CsvParser();
    }

    public function __get($key)
    {
        if($key == "csv"){
            return $this->tree2csv();
        } elseif($key == "dataTree") {
            return $this->data_tree;
        } elseif($key == "json") {
            return $this->tree2json();
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