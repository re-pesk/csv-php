php-csv

CSV:

~~~
field_name_1,"Field
Name 2",field_name_3 
"aaa","b 
,bb","ccc""ddd"
zzz,,""
1,2,
,3,

~~~

Output:

~~~
[
    'header' => [
        0 => 'field_name_1',
        1 => '"Field
Name 2"',
        2 => 'field_name_3 ',
    ],
    'records' => [
        0 => [
            0 => '"aaa"',
            1 => '"b 
,bb"',
            2 => '"ccc""ddd"',
        ],
        1 => [
            0 => 'zzz',
            1 => '',
            2 => '""',
        ],
        2 => [
            0 => '1',
            1 => '2',
            2 => '',
        ],
        3 => 
        [
            0 => '',
            1 => '3',
            2 => '',
        ],
    ],
]
~~~


JSON:

~~~
{
    "header": [
        "field_name_1",
        "\\"Field\\r\\nName 2\\"",
        "field_name_3 "
    ],
    "records": [
        [
            "\\"aaa\\"",
            "\\"b \\r\\n,bb\\"",
            "\\"ccc\\"\\"ddd\\""
        ],
        [
            "zzz",
            "",
            "\\"\\""
        ],
        [
            "1",
            "2",
            ""
        ],
        [
            "",
            "3",
            ""
        ]
    ]
}
~~~
