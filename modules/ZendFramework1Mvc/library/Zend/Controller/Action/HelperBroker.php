<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Action;

use Zend\Controller\Action,
    Zend\Loader\PluginSpecBroker;

/**
 * @uses       \Zend\Controller\Action\Exception
 * @uses       \Zend\Controller\Action\HelperBroker\PriorityStack
 * @uses       \Zend\Loader
 * @uses       \Zend\Loader\PluginLoader
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperBroker extends PluginSpecBroker implements \IteratorAggregate
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Controller\Action\HelperLoader';

    /**
     * Action controller reference
     *
     * @var \Zend\Controller\Action
     */
    protected $actionController;

    /**
     * Priority stack of helpers
     *
     * @var \Zend\Controller\Action\HelperBroker\PriorityStack
     */
    protected $stack = null;
   
    /**
     * resetHelpers()
     *
     * @return void
     */
    public function reset()
    {
        $this->plugins = array();
        $this->stack   = null;
        return;
    }

    /**
     * Lazy load the priority stack and return it
     *
     * @return \Zend\Controller\Action\HelperPriorityStack
     */
    public function getStack()
    {
        if (!$this->stack instanceof HelperPriorityStack) {
            $this->stack = new HelperPriorityStack();
        }

        return $this->stack;
    }

    /**
     * Register a helper
     *
     * Proxies to parent functionality, and then registers helper with 
     * HelperPriorityStack.
     *
     * Additionally, if the plugin implements a "setBroker()" method, it will
     * inject itself into the helper.
     * 
     * @param  string $name 
     * @param  mixed $plugin 
     * @return HelperBroker
     */
    public function register($name, $plugin)
    {
        parent::register($name, $plugin);

        $stack   = $this->getStack();
        $stack[] = $plugin;

        if (method_exists($plugin, 'setBroker')) {
            $plugin->setBroker($this);
        }

        if (null !== $controller = $this->getActionController()) {
            $plugin->setActionController($controller);
        }

        return $this;
    }
    
    /**
     * Load and return a plugin instance
     *
     * If the plugin was previously loaded, returns that instance.
     *
     * If no options were passed, and we have no specification, load normally.
     *
     * If no options were passed, and we have a specification, use the
     * specification to load an instance.
     *
     * Otherwise, simply try and load the plugin.
     *
     * @param  string $plugin
     * @param  array|null $options
     * @return object
     * @throws Exception if plugin not found
     */
    public function load($plugin, array $options = null)
    {
        $pluginName = strtolower($plugin);
        if (isset($this->plugins[$pluginName])) {
            // If we've loaded it already, just return it
            return $this->plugins[$pluginName];
        }

        $instance = parent::load($plugin, $options);

        if (null !== $this->actionController) {
            $instance->setActionController($this->actionController);
            $instance->init();
        }

        return $instance;
    }

    /**
     * Determine if we have a valid helper
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws Exception
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Helper\AbstractHelper) {
            throw new Exception('Action helpers must implement Zend\Controller\Action\Helper\AbstractHelper');
        }
        return true;
    }

    /**
     * Get plugins
     *
     * Loops through specs to determine what if any have not yet been loaded,
     * and loads them; the full stack is returned.
     *
     * Returns the HelperPriorityStack.
     * 
     * @return HelperPriorityStack
     */
    public function getPlugins()
    {
        foreach ($this->specs as $name => $spec) {
            if (!$this->isLoaded($name)) {
                $this->load($name);
            }
        }
        return $this->getStack();
    }

    /**
     * Iterate over helpers
     * 
     * @return Iterator
     */
    public function getIterator()
    {
        return $this->getStack()->getIterator();
    }

    /**
     * Set action controller instance
     * 
     * @param  Action $actionController 
     * @return HelperBroker
     */
    public function setActionController(Action $actionController)
    {
        $this->actionController = $actionController;
        foreach ($this->getStack() as $helper) {
            $helper->setActionController($actionController);
            $helper->init();
        }
        return $this;
    }

    /**
     * Retrieve currently registered action controller instance
     * 
     * @return null|Action
     */
    public function getActionController()
    {
        return $this->actionController;
    }

    /**
     * notifyPreDispatch() - called by action controller dispatch method
     *
     * @return void
     */
    public function notifyPreDispatch()
    {
        foreach ($this as $helper) {
            $helper->preDispatch();
        }
    }

    /**
     * notifyPostDispatch() - called by action controller dispatch method
     *
     * @return void
     */
    public function notifyPostDispatch()
    {
        foreach ($this as $helper) {
            $helper->postDispatch();
        }
    }
}
