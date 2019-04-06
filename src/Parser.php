<?php
declare(strict_types=1);

namespace CsvConverter;

interface Parser {
    public function makeDataTree(string $data);
    public static function inputType() : string;
}