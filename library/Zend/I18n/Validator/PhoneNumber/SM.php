<?php
return array(
    'code' => '378',
    'patterns' => array(
        'national' => array(
            'general' => '/^[05-7]\\d{7,9}$/',
            'fixed' => '/^0549(?:8[0157-9]|9\\d)\\d{4}$/',
            'mobile' => '/^6[16]\\d{6}$/',
            'premium' => '/^7[178]\\d{6}$/',
            'voip' => '/^5[158]\\d{6}$/',
            'emergency' => '/^11[358]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6,10}$/',
            'mobile' => '/^\\d{8}$/',
            'premium' => '/^\\d{8}$/',
            'voip' => '/^\\d{8}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
