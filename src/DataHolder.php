<?php
declare(strict_types=1);

namespace CsvConverter;

use CsvConverter\CsvParser;

class DataHolder {
    private $parser_list = [];
    private $converter_list = [];
    private $data_tree = null;

    public function __construct(?Parser $parser = null, ?Converter $converter = null)
    {
        if(!is_null($parser)){
            $this->addParser($parser);
        }

        if(!is_null($converter)){
            $this->addConverter($converter);
        }
    }

    public function addParser(Parser $parser) 
    {
        $this->parser_list[$parser->inputType()] = $parser;
        return $this;
    }

    public function hasParser(string $parserOuputType) 
    {
        return isset($this->parser_list[$parserOuputType]);
    }

    public function removeParser(string $parserOuputType)
    {
        if ($this->hasConverter($parserOuputType)){
            unset($this->parser_list[$parserOuputType]);
        }
        return $this;
    }

    public function addConverter(Converter $converter)
    {
        $this->converter_list[$converter->outputType()] = $converter;
        return $this;
    }

    public function hasConverter(string $converterOuputType) 
    {
        return isset($this->converter_list[$converterOuputType]);
    }

    public function removeConverter(string $converterOuputType)
    {
        if ($this->hasConverter($converterOuputType)){
            unset($this->converter_list[$converterOuputType]);
        }
        return $this;
    }

    public function __get($key)
    {
        if($key == "dataTree") {
            return $this->data_tree;
        } elseif(isset($this->converter_list[$key])){
            return $this->converter_list[$key]->convert($this->data_tree);
        } else {
            throw new \InvalidArgumentException(
                "\n" . __METHOD__ . '.args["key"]: ' . "'{$key}' is not a valid preperty name\n"
            );
        }
        return null;
    }

    public function __set($key, $value) 
    {
        if(isset($this->parser_list[$key])){
            $this->data_tree = $this->parser_list[$key]->makeDataTree($value);
        } else {
            throw new \InvalidArgumentException(
                "\n" . __METHOD__ . '.args["key"]: ' . "'{$key}' is not a valid preperty name\n"
            );
        }
    }

    public function __call($key, $args) {
        if(isset($this->converter_list[$key])){
            return $this->converter_list[$key]->convert($this->data_tree, ...$args);
        } else {
            throw new \InvalidArgumentException(
                "\n" . __METHOD__ . '.args["key"]: ' . "'{$key}' is not a valid preperty name\n"
            );
        }
        return null;
    }

}