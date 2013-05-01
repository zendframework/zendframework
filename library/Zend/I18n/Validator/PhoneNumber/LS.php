<?php
return array(
    'code' => '266',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2568]\\d{7}$/',
            'fixed' => '/^2\\d{7}$/',
            'mobile' => '/^[56]\\d{7}$/',
            'tollfree' => '/^800[256]\\d{4}$/',
            'emergency' => '/^11[257]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{8}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
