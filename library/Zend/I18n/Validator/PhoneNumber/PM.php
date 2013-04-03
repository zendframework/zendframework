<?php
return array(
    'code' => '508',
    'patterns' => array(
        'national' => array(
            'general' => '/^[45]\\d{5}$/',
            'fixed' => '/^41\\d{4}$/',
            'mobile' => '/^55\\d{4}$/',
            'emergency' => '/^1[578]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6}$/',
            'emergency' => '/^\\d{2}$/',
        ),
    ),
);
