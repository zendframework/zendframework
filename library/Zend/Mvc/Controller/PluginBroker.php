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
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Controller;

use Zend\Loader\PluginBroker as PluginBrokerBase,
    Zend\Stdlib\DispatchableInterface as Dispatchable;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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
