<?php
return array(
    'router' => array(
        'routes' => array(
            'fooroute' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/foo-test',
                    'defaults' => array(
                        'controller' => 'foo_index',
                        'action'     => 'unittests',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'foo_index' => 'Foo\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
