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
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Application;

use Zend\Loader\PluginSpecBroker;

/**
 * Plugin Broker implementation for bootstrap resources.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ResourceBroker extends PluginSpecBroker implements BootstrapAware
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Application\ResourceLoader';

    /**
     * Bootstrap object with which to inject plugins
     * 
     * @var Bootstrap
     */
    protected $bootstrap;

    /**
     * Set boostrap object with which to inject resources
     * 
     * @param  ResourceBootstrapper $bootstrap 
     * @return ResourceBroker
     */
    public function setBootstrap(Bootstrapper $bootstrap)
    {
        $this->bootstrap = $bootstrap;
        return $this;
    }

    /**
     * Retrieve bootstrap object
     * 
     * @return null|ResourceBootstrapper
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * Load a resource
     * 
     * @param  mixed $plugin 
     * @param  array $options 
     * @return Zend\Application\Resource
     */
    public function load($plugin, array $options = null)
    {
        $resource = parent::load($plugin, $options);
        if (null !== $bootstrap = $this->getBootstrap()) {
            $resource->setBootstrap($bootstrap);
        }
        return $resource;
    }

    /**
     * Determine if we have a valid resource
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws InvalidArgumentException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Resource) {
            throw new \InvalidArgumentException('Bootstrap resources must implement Zend\Application\Resource');
        }
        return true;
    }
}
