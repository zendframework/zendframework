<?php
return array(
    'code' => '500',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-7]\\d{4}$/',
            'fixed' => '/^[2-47]\\d{4}$/',
            'mobile' => '/^[56]\\d{4}$/',
            'shortcode' => '/^1\\d{2}$/',
            'emergency' => '/^999$/',
        ),
        'possible' => array(
            'general' => '/^\\d{5}$/',
            'shortcode' => '/^\\d{3}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
