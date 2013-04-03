<?php
return array(
    'code' => '596',
    'patterns' => array(
        'national' => array(
            'general' => '/^[56]\\d{8}$/',
            'fixed' => '/^596(?:0[2-5]|[12]0|3[05-9]|4[024-8]|[5-7]\\d|89|9[4-8])\\d{4}$/',
            'mobile' => '/^696(?:[0-479]\\d|5[01]|8[0-689])\\d{4}$/',
            'emergency' => '/^1(?:12|[578])$/',
        ),
        'possible' => array(
            'general' => '/^\\d{9}$/',
            'emergency' => '/^\\d{2,3}$/',
        ),
    ),
);
