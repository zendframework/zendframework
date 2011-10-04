<?php

namespace Zend\Mvc\Controller;

use Zend\Loader\PluginBroker as PluginBrokerBase,
    Zend\Stdlib\Dispatchable;

class PluginBroker extends PluginBrokerBase
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Mvc\Controller\PluginLoader';

    /**
     * @var Dispatchable
     */
    protected $controller;

    /**
     * Set controller object
     *
     * @param  Dispatchable $controller
     * @return PluginBroker
     */
    public function setController(Dispatchable $controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Retrieve controller instance
     *
     * @return null|Dispatchable
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Load a plugin
     *
     * Injects the controller object into the plugin prior to returning it, if 
     * available, and if the plugin supports it.
     *
     * @param  mixed $plugin
     * @param  array|null $options
     * @return mixed
     */
    public function load($plugin, array $options = null)
    {
        $helper = parent::load($plugin, $options);
        if (method_exists($helper, 'setController')) {
            if (null !== ($controller = $this->getController())) {
                $helper->setController($controller);
            }
        }
        return $helper;
    }
}
