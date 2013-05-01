<?php
return array(
    'code' => '212',
    'patterns' => array(
        'national' => array(
            'general' => '/^[5689]\\d{8}$/',
            'fixed' => '/^528[89]\\d{5}$/',
            'mobile' => '/^6(?:0[0-6]|[14-7]\\d|2[2-46-9]|3[03-8]|8[01]|99)\\d{6}$/',
            'tollfree' => '/^80\\d{7}$/',
            'premium' => '/^89\\d{7}$/',
            'emergency' => '/^1(?:[59]|77)$/',
        ),
        'possible' => array(
            'general' => '/^\\d{9}$/',
            'emergency' => '/^\\d{2,3}$/',
        ),
    ),
);
