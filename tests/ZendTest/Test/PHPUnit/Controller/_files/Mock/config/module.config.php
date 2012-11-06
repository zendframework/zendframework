<?php

return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'consoleroute' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route'    => '--console',
                        'defaults' => array(
                            'controller' => 'mock_index',
                            'action'     => 'console',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'myroute' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/tests',
                    'defaults' => array(
                        'controller' => 'mock_index',
                        'action'     => 'unittests',
                    ),
                ),
            ),
            'dnsroute' => array(
                'type' => 'hostname',
                'options' => array(
                    'route' => ':subdomain.domain.tld',
                    'constraints' => array(
                        'subdomain' => '\w+'
                    ),
                    'defaults' => array(
                        'controller' => 'mock_index',
                        'action'     => 'unittests',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'mock_index' => 'Mock\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
