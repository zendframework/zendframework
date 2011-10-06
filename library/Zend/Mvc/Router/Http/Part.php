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
 * @package    Zend_Router
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
    Zend\Mvc\Router\Exception;

/**
 * Route part.
 *
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Part extends TreeRouteStack implements Route
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
     * __construct(): defined by Route interface.
     *
     * @see    Route::__construct()
     * @param  mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        
        if ($options instanceof Config) {
            $options = $options->toArray();
        } elseif ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected an array or Traversable; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        
        if (!isset($options['route']) || !$options['route'] instanceof Route) {
            throw new Exception\InvalidArgumentException('Route not defined or not an instance of Route');
        }

        $this->route        = $options['route'];
        $this->mayTerminate = (isset($options['may_terminate']) && $options['may_terminate']);
        
        if (isset($options['child_routes'])) {
            $this->childRoutes = $options['child_routes'];
        }
    }
    
    /**
     * init(): defined by SimpleRouteStack.
     * 
     * @see    SimpleRouteStack::init()
     * @return void
     */
    protected function init()
    {
        // Don't register HTTP plugins again.
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
            $path = $uri->getPath();
            
            if ($this->mayTerminate && $nextOffset === strlen($path)) {
                return $match;
            }
            
            foreach ($this->children as $name => $route) {
                $subMatch = $route->match($match, $nextOffset);

                if ($subMatch !== null) {
                    return $match->merge($subMatch);
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
    public function assemble(array $params = null, array $options = null)
    {
       if ($this->childRoutes !== null) {
            $this->addRoutes($this->childRoutes);
            $this->childRoutes = null;
        }
        
        $uri = $this->route->assemble($params, $options)
             . parent::assemble($params, $options);
        
        return $uri;
    }
}
