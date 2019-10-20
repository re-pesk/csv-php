## Converter from csv to json

CSV:

~~~
'field_name_1,"Field
Name 2",\rfield_name_3\n 
"aaa","b 
,bb","ccc""ddd"
zzz,,""
1,2.2,
,3,
'
~~~

Records: 

Each record consists of 4 parts: all field, head of field (fields or records separator), fields body and tail that if not empty makes field corrupted. 
Each part has string and position. Positions of all field contains position of than field in input string, position of other parts shows position of the part in the field.  

~~~
array(5) {
  [0] => array(3) {
    [0] => array(4) {
      [0] => array(2) {
        [0] => string(12) "field_name_1"
        [1] => int(0)
      }
      [1] => array(2) {
        [0] => string(0) ""
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(12) "field_name_1"
        [1] => int(0)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(12)
      }
    }
    [1] => array(4) {
      [0] => array(2) {
        [0] => string(16) ","Field
Name 2""
        [1] => int(12)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(15) ""Field
Name 2""
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(16)
      }
    }
    [2] => array(4) {
      [0] => array(2) {
        [0] => string(18) ",\rfield_name_3\n "
        [1] => int(28)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(17) "\rfield_name_3\n "
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(18)
      }
    }
  }
  [1] => array(3) {
    [0] => array(4) {
      [0] => array(2) {
        [0] => string(5) "
aaa"
        [1] => int(46)
      }
      [1] => array(2) {
        [0] => string(2) "
"
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(3) "aaa"
        [1] => int(2)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(5)
      }
    }
    [1] => array(4) {
      [0] => array(2) {
        [0] => string(10) ","b 
,bb""
        [1] => int(51)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(9) ""b 
,bb""
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(10)
      }
    }
    [2] => array(4) {
      [0] => array(2) {
        [0] => string(11) ","ccc""ddd""
        [1] => int(61)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(10) ""ccc""ddd""
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(11)
      }
    }
  }
  [2] => array(3) {
    [0] => array(4) {
      [0] => array(2) {
        [0] => string(5) "
zzz"
        [1] => int(72)
      }
      [1] => array(2) {
        [0] => string(2) "
"
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(3) "zzz"
        [1] => int(2)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(5)
      }
    }
    [1] => array(4) {
      [0] => array(2) {
        [0] => string(1) ","
        [1] => int(77)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(0) ""
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(1)
      }
    }
    [2] => array(4) {
      [0] => array(2) {
        [0] => string(3) ","""
        [1] => int(78)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(2) """"
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(3)
      }
    }
  }
  [3] => array(3) {
    [0] => array(4) {
      [0] => array(2) {
        [0] => string(3) "
1"
        [1] => int(81)
      }
      [1] => array(2) {
        [0] => string(2) "
"
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(1) "1"
        [1] => int(2)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(3)
      }
    }
    [1] => array(4) {
      [0] => array(2) {
        [0] => string(4) ",2.2"
        [1] => int(84)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(3) "2.2"
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(4)
      }
    }
    [2] => array(4) {
      [0] => array(2) {
        [0] => string(1) ","
        [1] => int(88)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(0) ""
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(1)
      }
    }
  }
  [4] => array(3) {
    [0] => array(4) {
      [0] => array(2) {
        [0] => string(2) "
"
        [1] => int(89)
      }
      [1] => array(2) {
        [0] => string(2) "
"
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(0) ""
        [1] => int(2)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(2)
      }
    }
    [1] => array(4) {
      [0] => array(2) {
        [0] => string(2) ",3"
        [1] => int(91)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(1) "3"
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(2)
      }
    }
    [2] => array(4) {
      [0] => array(2) {
        [0] => string(1) ","
        [1] => int(93)
      }
      [1] => array(2) {
        [0] => string(1) ","
        [1] => int(0)
      }
      [2] => array(2) {
        [0] => string(0) ""
        [1] => int(1)
      }
      [3] => array(2) {
        [0] => string(0) ""
        [1] => int(1)
      }
    }
  }
}
~~~

Data Tree:

~~~
array(2) {
  'header' => array(3) {
    [0] => string(12) "field_name_1"
    [1] => string(12) "Field
Name 2"
    [2] => string(13) "\rfield_name_3\n "
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
      [1] => double(2.2)
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
        "\rfield_name_3\n "
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
            2.2,
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

### How to run

1. Install *php*, *composer* ir *git*.
2. Clone repository to local storage:
  ~~~
  git clone https://github.com/re-pe/csv-php.git
  ~~~
3. In the folder of cloned repository, install necessary packages:
  ~~~
  cd csv-php
  composer install
  ~~~
4. Run tests:
  ~~~
  ./vendor/bin/kahlan
  ~~~
5. Run demo of usage:
  ~~~
  php index.php
  ~~~
