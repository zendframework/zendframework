<?php
return array(
    'code' => '255',
    'patterns' => array(
        'national' => array(
            'general' => '/^\\d{9}$/',
            'fixed' => '/^2[2-8]\\d{7}$/',
            'mobile' => '/^(?:6[158]|7[1-9])\\d{7}$/',
            'tollfree' => '/^80[08]\\d{6}$/',
            'premium' => '/^90\\d{7}$/',
            'shared' => '/^8(?:40|6[01])\\d{6}$/',
            'voip' => '/^41\\d{7}$/',
            'emergency' => '/^11[12]|999$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7,9}$/',
            'fixed' => '/^\\d{7,9}$/',
            'mobile' => '/^\\d{9}$/',
            'tollfree' => '/^\\d{9}$/',
            'premium' => '/^\\d{9}$/',
            'shared' => '/^\\d{9}$/',
            'voip' => '/^\\d{9}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
