<?php

namespace CsvConverter;

interface Converter {
    public function convert(array $dataTree, $flags = 0);
    public function outputType() : string;
}