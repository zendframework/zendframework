<?php

namespace Zend\Mvc\Controller;

use Zend\Loader\PluginClassLoader;

class PluginLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased plugins
     */
    protected $plugins = array(
        'flash_messenger' => 'Zend\Mvc\Controller\Plugin\FlashMessenger',
        'flashmessenger'  => 'Zend\Mvc\Controller\Plugin\FlashMessenger',
        'forward'         => 'Zend\Mvc\Controller\Plugin\Forward',
        'layout'          => 'Zend\Mvc\Controller\Plugin\Layout',
        'redirect'        => 'Zend\Mvc\Controller\Plugin\Redirect',
        'url'             => 'Zend\Mvc\Controller\Plugin\Url',
    );
}
