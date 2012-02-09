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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router;

use Countable,
    Iterator;

/**
 * Priority list
 *
 * @package    Zend_Mvc_Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class PriorityList implements Iterator, Countable
{
    /**
     * Internal list of all routes.
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Serial assigned to routes to preserve LIFO.
     * 
     * @var integer
     */
    protected $serial = 0;

    /**
     * Internal counter to avoid usage of count().
     *
     * @var integer
     */
    protected $count = 0;

    /**
     * Whether the list was already sorted.
     *
     * @var boolean
     */
    protected $sorted = false;

    /**
     * Insert a new route.
     *
     * @param  string  $name
     * @param  Route   $route
     * @param  integer $priority
     * @return void
     */
    public function insert($name, Route $route, $priority)
    {
        $this->sorted = false;
        $this->count++;

        $this->routes[$name] = array(
            'route'    => $route,
            'priority' => $priority,
            'serial'   => $this->serial++,
        );
    }

    /**
     * Remove a route.
     *
     * @param  string $name
     * @return void
     */
    public function remove($name)
    {
        if (!isset($this->routes[$name])) {
            return;
        }
        
        $this->count--;

        unset($this->routes[$name]);
    }
    
    /**
     * Remove all routes.
     * 
     * @return void 
     */
    public function clear()
    {
        $this->routes = array();
        $this->serial = 0;
        $this->count  = 0;
        $this->sorted = false;
    }
    
    /**
     * Get a route.
     * 
     * @param  string $name 
     * @return Route
     */
    public function get($name)
    {
        if (!isset($this->routes[$name])) {
            return null;
        }
        
        return $this->routes[$name]['route'];
    }

    /**
     * Sort all routes.
     *
     * @return void
     */
    protected function sort()
    {
        uasort($this->routes, array($this, 'compare'));
        $this->sorted = true;
    }

    /**
     * Compare the priority of two routes.
     *
     * @param  array $route1,
     * @param  array $route2
     * @return integer
     */
    protected function compare(array $route1, array $route2)
    {
        if ($route1['priority'] === $route2['priority']) {
            return ($route1['serial'] > $route2['serial'] ? -1 : 1);
        }

        return ($route1['priority'] > $route2['priority'] ? -1 : 1);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() 
    {
        if (!$this->sorted) {
            $this->sort();
        }

        reset($this->routes);
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return Route
     */
    public function current() 
    {
        $node = current($this->routes);
        return ($node !== false ? $node['route'] : false);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return string
     */
    public function key() 
    {
        return key($this->routes);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return Route
     */
    public function next() 
    {
        $node = next($this->routes);
        return ($node !== false ? $node['route'] : false);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return boolean
     */
    public function valid() 
    {
        return ($this->current() !== false);
    }

    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count() 
    {
        return $this->count;
    }
}
