<?php
return array(
    'code' => '253',
    'patterns' => array(
        'national' => array(
            'general' => '/^[27]\\d{7}$/',
            'fixed' => '/^2(?:1[2-5]|7[45])\\d{5}$/',
            'mobile' => '/^77[6-8]\\d{5}$/',
            'emergency' => '/^1[78]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{8}$/',
            'emergency' => '/^\\d{2}$/',
        ),
    ),
);
