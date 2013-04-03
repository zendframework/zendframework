<?php
return array(
    'code' => '211',
    'patterns' => array(
        'national' => array(
            'general' => '/^[19]\\d{8}$/',
            'fixed' => '/^18\\d{7}$/',
            'mobile' => '/^(?:12|9[1257])\\d{7}$/',
        ),
        'possible' => array(
            'general' => '/^\\d{9}$/',
        ),
    ),
);
