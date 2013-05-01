<?php
return array(
    'code' => '683',
    'patterns' => array(
        'national' => array(
            'general' => '/^[1-5]\\d{3}$/',
            'fixed' => '/^[34]\\d{3}$/',
            'mobile' => '/^[125]\\d{3}$/',
            'emergency' => '/^999$/',
        ),
        'possible' => array(
            'general' => '/^\\d{4}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
