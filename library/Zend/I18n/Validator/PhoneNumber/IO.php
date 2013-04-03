<?php
return array(
    'code' => '246',
    'patterns' => array(
        'national' => array(
            'general' => '/^3\\d{6}$/',
            'fixed' => '/^37\\d{5}$/',
            'mobile' => '/^38\\d{5}$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7}$/',
        ),
    ),
);
