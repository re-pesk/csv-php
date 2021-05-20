<?php

declare(strict_types=1);

namespace CsvConverter;

use CsvConverter\CsvParser;

class DataHolder
{
    private $parser_list = [];
    private $converter_list = [];
    private $data_tree = null;

    public function __construct(?Parser $parser = null, ?Converter $converter = null)
    {
        if (!is_null($parser)) {
            $this->addParser($parser);
        }

        if (!is_null($converter)) {
            $this->addConverter($converter);
        }
    }

    public function addParser(Parser $parser)
    {
        $this->parser_list[$parser->dataType()] = $parser;
        return $this;
    }

    public function hasParser(string $parserDataType)
    {
        return isset($this->parser_list[$parserDataType]);
    }

    public function removeParser(string $parserDataType)
    {
        if ($this->hasParser($parserDataType)) {
            unset($this->parser_list[$parserDataType]);
        }
        return $this;
    }

    public function addConverter(Converter $converter)
    {
        $this->converter_list[$converter->dataType()] = $converter;
        return $this;
    }

    public function hasConverter(string $converterDataType)
    {
        return isset($this->converter_list[$converterDataType]);
    }

    public function removeConverter(string $converterDataType)
    {
        if ($this->hasConverter($converterDataType)) {
            unset($this->converter_list[$converterDataType]);
        }
        return $this;
    }

    public function __get($key)
    {
        if ($key == "dataTree") {
            return $this->data_tree;
        } elseif (isset($this->converter_list[$key])) {
            return $this->converter_list[$key]->convert($this->data_tree);
        }
        throw new \InvalidArgumentException(
            "\n" . __METHOD__ . ".args['key']: converter with type '{$key}' does not exist.\n"
        );
    }

    public function __set($key, $value)
    {
        if (isset($this->parser_list[$key])) {
            $this->data_tree = $this->parser_list[$key]->makeDataTree($value);
            return;
        }
        throw new \InvalidArgumentException(
            "\n" . __METHOD__ . ".args['key']: parser with type '{$key}' does not exist.\n"
        );
    }

    public function __call($key, $args)
    {
        if (isset($this->converter_list[$key])) {
            return $this->converter_list[$key]
            ->convert($this->data_tree, (count($args) < 1 || is_null($args[0])) ? 0 : $args[0]);
        }
        throw new \InvalidArgumentException(
            "\n" . __METHOD__ . ".args['key']: converter with type '{$key}' does not exist.\n"
        );
    }
}
