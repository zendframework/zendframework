<?php
return array(
    'code' => '247',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-467]\d{3}$/',
            'fixed' => '/^(?:[267]\d|3[0-5]|4[4-69])\d{2}$/',
            'emergency' => '/^911$/',
        ),
        'possible' => array(
            'general' => '/^\d{4}$/',
            'fixed' => '/^\d{4}$/',
            'emergency' => '/^\d{3}$/',
        ),
    ),
);
