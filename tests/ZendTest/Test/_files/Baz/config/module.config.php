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
                            'controller' => 'baz_index',
                            'action'     => 'console',
                        ),
                    ),
                ),
                'arguments' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route'    => 'filter --date= --id= --text=',
                        'defaults' => array(
                            'controller' => 'baz_index',
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
                        'controller' => 'baz_index',
                        'action'     => 'unittests',
                    ),
                ),
            ),
            'myroutebis' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/tests-bis',
                    'defaults' => array(
                        'controller' => 'baz_index',
                        'action'     => 'unittests',
                    ),
                ),
            ),
            'exception' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/exception',
                    'defaults' => array(
                        'controller' => 'baz_index',
                        'action'     => 'exception',
                    ),
                ),
            ),
            'redirect' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/redirect',
                    'defaults' => array(
                        'controller' => 'baz_index',
                        'action'     => 'redirect',
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
                        'controller' => 'baz_index',
                        'action'     => 'unittests',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'baz_index' => 'Baz\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            '404' => __DIR__ . '/../view/baz/error/404.phtml',
            'error' => __DIR__ . '/../view/baz/error/error.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
