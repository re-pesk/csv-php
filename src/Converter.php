<?php
declare(strict_types=1);

namespace CsvConverter;

interface Converter {
    public function convert(array $dataTree, $flags = 0);
    public function dataType() : string;
}