<?php
return array(
    'code' => '291',
    'patterns' => array(
        'national' => array(
            'general' => '/^[178]\\d{6}$/',
            'fixed' => '/^1(?:1[12568]|20|40|55|6[146])\\d{4}|8\\d{6}$/',
            'mobile' => '/^17[1-3]\\d{4}|7\\d{6}$/',
        ),
        'possible' => array(
            'general' => '/^\\d{6,7}$/',
            'mobile' => '/^\\d{7}$/',
        ),
    ),
);
