<?php
return array(
    'modules' => array(
        'Mock',
        'Foo',
        'Bar',
    ),
    'module_listener_options' => array(
        'config_static_paths'    => array(),
        'module_paths' => array(
            'Mock' => __DIR__ . '/Mock/',
            'Foo' => __DIR__ . '/modules-path/with-subdir/Foo',
            'Bar' => __DIR__ . '/modules-path/with-subdir/Bar',
        ),
    ),
);
