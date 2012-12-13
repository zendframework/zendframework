<?php
return array(
    'router' => array(
        'routes' => array(
            'barroute' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/bar-test',
                    'defaults' => array(
                        'controller' => 'bar_index',
                        'action'     => 'unittests',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'bar_index' => 'Bar\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
