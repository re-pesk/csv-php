<?php

use function CsvConverter\JsonConverter;

describe("JsonConverter", function(){
  beforeEach(function() {
    $this->jsonConverter = JsonConverter();
  });
  describe("()", function(){
    it("returns object with class 'JsonConverter'", function() {
      expect($this->jsonConverter)->toBeA('object')->toBeAnInstanceOf('CsvConverter\JsonConverter');
    });
  });
  describe("->outputType()", function(){
    it("returns type of converted value", function() {
      expect($this->jsonConverter->outputType())->toBeA('string')->toEqual('json');
    });
  });
  describe("->convert()", function(){
    it("returns JSON string", function() {
      $dataTree = array (
        [ "field_name_1", "Field\r\nName 2", "\rfield_name_3\n " ],
        [ "aaa", "b \r\n,bb", "ccc\"ddd" ],
        [ 'zzz', NULL, '' ],
        [ 1, 2.2, NULL ],
        [ NULL, 3, NULL ],
      );
      expect($this->jsonConverter->convert($dataTree))
        ->toBeA('string')
        ->toEqual(
          '[["field_name_1","Field\r\nName 2","\\rfield_name_3\\n "],["aaa","b \r\n,bb","ccc\"ddd"],["zzz",null,""],[1,2.2,null],[null,3,null]]'
        );
    });
  });
});

