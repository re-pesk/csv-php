<?php

use function CsvConverter\{DataHolder, CsvParser, JsonConverter};
use CsvConverter\DataHolder;

describe("DataHolder", function(){
  beforeEach(function() {
    $this->dataHolder = DataHolder();
  });
  describe("()", function(){
    describe("without arguments", function(){
      it("returns object of class 'DataHolder'", function() {
        expect($this->dataHolder)->toBeA('object')->toBeAnInstanceOf('CsvConverter\DataHolder');
      });
    });
    describe("with both arguments", function(){
      it("returns object of class 'DataHolder' having parser and converter", function() {
        $dataHolder = DataHolder(CsvParser(), JsonConverter());
        expect($dataHolder)->toBeA('object')->toBeAnInstanceOf('CsvConverter\DataHolder');
        expect($dataHolder->hasParser('csv'))->toBeA('boolean')->toEqual(true);
        expect($dataHolder->hasConverter('json'))->toBeA('boolean')->toEqual(true);
      });
    });
  });
  describe("->addParser()", function(){
    it("adds csv parser to list of available parsers", function() {
      $parser = CsvParser();
      $this->dataHolder->addParser($parser);
      expect($this->dataHolder->hasParser($parser->dataType()))
        ->toBeA('boolean')
        ->toEqual(true);
    });
  });
  describe("->removeParser()", function(){
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
  describe("->addConverter()", function(){
    it("adds csv parser to list of available parsers", function() {
      $converter = JsonConverter();
      $this->dataHolder->addConverter($converter);
      expect($this->dataHolder->hasConverter($converter->dataType()))
        ->toBeA('boolean')
        ->toEqual(true);
    });
  });
  describe("->removeConverter()", function(){
    it("remove csv parser from list of available parsers", function() {
      $converter = JsonConverter();
      $this->dataHolder->addConverter($converter);
      expect($this->dataHolder->hasConverter($converter->dataType()))
        ->toBeA('boolean')
        ->toEqual(true);
      $this->dataHolder->removeConverter($converter->dataType());
      expect($this->dataHolder->hasConverter($converter->dataType()))
        ->toBeA('boolean')
        ->toEqual(false);
    });
  });
  describe("->__set()", function(){
    it("throws error if type of parser was wrong", function(){
      expect(function () {
        $this->dataHolder->abc = "a,b,c";
      })->toThrow(new \InvalidArgumentException("\nCsvConverter\\DataHolder::__set.args['key']: parser with type 'abc' does not exist.\n"));
    });
    it("parses input string and stores created data tree", function(){
      $this->dataHolder->addParser(CsvParser());
      $this->dataHolder->csv = "a,b,c";
      expect($this->dataHolder->dataTree)->toBeA('array')->toEqual([["a", "b", "c"]]);
    });
  });
  describe("->__get()", function(){
    it("throws error if type of converter was wrong", function(){
      expect(function() {
        $this->dataHolder->abc;
      })->toThrow(new \InvalidArgumentException("\nCsvConverter\\DataHolder::__get.args['key']: converter with type 'abc' does not exist.\n"));
    });
    it("returns data tree if name of accessed property is dataTree", function(){
      $this->dataHolder->addParser(CsvParser());
      $this->dataHolder->csv = 'a,b,c';
      expect($this->dataHolder->dataTree)->toBeA('array')->toEqual([["a","b","c"]]);
    });
    it("returns data tree converted to string", function(){
      $this->dataHolder->addParser(CsvParser());
      $this->dataHolder->csv = 'a,b,c';
      $this->dataHolder->addConverter(JsonConverter());
      expect($this->dataHolder->json)->toBeA('string')->toEqual('[["a","b","c"]]');
    });
  });
  describe("->__call()", function(){
    it("throws error if type of converter was wrong", function(){
      expect(function() {
        return $this->dataHolder->abc('abc');
      })->toThrow(new \InvalidArgumentException("\nCsvConverter\\DataHolder::__call.args['key']: converter with type 'abc' does not exist.\n"));
    });
    it("returns data tree converted to string", function(){
      $this->dataHolder->addParser(CsvParser());
      $this->dataHolder->csv = 'a,b,"x""y"';
      $this->dataHolder->addConverter(JsonConverter());
      expect($this->dataHolder->json())->toBeA('string')->toEqual('[["a","b","x\"y"]]');
      expect($this->dataHolder->json(null))->toBeA('string')->toEqual('[["a","b","x\"y"]]');
    });
    it("returns data tree converted to string according to flags given as argument", function(){
      $this->dataHolder->addParser(CsvParser());
      $this->dataHolder->csv = 'a,b,"x""y"';
      $this->dataHolder->addConverter(JsonConverter());
      expect($this->dataHolder->json(JSON_HEX_QUOT))->toBeA('string')->toEqual('[["a","b","x\u0022y"]]');
    });
  });
});
