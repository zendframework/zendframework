<?php
return array(
    'code' => '674',
    'patterns' => array(
        'national' => array(
            'general' => '/^[458]\\d{6}$/',
            'fixed' => '/^(?:444|888)\\d{4}$/',
            'mobile' => '/^55[5-9]\\d{4}$/',
            'shortcode' => '/^1(?:23|92)$/',
            'emergency' => '/^11[0-2]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7}$/',
            'shortcode' => '/^\\d{3}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
