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
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller;

/**
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Router
{
    /**
     * Processes a request and sets its controller and action.  If
     * no route was possible, an exception is thrown.
     *
     * @param  \Zend\Controller\Request\AbstractRequest
     * @throws \Zend\Controller\Router\Exception
     * @return \Zend\Controller\Request\AbstractRequest|boolean
     */
    public function route(\Zend\Controller\Request\AbstractRequest $dispatcher);

    /**
     * Generates a URL path that can be used in URL creation, redirection, etc.
     *
     * May be passed user params to override ones from URI, Request or even defaults.
     * If passed parameter has a value of null, it's URL variable will be reset to
     * default.
     *
     * If null is passed as a route name assemble will use the current Route or 'default'
     * if current is not yet set.
     *
     * Reset is used to signal that all parameters should be reset to it's defaults.
     * Ignoring all URL specified values. User specified params still get precedence.
     *
     * Encode tells to url encode resulting path parts.
     *
     * @param  array $userParams Options passed by a user used to override parameters
     * @param  mixed $name The name of a Route to use
     * @param  bool $reset Whether to reset to the route defaults ignoring URL params
     * @param  bool $encode Tells to encode URL parts on output
     * @throws \Zend\Controller\Router\Exception
     * @return string Resulting URL path
     */
    public function assemble($userParams, $name = null, $reset = false, $encode = true);

    /**
     * Retrieve Front Controller
     *
     * @return \Zend\Controller\Front
     */
    public function getFrontController();

    /**
     * Set Front Controller
     *
     * @param \Zend\Controller\Front $controller
     * @return \Zend\Controller\Router
     */
    public function setFrontController(\Zend\Controller\Front $controller);

    /**
     * Add or modify a parameter with which to instantiate any helper objects
     *
     * @param string $name
     * @param mixed $param
     * @return \Zend\Controller\Router
     */
    public function setParam($name, $value);

    /**
     * Set an array of a parameters to pass to helper object constructors
     *
     * @param array $params
     * @return \Zend\Controller\Router
     */
    public function setParams(array $params);

    /**
     * Retrieve a single parameter from the controller parameter stack
     *
     * @param string $name
     * @return mixed
     */
    public function getParam($name);

    /**
     * Retrieve the parameters to pass to helper object constructors
     *
     * @return array
     */
    public function getParams();

    /**
     * Clear the controller parameter stack
     *
     * By default, clears all parameters. If a parameter name is given, clears
     * only that parameter; if an array of parameter names is provided, clears
     * each.
     *
     * @param null|string|array single key or array of keys for params to clear
     * @return \Zend\Controller\Router
     */
    public function clearParams($name = null);

}
