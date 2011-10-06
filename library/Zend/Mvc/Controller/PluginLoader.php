<?php

namespace Zend\Mvc\Controller;

use Zend\Loader\PluginClassLoader;

class PluginLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased plugins
     */
    protected $plugins = array(
        'forward'  => 'Zend\Mvc\Controller\Plugin\Forward',
        'redirect' => 'Zend\Mvc\Controller\Plugin\Redirect',
        'url'      => 'Zend\Mvc\Controller\Plugin\Url',
    );
}
