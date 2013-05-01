<?php
return array(
    'code' => '216',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-57-9]\\d{7}$/',
            'fixed' => '/^(?:3[012]|7\\d)\\d{6}$/',
            'mobile' => '/^(?:[259]\\d|4[0-2])\\d{6}$/',
            'premium' => '/^8[0128]\\d{6}$/',
            'emergency' => '/^19[078]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{8}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
