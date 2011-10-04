<?php

namespace Zend\Mvc\Controller;

use Zend\Loader\PluginClassLoader;

class PluginLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased plugins
     */
    protected $plugins = array(
        'url'      => 'Zend\Mvc\Controller\Plugin\Url',
        'redirect' => 'Zend\Mvc\Controller\Plugin\Redirect',
    );
}
