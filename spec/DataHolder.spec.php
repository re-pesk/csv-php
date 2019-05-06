<?php

use function CsvConverter\{DataHolder, CsvParser, JsonConverter};

describe("DataHolder", function(){
  beforeEach(function() {
    $this->dataHolder = DataHolder();
  });
  describe("()", function(){
    it("returns object with class 'DataHolder'", function() {
      expect($this->dataHolder)->toBeA('object')->toBeAnInstanceOf('CsvConverter\DataHolder');
    });
  });
  describe("->addParser", function(){
    it("adds csv parser to list of available parsers", function() {
      $parser = CsvParser();
      $this->dataHolder->addParser($parser);
      expect($this->dataHolder->hasParser($parser->dataType()))
        ->toBeA('boolean')
        ->toEqual(true);
    });
  });
  describe("->removeParser", function(){
    it("remove csv parser from list of available parsers", function() {
      $parser = CsvParser();
      $this->dataHolder->addParser($parser);
      expect($this->dataHolder->hasParser($parser->dataType()))
        ->toBeA('boolean')
        ->toEqual(true);
      $this->dataHolder->removeParser($parser->dataType());
      expect($this->dataHolder->hasParser($parser->dataType()))
        ->toBeA('boolean')
        ->toEqual(false);
    });
  });
});
