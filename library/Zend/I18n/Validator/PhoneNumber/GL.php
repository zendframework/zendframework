<?php
return array(
    'code' => '299',
    'patterns' => array(
        'national' => array(
            'general' => '/^[1-689]\\d{5}$/',
            'fixed' => '/^(?:19|3[1-6]|6[14689]|8[14-79]|9\\d)\\d{4}$/',
            'mobile' => '/^[245][2-9]\\d{4}$/',
            'tollfree' => '/^80\\d{4}$/',
            'voip' => '/^3[89]\\d{4}$/',
            'emergency' => '/^112$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
