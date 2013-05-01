<?php
return array(
    'code' => '856',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-8]\\d{7,9}$/',
            'fixed' => '/^(?:2[13]|[35-7][14]|41|8[1468])\\d{6}$/',
            'mobile' => '/^20(?:2[2389]|5[4-689]|7[6-8]|9[57-9])\\d{6}$/',
            'emergency' => '/^19[015]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6,10}$/',
            'fixed' => '/^\\d{6,8}$/',
            'mobile' => '/^\\d{10}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
