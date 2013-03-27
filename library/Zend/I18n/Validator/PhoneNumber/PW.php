<?php
return array(
    'code' => '680',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-8]\\d{6}$/',
            'fixed' => '/^2552255|(?:277|345|488|5(?:35|44|87)|6(?:22|54|79)|7(?:33|47)|8(?:24|55|76))\\d{4}$/',
            'mobile' => '/^(?:6[234689]0|77[45789])\\d{4}$/',
            'emergency' => '/^911$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
