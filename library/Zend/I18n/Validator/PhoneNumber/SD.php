<?php
return array(
    'code' => '249',
    'patterns' => array(
        'national' => array(
            'general' => '/^[19]\\d{8}$/',
            'fixed' => '/^1(?:[125]\\d|8[3567])\\d{6}$/',
            'mobile' => '/^9[012569]\\d{7}$/',
            'emergency' => '/^999$/',
        ),
        'possible' => array(
            'general' => '/^\\d{9}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
