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
 * @package    Zend_Mvc
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Router;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for routes
 *
 * Enforces that routes retrieved are instances of RouteInterface. It overrides 
 * createFromInvokable() to call the route's factory method in order to get an 
 * instance. The manager is marked to not share by default, in order to allow 
 * multiple route instances of the same type.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RoutePluginManager extends AbstractPluginManager
{
    /**
     * @var bool Do not share instances
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof RouteInterface) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\RouteInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }

    /**
     * Attempt to create an instance via an invokable class
     *
     * Overrides parent implementation by invoking the route factory,
     * passing $creationOptions as the argument.
     * 
     * @param  string $canonicalName 
     * @param  string $requestedName 
     * @return null|\stdClass
     * @throws Exception\RuntimeException If resolved class does not exist, or does not implement RouterInterface
     */
    protected function createFromInvokable($canonicalName, $requestedName)
    {
        $invokable = $this->invokableClasses[$canonicalName];
        if (!class_exists($invokable)) {
            throw new Exception\RuntimeException(sprintf(
                '%s: failed retrieving "%s%s" via invokable class "%s"; class does not exist',
                __METHOD__,
                $canonicalName,
                ($requestedName ? '(alias: ' . $requestedName . ')' : ''),
                $canonicalName
            ));
        }

        if (!$this->isSubclassOf($invokable, __NAMESPACE__ . '\RouteInterface')) {
            throw new Exception\RuntimeException(sprintf(
                '%s: failed retrieving "%s%s" via invokable class "%s"; class does not implement %s\RouteInterface',
                __METHOD__,
                $canonicalName,
                ($requestedName ? '(alias: ' . $requestedName . ')' : ''),
                $canonicalName,
                __NAMESPACE__
            ));
        }

        return $invokable::factory($this->creationOptions);
    }
}
