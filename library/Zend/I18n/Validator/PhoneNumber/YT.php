<?php
return array(
    'code' => '262',
    'patterns' => array(
        'national' => array(
            'general' => '/^[268]\\d{8}$/',
            'fixed' => '/^2696[0-4]\\d{4}$/',
            'mobile' => '/^639\\d{6}$/',
            'tollfree' => '/^80\\d{7}$/',
            'emergency' => '/^1(?:12|5)$/',
        ),
        'possible' => array(
            'general' => '/^\\d{9}$/',
            'emergency' => '/^\\d{2,3}$/',
        ),
    ),
);
