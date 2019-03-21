<?php

namespace CsvConverter;

interface Parser {
    public function makeDataTree(string $data);
    public static function inputType() : string;
}