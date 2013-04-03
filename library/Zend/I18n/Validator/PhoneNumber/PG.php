<?php
return array(
    'code' => '675',
    'patterns' => array(
        'national' => array(
            'general' => '/^[1-9]\\d{6,7}$/',
            'fixed' => '/^(?:3\\d{2}|4[257]\\d|5[34]\\d|6(?:29|4[1-9])|85[02-46-9]|9[78]\\d)\\d{4}$/',
            'mobile' => '/^(?:68|7[0-36]\\d)\\d{5}$/',
            'tollfree' => '/^180\\d{4}$/',
            'voip' => '/^275\\d{4}$/',
            'emergency' => '/^000$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7,8}$/',
            'fixed' => '/^\\d{7}$/',
            'mobile' => '/^\\d{7,8}$/',
            'tollfree' => '/^\\d{7}$/',
            'voip' => '/^\\d{7}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
