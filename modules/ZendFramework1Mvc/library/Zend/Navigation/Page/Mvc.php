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
 * @package    Zend_Navigation
 * @subpackage Page
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Navigation\Page;

use Zend\Navigation\Exception,
    Zend\Controller\Front as FrontController;

/**
 * Represents a page that is defined using module, controller, action, route
 * name and route params to assemble the href
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage Page
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Mvc extends AbstractPage
{
    /**
     * Action name to use when assembling URL
     *
     * @var string
     */
    protected $action;

    /**
     * Controller name to use when assembling URL
     *
     * @var string
     */
    protected $controller;

    /**
     * Module name to use when assembling URL
     *
     * @var string
     */
    protected $module;

    /**
     * Params to use when assembling URL
     *
     * @see getHref()
     * @var array
     */
    protected $params = array();

    /**
     * Route name to use when assembling URL
     *
     * @see getHref()
     * @var string
     */
    protected $route;

    /**
     * Whether params should be reset when assembling URL
     *
     * @see getHref()
     * @var bool
     */
    protected $resetParams = true;

    /**
     * Whether href should be encoded when assembling URL
     *
     * @see getHref()
     * @var bool
     */
    protected $encodeUrl = true;

    /**
     * Cached href
     *
     * The use of this variable minimizes execution time when getHref() is
     * called more than once during the lifetime of a request. If a property
     * is updated, the cache is invalidated.
     *
     * @var string
     */
    protected $hrefCache;

    /**
     * Action helper for assembling URLs
     *
     * @see getHref()
     * @var \Zend\Controller\Action\Helper\Url
     */
    protected static $urlHelper = null;

    // Accessors:

    /**
     * Returns whether page should be considered active or not
     *
     * This method will compare the page properties against the request object
     * that is found in the front controller.
     *
     * @param  bool $recursive  [optional] whether page should be considered
     *                          active if any child pages are active. Default is
     *                          false.
     *
     * @return bool             whether page should be considered active or not
     */
    public function isActive($recursive = false)
    {
        if (!$this->active) {
            $front     = FrontController::getInstance();
            $reqParams = $front->getRequest()->getParams();

            if (!array_key_exists('module', $reqParams)) {
                $reqParams['module'] = $front->getDefaultModule();
            }

            $myParams = $this->params;

            if (null !== $this->module) {
                $myParams['module'] = $this->module;
            } else {
                $myParams['module'] = $front->getDefaultModule();
            }

            if (null !== $this->controller) {
                $myParams['controller'] = $this->controller;
            } else {
                $myParams['controller'] = $front->getDefaultControllerName();
            }

            if (null !== $this->action) {
                $myParams['action'] = $this->action;
            } else {
                $myParams['action'] = $front->getDefaultAction();
            }

            if (count(array_intersect_assoc($reqParams, $myParams)) ==
                count($myParams)
            ) {
                $this->active = true;
                return true;
            }
        }

        return parent::isActive($recursive);
    }

    /**
     * Returns href for this page
     *
     * This method uses {@link \Zend\Controller\Action\Helper\Url} to assemble
     * the href based on the page's properties.
     *
     * @return string  page href
     */
    public function getHref()
    {
        if ($this->hrefCache) {
            return $this->hrefCache;
        }

        if (null === self::$urlHelper) {
            $front           = FrontController::getInstance();
            $broker          = $front->getHelperBroker();
            self::$urlHelper = $broker->load('url');
        }

        $params = $this->getParams();

        if (($param = $this->getModule()) != null) {
            $params['module'] = $param;
        }

        if (($param = $this->getController()) != null) {
            $params['controller'] = $param;
        }

        if (($param = $this->getAction()) != null) {
            $params['action'] = $param;
        }

        $url = self::$urlHelper->__invoke(
            $params,
            $this->getRoute(),
            $this->getResetParams(),
            $this->getEncodeUrl()
        );

        // Add the fragment identifier if it is set
        $fragment = $this->getFragment();
        if (null !== $fragment) {
            $url .= '#' . $fragment;
        }

        return $this->hrefCache = $url;
    }

    /**
     * Sets action name to use when assembling URL
     *
     * @see getHref()
     *
     * @param  string $action   action name
     *
     * @return Page\Mvc   fluent interface, returns self
     * @throws Exception\InvalidArgumentException  if invalid $action is given
     */
    public function setAction($action)
    {
        if (null !== $action && !is_string($action)) {
            throw new Exception\InvalidArgumentException(
                'Invalid argument: $action must be a string or null'
            );
        }

        $this->action    = $action;
        $this->hrefCache = null;
        return $this;
    }

    /**
     * Returns action name to use when assembling URL
     *
     * @see getHref()
     *
     * @return string|null  action name
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Sets controller name to use when assembling URL
     *
     * @see getHref()
     *
     * @param  string|null $controller  controller name
     *
     * @return Page\Mvc   fluent interface, returns self
     * @throws Exception\InvalidArgumentException   if invalid controller name
     *                                              is given
     */
    public function setController($controller)
    {
        if (null !== $controller && !is_string($controller)) {
            throw new Exception\InvalidArgumentException(
                'Invalid argument: $controller must be a string or null'
            );
        }

        $this->controller = $controller;
        $this->hrefCache  = null;
        return $this;
    }

    /**
     * Returns controller name to use when assembling URL
     *
     * @see getHref()
     *
     * @return string|null  controller name or null
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sets module name to use when assembling URL
     *
     * @see getHref()
     *
     * @param  string|null $module  module name
     *
     * @return Page\Mvc   fluent interface, returns self
     * @throws Exception\InvalidArgumentException   if invalid module name
     *                                              is given
     */
    public function setModule($module)
    {
        if (null !== $module && !is_string($module)) {
            throw new Exception\InvalidArgumentException(
                'Invalid argument: $module must be a string or null'
            );
        }

        $this->module    = $module;
        $this->hrefCache = null;
        return $this;
    }

    /**
     * Returns module name to use when assembling URL
     *
     * @see getHref()
     *
     * @return string|null  module name or null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Sets params to use when assembling URL
     *
     * @see getHref()
     *
     * @param  array|null $params   [optional] page params. Default is null
     *                              which sets no params.
     *
     * @return Page\Mvc  fluent interface, returns self
     */
    public function setParams(array $params = null)
    {
        if (null === $params) {
            $this->params = array();
        } else {
            // TODO: do this more intelligently?
            $this->params = $params;
        }

        $this->hrefCache = null;
        return $this;
    }

    /**
     * Returns params to use when assembling URL
     *
     * @see getHref()
     *
     * @return array  page params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Sets route name to use when assembling URL
     *
     * @see getHref()
     *
     * @param  string $route    route name to use when assembling URL
     *
     * @return Page\Mvc   fluent interface, returns self
     * @throws Exception\InvalidArgumentException  if invalid $route is given
     */
    public function setRoute($route)
    {
        if (null !== $route && (!is_string($route) || strlen($route) < 1)) {
            throw new Exception\InvalidArgumentException(
                'Invalid argument: $route must be a non-empty string or null'
            );
        }

        $this->route     = $route;
        $this->hrefCache = null;
        return $this;
    }

    /**
     * Returns route name to use when assembling URL
     *
     * @see getHref()
     *
     * @return string  route name
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Sets whether params should be reset when assembling URL
     *
     * @see getHref()
     *
     * @param  bool $resetParams    whether params should be reset when
     *                              assembling URL
     *
     * @return Page\Mvc  fluent interface, returns self
     */
    public function setResetParams($resetParams)
    {
        $this->resetParams = (bool)$resetParams;
        $this->hrefCache   = null;
        return $this;
    }

    /**
     * Returns whether params should be reset when assembling URL
     *
     * @see getHref()
     *
     * @return bool  whether params should be reset when assembling URL
     */
    public function getResetParams()
    {
        return $this->resetParams;
    }

    /**
     * Sets whether href should be encoded when assembling URL
     *
     * @see getHref()
     *
     * @param bool $resetParams whether href should be encoded when
     *                          assembling URL
     *
     * @return Page\Mvc fluent interface, returns self
     */
    public function setEncodeUrl($encodeUrl)
    {
        $this->encodeUrl = (bool) $encodeUrl;
        $this->hrefCache = null;

        return $this;
    }

    /**
     * Returns whether herf should be encoded when assembling URL
     *
     * @see getHref()
     *
     * @return bool whether herf should be encoded when assembling URL
     */
    public function getEncodeUrl()
    {
        return $this->encodeUrl;
    }

    /**
     * Sets action helper for assembling URLs
     *
     * @see getHref()
     *
     * @param  \Zend\Controller\Action\Helper\Url $uh  URL helper
     *
     * @return void
     */
    public static function setUrlHelper(\Zend\Controller\Action\Helper\Url $uh)
    {
        self::$urlHelper = $uh;
    }

    // Public methods:

    /**
     * Returns an array representation of the page
     *
     * @return array  associative array containing all page properties
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            array(
                 'action'       => $this->getAction(),
                 'controller'   => $this->getController(),
                 'module'       => $this->getModule(),
                 'params'       => $this->getParams(),
                 'route'        => $this->getRoute(),
                 'reset_params' => $this->getResetParams(),
                 'encodeUrl'    => $this->getEncodeUrl(),
            )
        );
    }
}
