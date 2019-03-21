<?php

namespace CsvConverter;

interface Converter {
    public function convert(DataHolder $dataHolder);
    public function outputType() : string;
}