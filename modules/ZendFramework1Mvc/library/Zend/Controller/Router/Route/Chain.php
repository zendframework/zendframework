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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router\Route;
use Zend\Config;

/**
 * Chain route is used for managing route chaining.
 *
 * @uses       \Zend\Controller\Router\Route\AbstractRoute
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Chain extends AbstractRoute
{
    protected $_routes = array();
    protected $_separators = array();

    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param \Zend\Config\Config $config Configuration object
     */
    public static function getInstance(Config\Config $config)
    {
        $defs = ($config->defaults instanceof Config\Config) ? $config->defaults->toArray() : array();
        return new self($config->route, $defs);
    }

    /**
     * Add a route to this chain
     *
     * @param  \Zend\Controller\Router\Route\AbstractRoute $route
     * @param  string                                $separator
     * @return \Zend\Controller\Router\Route\Chain
     */
    public function addChain(AbstractRoute $route, $separator = '/')
    {
        $this->_routes[]     = $route;
        $this->_separators[] = $separator;

        return $this;

    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param  \Zend\Controller\Request\Http $request Request to get the path info from
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($request, $partial = null)
    {
        $path    = trim($request->getPathInfo(), '/');
        $subPath = $path;
        $values  = array();

        foreach ($this->_routes as $key => $route) {
            if ($key > 0 && $matchedPath !== null) {
                $separator = substr($subPath, 0, strlen($this->_separators[$key]));

                if ($separator !== $this->_separators[$key]) {
                    return false;
                }

                $subPath = substr($subPath, strlen($separator));
            }

            // TODO: Should be an interface method. Hack for 1.0 BC
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $subPath;
            } else {
                $request->setPathInfo($subPath);
                $match = $request;
            }

            $res = $route->match($match, true);
            if ($res === false) {
                return false;
            }

            $matchedPath = $route->getMatchedPath();

            if ($matchedPath !== null) {
                $subPath     = substr($subPath, strlen($matchedPath));
                $separator   = substr($subPath, 0, strlen($this->_separators[$key]));
            }

            $values = $res + $values;
        }

        $request->setPathInfo($path);

        if ($subPath !== '' && $subPath !== false) {
            return false;
        }

        return $values;
    }

    /**
     * Assembles a URL path defined by this route
     *
     * @param array $data An array of variable and value pairs used as parameters
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false)
    {
        $value     = '';
        $numRoutes = count($this->_routes);

        foreach ($this->_routes as $key => $route) {
            if ($key > 0) {
                $value .= $this->_separators[$key];
            }

            $value .= $route->assemble($data, $reset, $encode, (($numRoutes - 1) > $key));

            if (method_exists($route, 'getVariables')) {
                $variables = $route->getVariables();

                foreach ($variables as $variable) {
                    $data[$variable] = null;
                }
            }
        }

        return $value;
    }

    /**
     * Set the request object for this and the child routes
     *
     * @param  \Zend\Controller\Request\AbstractRequest|null $request
     * @return void
     */
    public function setRequest(\Zend\Controller\Request\AbstractRequest $request = null)
    {
        $this->_request = $request;

        foreach ($this->_routes as $route) {
            if (method_exists($route, 'setRequest')) {
                $route->setRequest($request);
            }
        }
    }

}
