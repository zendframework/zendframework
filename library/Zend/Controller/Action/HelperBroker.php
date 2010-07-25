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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Controller\Action;

use Zend\Controller\Action,
    Zend\Loader\PrefixPathMapper,
    Zend\Loader\ShortNameLocater,
    Zend\Loader\PluginLoaderException,
    Zend\Loader\PluginLoader;

/**
 * @uses       \Zend\Controller\Action\Exception
 * @uses       \Zend\Controller\Action\HelperBroker\PriorityStack
 * @uses       \Zend\Loader
 * @uses       \Zend\Loader\PluginLoader
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperBroker
{
    /**
     * $_actionController - ActionController reference
     *
     * @var \Zend\Controller\Action
     */
    protected $_actionController;

    /**
     * @var \Zend\Loader\ShortNameLocater
     */
    protected static $_pluginLoader;

    /**
     * $_helpers - Helper array
     *
     * @var \Zend\Controller\Action\HelperBroker\PriorityStack
     */
    protected static $_stack = null;

    /**
     * Set PluginLoader for use with broker
     *
     * @param  \Zend\Loader\ShortNameLocater $loader
     * @return void
     */
    public static function setPluginLoader(ShortNameLocater $loader = null)
    {
        self::$_pluginLoader = $loader;
    }

    public static function reset()
    {
        self::resetHelpers();
        self::$_pluginLoader = null;
    }
    
    /**
     * Retrieve PluginLoader
     *
     * @return \Zend\Loader\ShortNameLocater
     */
    public static function getPluginLoader()
    {
        if (null === self::$_pluginLoader) {
            self::$_pluginLoader = new PluginLoader(array(
                'Zend\Controller\Action\Helper' => 'Zend/Controller/Action/Helper/',
            ));
        }
        return self::$_pluginLoader;
    }

    /**
     * addPrefix() - Add repository of helpers by prefix
     *
     * @param string $prefix
     */
    static public function addPrefix($prefix)
    {
        $loader = self::getPluginLoader();
        if (!$loader instanceof PrefixPathMapper) {
            return;
        }
        $prefix = rtrim($prefix, '\\');
        $path   = str_replace('\\', DIRECTORY_SEPARATOR, $prefix);
        $loader->addPrefixPath($prefix, $path);
    }

    /**
     * addPath() - Add path to repositories where Action_Helpers could be found.
     *
     * @param string $path
     * @param string $prefix Optional; defaults to 'Zend_Controller_Action_Helper'
     * @return void
     */
    static public function addPath($path, $prefix = 'Zend\Controller\Action\Helper')
    {
        $loader = self::getPluginLoader();
        if (!$loader instanceof PrefixPathMapper) {
            return;
        }
        $loader->addPrefixPath($prefix, $path);
    }

    /**
     * addHelper() - Add helper objects
     *
     * @param \Zend\Controller\Action\Helper\AbstractHelper $helper
     * @return void
     */
    static public function addHelper(Helper\AbstractHelper $helper)
    {
        self::getStack()->push($helper);
        return;
    }

    /**
     * resetHelpers()
     *
     * @return void
     */
    static public function resetHelpers()
    {
        self::$_stack = null;
        return;
    }

    /**
     * Retrieve or initialize a helper statically
     *
     * Retrieves a helper object statically, loading on-demand if the helper
     * does not already exist in the stack. Always returns a helper, unless
     * the helper class cannot be found.
     *
     * @param  string $name
     * @return \Zend\Controller\Action\Helper\AbstractHelper
     */
    public static function getStaticHelper($name)
    {
        $name  = self::_normalizeHelperName($name);
        $stack = self::getStack();

        if (!isset($stack->{$name})) {
            self::_loadHelper($name);
        }

        return $stack->{$name};
    }

    /**
     * getExistingHelper() - get helper by name
     *
     * Static method to retrieve helper object. Only retrieves helpers already
     * initialized with the broker (either via addHelper() or on-demand loading
     * via getHelper()).
     *
     * Throws an exception if the referenced helper does not exist in the
     * stack; use {@link hasHelper()} to check if the helper is registered
     * prior to retrieving it.
     *
     * @param  string $name
     * @return \Zend\Controller\Action\Helper\AbstractHelper
     * @throws \Zend\Controller\Action\Exception
     */
    public static function getExistingHelper($name)
    {
        $name  = self::_normalizeHelperName($name);
        $stack = self::getStack();

        if (!isset($stack->{$name})) {
            throw new Exception('Action helper "' . $name . '" has not been registered with the helper broker');
        }

        return $stack->{$name};
    }

    /**
     * Return all registered helpers as helper => object pairs
     *
     * @return array
     */
    public static function getExistingHelpers()
    {
        return self::getStack()->getHelpersByName();
    }

    /**
     * Is a particular helper loaded in the broker?
     *
     * @param  string $name
     * @return boolean
     */
    public static function hasHelper($name)
    {
        $name = self::_normalizeHelperName($name);
        return isset(self::getStack()->{$name});
    }

    /**
     * Remove a particular helper from the broker
     *
     * @param  string $name
     * @return boolean
     */
    public static function removeHelper($name)
    {
        $name = self::_normalizeHelperName($name);
        $stack = self::getStack();
        if (isset($stack->{$name})) {
            unset($stack->{$name});
        }

        return false;
    }

    /**
     * Lazy load the priority stack and return it
     *
     * @return \Zend\Controller\Action\HelperBroker\PriorityStack
     */
    public static function getStack()
    {
        if (self::$_stack == null) {
            self::$_stack = new HelperPriorityStack();
        }

        return self::$_stack;
    }

    /**
     * Constructor
     *
     * @param \Zend\Controller\Action\Action $actionController
     * @return void
     */
    public function __construct(Action $actionController)
    {
        $this->_actionController = $actionController;
        foreach (self::getStack() as $helper) {
            $helper->setActionController($actionController);
            $helper->init();
        }
    }

    /**
     * notifyPreDispatch() - called by action controller dispatch method
     *
     * @return void
     */
    public function notifyPreDispatch()
    {
        foreach (self::getStack() as $helper) {
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
        foreach (self::getStack() as $helper) {
            $helper->postDispatch();
        }
    }

    /**
     * getHelper() - get helper by name
     *
     * @param  string $name
     * @return \Zend\Controller\Action\Helper\AbstractHelper
     */
    public function getHelper($name)
    {
        $name  = self::_normalizeHelperName($name);
        $stack = self::getStack();

        if (!isset($stack->{$name})) {
            self::_loadHelper($name);
        }

        $helper = $stack->{$name};

        $initialize = false;
        if (null === ($actionController = $helper->getActionController())) {
            $initialize = true;
        } elseif ($actionController !== $this->_actionController) {
            $initialize = true;
        }

        if ($initialize) {
            $helper->setActionController($this->_actionController)
                   ->init();
        }

        return $helper;
    }

    /**
     * Method overloading
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws \Zend\Controller\Action\Exception if helper does not have a direct() method
     */
    public function __call($method, $args)
    {
        $helper = $this->getHelper($method);
        if (!method_exists($helper, 'direct')) {
            throw new Exception('Helper "' . $method . '" does not support overloading via direct()');
        }
        return call_user_func_array(array($helper, 'direct'), $args);
    }

    /**
     * Retrieve helper by name as object property
     *
     * @param  string $name
     * @return \Zend\Controller\Action\Helper\AbstractHelper
     */
    public function __get($name)
    {
        return $this->getHelper($name);
    }

    /**
     * Normalize helper name for lookups
     *
     * @param  string $name
     * @return string
     */
    protected static function _normalizeHelperName($name)
    {
        if (strpos($name, '\\') !== false) {
            $name = str_replace(' ', '', ucwords(str_replace('\\', ' ', $name)));
        }

        return ucfirst($name);
    }

    /**
     * Load a helper
     *
     * @param  string $name
     * @return void
     */
    protected static function _loadHelper($name)
    {
        try {
            $class = self::getPluginLoader()->load($name);
        } catch (PluginLoaderException $e) {
            throw new Exception('Action Helper by name ' . $name . ' not found', 0, $e);
        }

        $helper = new $class();

        if (!$helper instanceof Helper\AbstractHelper) {
            throw new Exception('Helper name ' . $name . ' -> class ' . $class . ' is not of type Zend\Controller\Action\Helper\AbstractHelper');
        }

        self::getStack()->push($helper);
    }
}
