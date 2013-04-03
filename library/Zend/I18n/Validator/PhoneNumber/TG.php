<?php
return array(
    'code' => '228',
    'patterns' => array(
        'national' => array(
            'general' => '/^[29]\\d{7}$/',
            'fixed' => '/^2(?:2[2-7]|3[23]|44|55|66|77)\\d{5}$/',
            'mobile' => '/^9[0-289]\\d{6}$/',
            'emergency' => '/^1(?:01|1[78]|7[17])$/',
        ),
        'possible' => array(
            'general' => '/^\\d{8}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
