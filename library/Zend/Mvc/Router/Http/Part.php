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
 * @package    Zend_Mvc_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router\Http;

use Traversable,
    Zend\Config\Config,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Mvc\Router\Exception,
    Zend\Mvc\Router\PriorityList;

/**
 * Route part.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Part extends TreeRouteStack
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
     * Child routes.
     * 
     * @var mixed
     */
    protected $childRoutes;

    /**
     * Create a new part route.
     * 
     * @param  string  $route
     * @param  boolean $mayTerminate
     * @param  array   $childRoutes
     * @return void
     */
    public function __construct($route, $mayTerminate, array $childRoutes = null)
    {
        $this->route        = $route;
        $this->mayTerminate = $mayTerminate;
        $this->childRoutes  = $childRoutes;
        $this->routes       = new PriorityList();
    }
    
    /**
     * factory(): defined by Route interface.
     *
     * @see    Route::factory()
     * @param  mixed $options
     * @return void
     */
    public static function factory(array $options = array())
    {
        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (!isset($options['may_terminate'])) {
            $options['may_terminate'] = false;
        }
        
        if (!isset($options['child_routes']) || !$options['child_routes']) {
            $options['child_routes'] = null;
        }

        return new static($options['route'], $options['may_terminate'], $options['child_routes']);
    }

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  Request $request
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null)
    {
        $match = $this->route->match($request, $pathOffset);

        if ($match !== null && method_exists($request, 'uri')) {
            if ($this->childRoutes !== null) {
                $this->addRoutes($this->childRoutes);
                $this->childRoutes = null;
            }
            
            $nextOffset = $pathOffset + $match->getLength();
            
            $uri  = $request->uri();
            $pathLength = strlen($uri->getPath());
            
            if ($this->mayTerminate && $nextOffset === $pathLength) {
                return $match;
            }
            
            foreach ($this->routes as $name => $route) {
                if (($subMatch = $route->match($request, $nextOffset)) instanceof RouteMatch) {
                    if ($match->getLength() + $subMatch->getLength() + $pathOffset === $pathLength) {
                        return $match->merge($subMatch);
                    }
                }
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
    public function assemble(array $params = array(), array $options = array())
    {
       if ($this->childRoutes !== null) {
            $this->addRoutes($this->childRoutes);
            $this->childRoutes = null;
        }
        
        $uri = $this->route->assemble($params, $options);
        
        if (!isset($options['name'])) {
            if (!$this->mayTerminate) {
                throw new Exception\RuntimeException('Part route may not terminate');
            } else {
                return $uri;
            }
        }
        
        $uri .= parent::assemble($params, $options);
        
        return $uri;
    }
}
