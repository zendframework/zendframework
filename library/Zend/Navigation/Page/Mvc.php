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

use Zend\Mvc\Router\RouteMatch,
    Zend\Navigation\Exception,
    Zend\View\Helper\Url as UrlHelper;

/**
 * Represents a page that is defined using controller, action, route
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
     * Params to use when assembling URL
     *
     * @see getHref()
     * @var array
     */
    protected $params = array();

    /**
     * RouteInterface name to use when assembling URL
     *
     * @see getHref()
     * @var string
     */
    protected $route;

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
     * RouteInterface matches; used for routing parameters and testing validity
     *
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * View helper for assembling URLs
     *
     * @see getHref()
     * @var UrlHelper
     */
    protected $urlHelper = null;

    /**
     * Default urlHelper to be used if urlHelper is not given.
     *
     * @see getHref()
     *
     * @var UrlHelper
     */
    protected static $defaultUrlHelper = null;

    // Accessors:

    /**
     * Returns whether page should be considered active or not
     *
     * This method will compare the page properties against the route matches
     * composed in the object.
     *
     * @param  bool $recursive  [optional] whether page should be considered
     *                          active if any child pages are active. Default is
     *                          false.
     * @return bool             whether page should be considered active or not
     */
    public function isActive($recursive = false)
    {
        if (!$this->active) {
            $reqParams = array();
            if ($this->routeMatch instanceof RouteMatch) {
                $reqParams = $this->routeMatch->getParams();

                if (null !== $this->getRoute()
                    && $this->routeMatch->getMatchedRouteName() === $this->getRoute()
                ) {
                    $this->active = true;
                    return true;
                }
            }


            $myParams = $this->params;

            if (null !== $this->controller) {
                $myParams['controller'] = $this->controller;
            } else {
                /**
                 * @todo In ZF1, this was configurable and pulled from the front controller
                 */
                $myParams['controller'] = 'index';
            }

            if (null !== $this->action) {
                $myParams['action'] = $this->action;
            } else {
                /**
                 * @todo In ZF1, this was configurable and pulled from the front controller
                 */
                $myParams['action'] = 'action';
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
     * This method uses {@link UrlHelper} to assemble
     * the href based on the page's properties.
     *
     * @see UrlHelper
     * @return string  page href
     * @throws Exception\DomainException if no UrlHelper is set
     */
    public function getHref()
    {
        if ($this->hrefCache) {
            return $this->hrefCache;
        }

        $helper = $this->urlHelper;
        if (null === $helper) {
            $helper = self::$defaultUrlHelper;
        }

        if (!$helper instanceof UrlHelper) {
            throw new Exception\DomainException(
                __METHOD__
                . ' cannot execute as no Zend\View\Helper\Url instance is composed'
            );
        }

        $params = $this->getParams();

        if (($param = $this->getController()) != null) {
            $params['controller'] = $param;
        }

        if (($param = $this->getAction()) != null) {
            $params['action'] = $param;
        }

        $url = $helper(
            $this->getRoute(),
            $params
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
     * @param  string $action             action name
     * @return Mvc   fluent interface, returns self
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
     * @param  string|null $controller    controller name
     * @return Mvc   fluent interface, returns self
     * @throws Exception\InvalidArgumentException  if invalid controller name is given
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
     * Sets params to use when assembling URL
     *
     * @see getHref()
     * @param  array|null $params [optional] page params. Default is null
     *                            which sets no params.
     * @return Mvc  fluent interface, returns self
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
     * @param  string $route              route name to use when assembling URL
     * @return Mvc   fluent interface, returns self
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
     * Set route match object from which parameters will be retrieved
     *
     * @param  RouteMatch $matches
     * @return Mvc fluent interface, returns self
     */
    public function setRouteMatch(RouteMatch $matches)
    {
        $this->routeMatch = $matches;
        return $this;
    }

    /**
     * Sets action helper for assembling URLs
     *
     * @see getHref()
     *
     * @param  UrlHelper $helper URL helper plugin
     * @return Mvc    fluent interface, returns self
     */
    public function setUrlHelper(UrlHelper $helper)
    {
        $this->urlHelper = $helper;
        return $this;
    }

    /**
     * Sets the default view helper for assembling URLs.
     *
     * @see getHref()
     * @param  null|UrlHelper $helper  URL helper
     * @return void
     */
    public static function setDefaultUrlHelper($helper)
    {
        self::$defaultUrlHelper = $helper;
    }

    /**
     * Gets the default view helper for assembling URLs.
     *
     * @return UrlHelper
     */
    public static function getDefaultUrlHelper()
    {
        return self::$defaultUrlHelper;
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
                 'action'     => $this->getAction(),
                 'controller' => $this->getController(),
                 'params'     => $this->getParams(),
                 'route'      => $this->getRoute(),
            )
        );
    }
}
