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
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router\Rewrite;
use Zend\Controller\Router\Rewrite\Route\Route;

/**
 * Ruby routing based router
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Router
{
    /**
     * Append multiple routes
     *
     * @param  array $routes
     * @return Router
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }

        return $this;
    }

    /**
     * Appends a route to the end of the list
     *
     * @param  string $name
     * @param  mixed  $route
     * @return Router
     */
    public function addRoute($name, $route)
    {
        if (is_array($route)) {
            // @todo: Array to AbstractRoute
        } elseif (!$route instanceof Route) {
            // @todo: throw exception
        }

        $this->_routes[$name] = $route;

        return $this;
    }

    /**
     * Match a request
     *
     * @param \Zend\Controller\Request\HTTP $request
     */
    public function match(\Zend\Controller\Request\HTTP $request)
    {

    }

    /**
     * Assemble an URL
     *
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(array $params = null, array $options = null)
    {
        
    }
}
