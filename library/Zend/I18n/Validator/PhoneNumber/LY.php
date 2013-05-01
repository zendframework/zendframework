<?php
return array(
    'code' => '218',
    'patterns' => array(
        'national' => array(
            'general' => '/^[25679]\\d{8}$/',
            'fixed' => '/^(?:2[1345]|5[1347]|6[123479]|71)\\d{7}$/',
            'mobile' => '/^9[1-6]\\d{7}$/',
            'emergency' => '/^19[013]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7,9}$/',
            'mobile' => '/^\\d{9}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
