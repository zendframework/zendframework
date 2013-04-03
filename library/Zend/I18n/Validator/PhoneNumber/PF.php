<?php
return array(
    'code' => '689',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-9]\\d{5}$/',
            'fixed' => '/^(?:4(?:[02-9]\\d|1[02-9])|[5689]\\d{2})\\d{3}$/',
            'mobile' => '/^(?:[27]\\d{2}|3[0-79]\\d|411)\\d{3}$/',
            'emergency' => '/^1[578]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6}$/',
            'emergency' => '/^\\d{2}$/',
        ),
    ),
);
