<?php
return array(
    'modules' => array(
        'Baz',
    ),
    'module_listener_options' => array(
        'config_cache_enabled' => true,
        'cache_dir'            => __DIR__ . '/cache',
        'config_cache_key'     => 'phpunit',
        'config_static_paths'  => array(),
        'module_paths'         => array(
            'Baz' => __DIR__ . '/Baz/',
        ),
    ),
);
