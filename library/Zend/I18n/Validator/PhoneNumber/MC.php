<?php
return array(
    'code' => '377',
    'patterns' => array(
        'national' => array(
            'general' => '/^[4689]\\d{7,8}$/',
            'fixed' => '/^9[2-47-9]\\d{6}$/',
            'mobile' => '/^6\\d{8}|4\\d{7}$/',
            'tollfree' => '/^(?:8\\d|90)\\d{6}$/',
            'emergency' => '/^1(?:12|[578])$/',
        ),
        'possible' => array(
            'general' => '/^\\d{8,9}$/',
            'fixed' => '/^\\d{8}$/',
            'tollfree' => '/^\\d{8}$/',
            'emergency' => '/^\\d{2,3}$/',
        ),
    ),
);
