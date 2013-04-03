<?php
return array(
    'code' => '590',
    'patterns' => array(
        'national' => array(
            'general' => '/^[56]\\d{8}$/',
            'fixed' => '/^590(?:2[7-9]|5[12]|87)\\d{4}$/',
            'mobile' => '/^690(?:10|2[27]|66|77|8[78])\\d{4}$/',
            'emergency' => '/^18$/',
        ),
        'possible' => array(
            'general' => '/^\\d{9}$/',
            'emergency' => '/^\\d{2}$/',
        ),
    ),
);
