<?php
return array(
    'modules' => array(
        'Baz',
        'Foo',
        'Bar',
    ),
    'module_listener_options' => array(
        'config_static_paths' => array(),
        'module_paths'        => array(
            'Baz' => __DIR__ . '/Baz/',
            'Foo' => __DIR__ . '/modules-path/with-subdir/Foo',
            'Bar' => __DIR__ . '/modules-path/with-subdir/Bar',
        ),
    ),
);
