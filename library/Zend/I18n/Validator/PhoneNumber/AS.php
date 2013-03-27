<?php
return array(
    'code' => '1',
    'patterns' => array(
        'national' => array(
            'general' => '/^[5689]\d{9}$/',
            'fixed' => '/^6846(?:22|33|44|55|77|88|9[19])\d{4}$/',
            'mobile' => '/^684(?:733|258)\d{4}$/',
            'tollfree' => '/^8(?:00|55|66|77|88)[2-9]\d{6}$/',
            'premium' => '/^900[2-9]\d{6}$/',
            'personal' => '/^5(?:00|33|44)[2-9]\d{6}$/',
            'emergency' => '/^911$/',
        ),
        'possible' => array(
            'general' => '/^\d{7}(?:\d{3})?$/',
            'mobile' => '/^\d{10}$/',
            'tollfree' => '/^\d{10}$/',
            'premium' => '/^\d{10}$/',
            'personal' => '/^\d{10}$/',
            'emergency' => '/^\d{3}$/',
        ),
    ),
);
