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
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

use Zend\View,
    Zend\Controller\Front as FrontController;

/**
 * Helper for rendering output of a controller action
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Action extends AbstractHelper
{
    /**
     * @var string
     */
    public $defaultModule;

    /**
     * @var \Zend\Controller\Dispatcher
     */
    public $dispatcher;

    /**
     * @var \Zend\Controller\Front
     */
    protected $front;

    /**
     * @var \Zend\Controller\Request\AbstractRequest
     */
    public $request;

    /**
     * @var \Zend\Controller\Response\AbstractResponse
     */
    public $response;

    /**
     * Constructor
     *
     * Grab local copies of various MVC objects
     *
     * @return void
     */
    public function __construct()
    {
        $front   = $this->front = \Zend\Controller\Front::getInstance();
        $modules = $front->getControllerDirectory();
        if (empty($modules)) {
            $e = new View\Exception('Action helper depends on valid front controller instance');
            $e->setView($this->view);
            throw $e;
        }

        $request  = $front->getRequest();
        $response = $front->getResponse();
        $broker   = $front->getHelperBroker();

        if (empty($request) || empty($response)) {
            $e = new View\Exception('Action view helper requires both a registered request and response object in the front controller instance');
            $e->setView($this->view);
            throw $e;
        }

        $this->request       = clone $request;
        $this->response      = clone $response;
        $this->defaultModule = $front->getDefaultModule();
        $this->dispatcher    = clone $front->getDispatcher();
        $this->dispatcher->setHelperBroker($broker);
    }

    /**
     * Reset object states
     *
     * @return void
     */
    public function resetObjects()
    {
        $params = $this->request->getUserParams();
        foreach (array_keys($params) as $key) {
            $this->request->setParam($key, null);
        }

        $this->response->clearBody();
        $this->response->clearHeaders()
                       ->clearRawHeaders();
    }

    /**
     * Retrieve rendered contents of a controller action
     *
     * If the action results in a forward or redirect, returns empty string.
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module Defaults to default module
     * @param  array $params
     * @return string
     */
    public function __invoke($action, $controller, $module = null, array $params = array())
    {
        $this->resetObjects();
        if (null === $module) {
            $module = $this->defaultModule;
        }

        // clone the view object to prevent over-writing of view variables
        $broker = $this->front->getHelperBroker();
        $viewRenderer = $broker->load('viewRenderer');
        $viewRendererClone = clone $viewRenderer;
        $broker->register('viewRenderer', $viewRendererClone);

        $this->request->setParams($params)
                      ->setModuleName($module)
                      ->setControllerName($controller)
                      ->setActionName($action)
                      ->setDispatched(true);

        $this->dispatcher->dispatch($this->request, $this->response);

        // reset the viewRenderer object to it's original state
        $broker->register('viewRenderer', $viewRenderer);

        if (!$this->request->isDispatched()
            || $this->response->isRedirect())
        {
            // forwards and redirects render nothing
            return '';
        }

        $return = $this->response->getBody();
        $this->resetObjects();
        return $return;
    }

    /**
     * Clone the current View
     *
     * @return \Zend\View\Renderer
     */
    public function cloneView()
    {
        $view = clone $this->view;
        $view->clearVars();
        return $view;
    }
}
