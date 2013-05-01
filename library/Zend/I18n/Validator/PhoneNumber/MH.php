<?php
return array(
    'code' => '692',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-6]\\d{6}$/',
            'fixed' => '/^(?:247|528|625)\\d{4}$/',
            'mobile' => '/^(?:235|329|45[56]|545)\\d{4}$/',
            'voip' => '/^635\\d{4}$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7}$/',
        ),
    ),
);
