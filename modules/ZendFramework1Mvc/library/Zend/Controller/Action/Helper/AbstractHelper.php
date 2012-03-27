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
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Action\Helper;

use Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractHelper
{
    /**
     * $_actionController
     *
     * @var \Zend\Controller\Action $_actionController
     */
    protected $_actionController = null;

    /**
     * Action HelperBroker instance
     * @var Broker
     */
    protected $broker;

    /**
     * @var mixed $_frontController
     */
    protected $_frontController = null;

    /**
     * setActionController()
     *
     * @param  \Zend\Controller\Action $actionController
     * @return Zend_Controller_ActionHelper_Abstract Provides a fluent interface
     */
    public function setActionController(\Zend\Controller\Action $actionController = null)
    {
        $this->_actionController = $actionController;
        return $this;
    }

    /**
     * Retrieve current action controller
     *
     * @return \Zend\Controller\Action
     */
    public function getActionController()
    {
        return $this->_actionController;
    }

    /**
     * Set action helper broker
     * 
     * @param  Broker $broker 
     * @return AbstractHelper
     */
    public function setBroker(Broker $broker)
    {
        $this->broker = $broker;
        return $this;
    }

    /**
     * Get action helper broker instance
     * 
     * @return null|Broker
     */
    public function getBroker()
    {
        return $this->broker;
    }

    /**
     * Retrieve front controller instance
     *
     * @return \Zend\Controller\Front
     */
    public function getFrontController()
    {
        return \Zend\Controller\Front::getInstance();
    }

    /**
     * Hook into action controller initialization
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Hook into action controller preDispatch() workflow
     *
     * @return void
     */
    public function preDispatch()
    {
    }

    /**
     * Hook into action controller postDispatch() workflow
     *
     * @return void
     */
    public function postDispatch()
    {
    }

    /**
     * getRequest() -
     *
     * @return \Zend\Controller\Request\AbstractRequest $request
     */
    public function getRequest()
    {
        $controller = $this->getActionController();
        if (null === $controller) {
            $controller = $this->getFrontController();
        }

        return $controller->getRequest();
    }

    /**
     * getResponse() -
     *
     * @return \Zend\Controller\Response\AbstractResponse $response
     */
    public function getResponse()
    {
        $controller = $this->getActionController();
        if (null === $controller) {
            $controller = $this->getFrontController();
        }

        return $controller->getResponse();
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        $className = get_class($this);

        if (strpos($className, '\\') !== false) {
            $name  = ltrim(strrchr($className, '\\'), '\\');
            $words = preg_split('/(?<=\\w)(?=[A-Z])/', $name);
            return strtolower(implode('_', $words));
        } else {
            return $className;
        }
    }

}
