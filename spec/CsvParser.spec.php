<?php

use function CsvConverter\CsvParser;

describe("CsvParser", function() {
  beforeEach(function() {
    $this->csvParser = CsvParser();
  });
  describe("()", function(){
    it("returns object of class 'CsvParser'", function(){
      expect($this->csvParser)->toBeAn('object')->toBeAnInstanceOf('CsvConverter\CsvParser');
    });
  });
  describe("->__get()", function(){
    it("throws error if parameter's name was wrong", function(){
      expect(function() {
        $this->csvParser->abc;
      })->toThrow(new \InvalidArgumentException("\nCsvConverter\\CsvParser::getBooleanParameter.args['key']: 'abc' is not a valid parameter's name!\n"));
    });
    it("returns parameter", function(){
      expect($this->csvParser->hasHeader)->toBeA('boolean')->toEqual(false);
    });
  });
  describe("->__set()", function(){
    it("throws error if parameter's name was wrong", function(){
      expect(function() {
        $this->csvParser->abc = true;
      })->toThrow(new \InvalidArgumentException("\nCsvConverter\\CsvParser::setBooleanParameter.args['key']: 'abc' is not a valid parameter's name!\n"));
    });
    it("throws error if parameter's value was not boolean", function(){
      expect(function() {
        $this->csvParser->hasHeader = 5;
      })->toThrow(
        new \InvalidArgumentException("\nCsvConverter\\CsvParser::setBooleanParameter.args['value']: 'hasHeader' accepts only values of boolean type!\n")
      );
    });
    it("sets parameter if parameter's name was allowed", function(){
      $this->csvParser->hasHeader = true;
      expect($this->csvParser->hasHeader)->toBeA('boolean')->toEqual(true);
    });
    it("does not change parameters if value is empty array", function(){
      $this->csvParser->hasHeader = true;
      $this->csvParser->parameters = [];
      expect($this->csvParser->hasHeader)->toBeA('boolean')->toEqual(true);
    });
    it("set parameters if value is associative array", function(){
      $this->csvParser->parameters = [ 'hasHeader' => true ];
      expect($this->csvParser->hasHeader)->toBeA('boolean')->toEqual(true);
    });
  });
  describe("->__call()", function(){
    it("throws error if parameter's name was wrong", function(){
      expect(function() {
        $this->csvParser->abc();
      })->toThrow(new \InvalidArgumentException("\nCsvConverter\\CsvParser::setBooleanParameter.args['key']: 'abc' is not a valid parameter's name!\n"));
    });
    it("throws error if parameter's value was not boolean", function(){
      expect(function() {
        $this->csvParser->hasHeader(5);
      })->toThrow(
        new \InvalidArgumentException("\nCsvConverter\\CsvParser::setBooleanParameter.args['value']: 'hasHeader' accepts only values of boolean type!\n")
      );
    });
    it("sets parameter if parameter's name was allowed", function(){
      expect($this->csvParser->hasHeader(true))->toBeA('object')->toBeAnInstanceOf('CsvConverter\CsvParser');
      expect($this->csvParser->hasHeader)->toBeA('boolean')->toEqual(true);
    });
  });
});