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
namespace Zend\Controller\Router\Http;
use Zend\Controller\Router\Route,
    Zend\Controller\Router\RouteMatch,
    Zend\Controller\Router\PriorityList,
    Zend\Controller\Request\AbstractRequest,
    Zend\Controller\Request\Http as HttpRequest;

/**
 * Route part.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Part implements Route
{
    /**
     * Route to match.
     * 
     * @var Route
     */
    protected $route;

    /**
     * Whether the route may terminate.
     *
     * @var boolean
     */
    protected $mayTerminate;

    /**
     * Children of the route.
     *
     * @var PriorityList
     */
    protected $children;

    /**
     * __construct(): defined by Route interface.
     *
     * @see    Route::__construct()
     * @param  mixed $options
     * @return void
     */
    public function __construct($options)
    {
        if (!isset($options['route']) || !$options['route'] instanceof Route) {
            throw new UnexpectedValueException('Route not defined or not an instance of Route');
        }

        $this->route        = $options['route'];
        $this->mayTerminate = (isset($options['may_terminate']) && $options['may_terminate']);
        $this->children     = new PriorityList();
    }

    /**
     * Append a route to the part.
     *
     * @param  string $name
     * @param  Route $route
     * @return Part
     */
    public function append($name, Route $route)
    {
        $this->children->insert($name, $route);

        return $this;
    }

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  AbstractRequest $request
     * @return RouteMatch
     */
    public function match(AbstractRequest $request, $pathOffset = null)
    {
        $match = $this->route->match($request, $pathOffset);

        if ($match !== null) {
            $nextOffset = $pathOffset + $match->getInternalParameter('length');
            
            foreach ($this->children as $name => $route) {
                $subMatch = $route->match($match, $pathOffset);

                if ($subMatch !== null) {
                    return $match->merge($subMatch);
                }
            }

            if ($this->mayTerminate && $nextOffset === strlen($request->getRequestUri())) {
                return $match;
            }
        }

        return null;
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = null, array $options = null)
    {
        if (!isset($options['name'])) {
            throw new InvalidArgumentException('Name not defined');
        }
        
        if (null === ($route = $this->route->get($options['name']))) {
            throw new RuntimeException(sprintf('Route with name "%s" not found', $options['name']));
        }
        
        unset($options['name']);
        
        $uri = $this->route->assemble($params, $options)
             . $route->assemble($params, $options);
        
        return $uri;
    }
}
