<?php
return array(
    'code' => '239',
    'patterns' => array(
        'national' => array(
            'general' => '/^[29]\\d{6}$/',
            'fixed' => '/^22\\d{5}$/',
            'mobile' => '/^9[89]\\d{5}$/',
            'emergency' => '/^112$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
