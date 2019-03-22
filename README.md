## Converter from csv to json

CSV:

~~~
'field_name_1,"Field
Name 2",field_name_3 
"aaa","b 
,bb","ccc""ddd"
zzz,,""
1,2,
,3,
'
~~~

Data Tree:

~~~
array(2) {
  'header' => array(3) {
    [0] => string(12) "field_name_1"
    [1] => string(12) "Field
Name 2"
    [2] => string(13) "field_name_3 "
  }
  'records' => array(4) {
    [0] => array(3) {
      [0] => string(3) "aaa"
      [1] => string(6) "b 
,bb"
      [2] => string(7) "ccc"ddd"
    }
    [1] => array(3) {
      [0] => string(3) "zzz"
      [1] => string(0) ""
      [2] => string(0) ""
    }
    [2] => array(3) {
      [0] => int(1)
      [1] => int(2)
      [2] => string(0) ""
    }
    [3] => array(3) {
      [0] => string(0) ""
      [1] => int(3)
      [2] => string(0) ""
    }
  }
}
~~~


JSON:

~~~
string(411) "{
    "header": [
        "field_name_1",
        "Field\nName 2",
        "field_name_3 "
    ],
    "records": [
        [
            "aaa",
            "b \n,bb",
            "ccc\"ddd"
        ],
        [
            "zzz",
            "",
            ""
        ],
        [
            1,
            2,
            ""
        ],
        [
            "",
            3,
            ""
        ]
    ]
}"
~~~
