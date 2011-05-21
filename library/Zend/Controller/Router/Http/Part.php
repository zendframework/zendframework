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
    Zend\Controller\Router\Http\TreeRouteStack,
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
     * __construct(): defined by Route interface.
     *
     * @see    Route::__construct()
     * @param  mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        
        if (!is_array($options) && !$options instanceof \ArrayAccess) {
            throw new InvalidArgumentException(sprintf(
                'Expected an array or Traversable; received "%s"',
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        
        if (!isset($options['route']) || !$options['route'] instanceof Route) {
            throw new InvalidArgumentException('Route not defined or not an instance of Route');
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
     * @param  AbstractRequest $request
     * @return RouteMatch
     */
    public function match(AbstractRequest $request, $pathOffset = null)
    {
        $match = $this->route->match($request, $pathOffset);

        if ($match !== null) {
            if ($this->childRoutes !== null) {
                $this->addRoutes($this->childRoutes);
                $this->childRoutes = null;
            }
            
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
       if ($this->childRoutes !== null) {
            $this->addRoutes($this->childRoutes);
            $this->childRoutes = null;
        }
        
        $uri = $this->route->assemble($params, $options)
             . parent::assemble($params, $options);
        
        return $uri;
    }
}
