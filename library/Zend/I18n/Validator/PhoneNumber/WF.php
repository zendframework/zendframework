<?php
return array(
    'code' => '681',
    'patterns' => array(
        'national' => array(
            'general' => '/^[5-7]\\d{5}$/',
            'fixed' => '/^(?:50|68|72)\\d{4}$/',
            'mobile' => '/^(?:50|68|72)\\d{4}$/',
            'emergency' => '/^1[578]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6}$/',
            'emergency' => '/^\\d{2}$/',
        ),
    ),
);
