<?php
return array(
    'code' => '243',
    'patterns' => array(
        'national' => array(
            'general' => '/^[1-6]\\d{6}|8\\d{6,8}|9\\d{8}$/',
            'fixed' => '/^[1-6]\\d{6}$/',
            'mobile' => '/^8(?:[0-259]\\d{2}|[48])\\d{5}|9[7-9]\\d{7}$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7,9}$/',
            'fixed' => '/^\\d{7}$/',
        ),
    ),
);
