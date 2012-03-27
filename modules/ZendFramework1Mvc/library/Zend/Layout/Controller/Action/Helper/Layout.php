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
namespace Zend\Layout\Controller\Action\Helper;


/**
 * Helper for interacting with Zend_Layout objects
 *
 * @uses       \Zend\Controller\Action\Helper\AbstractHelper
 * @uses       \Zend\Controller\Front
 * @uses       \Zend\Layout\Layout
 * @uses       \Zend\Layout\Exception
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Layout extends \Zend\Controller\Action\Helper\AbstractHelper
{
    /**
     * @var \Zend\Controller\Front
     */
    protected $_frontController;

    /**
     * @var \Zend\Layout\Layout
     */
    protected $_layout;

    /**
     * @var bool
     */
    protected $_isActionControllerSuccessful = false;

    /**
     * Constructor
     *
     * @param  \Zend\Layout\Layout $layout
     * @return void
     */
    public function __construct(\Zend\Layout\Layout $layout = null)
    {
        if (null !== $layout) {
            $this->setLayoutInstance($layout);
        } else {
            $layout = \Zend\Layout\Layout::getMvcInstance();
        }

        if (null !== $layout) {
            $pluginClass = $layout->getPluginClass();
            $front = $this->getFrontController();
            if ($front->hasPlugin($pluginClass)) {
                $plugin = $front->getPlugin($pluginClass);
                $plugin->setLayoutActionHelper($this);
            }
        }
    }

    public function init()
    {
        $this->_isActionControllerSuccessful = false;
    }

    /**
     * Get front controller instance
     *
     * @return \Zend\Controller\Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            $this->_frontController = \Zend\Controller\Front::getInstance();
        }

        return $this->_frontController;
    }

    /**
     * Get layout object
     *
     * @return \Zend\Layout\Layout
     */
    public function getLayoutInstance()
    {
        if (null === $this->_layout) {
            if (null === ($this->_layout = \Zend\Layout\Layout::getMvcInstance())) {
                $this->_layout = new \Zend\Layout\Layout();
            }
        }

        return $this->_layout;
    }

    /**
     * Set layout object
     *
     * @param  \Zend\Layout\Layout $layout
     * @return \Zend\Layout\Controller\Action\Helper\Layout
     */
    public function setLayoutInstance(\Zend\Layout\Layout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Mark Action Controller (according to this plugin) as Running successfully
     *
     * @return \Zend\Layout\Controller\Action\Helper\Layout
     */
    public function postDispatch()
    {
        $this->_isActionControllerSuccessful = true;
        return $this;
    }

    /**
     * Did the previous action successfully complete?
     *
     * @return bool
     */
    public function isActionControllerSuccessful()
    {
        return $this->_isActionControllerSuccessful;
    }

    /**
     * Strategy pattern; call object as method
     *
     * Returns layout object
     *
     * @return \Zend\Layout\Layout
     */
    public function direct()
    {
        return $this->getLayoutInstance();
    }

    /**
     * Proxy method calls to layout object
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $layout = $this->getLayoutInstance();
        if (method_exists($layout, $method)) {
            return call_user_func_array(array($layout, $method), $args);
        }

        throw new Layout\Exception(sprintf("Invalid method '%s' called on layout action helper", $method));
    }
}
