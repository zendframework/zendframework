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
 * Priority list
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class PriorityList implements \Iterator, \Countable
{
    /**
     * Internal list of all routes
     *
     * @var array
     */
    protected $_routes = array();

    /**
     * Serial assigned to routes to preserve LIFO
     * 
     * @var integer
     */
    protected $_serial = 0;

    /**
     * Internal counter to avoid usage of count()
     *
     * @var integer
     */
    protected $_count = 0;

    /**
     * Wether the list was already sorted
     *
     * @var boolean
     */
    protected $_sorted = false;

    /**
     * Insert a new route
     *
     * @param  string  $name
     * @param  Route   $route
     * @param  integer $priority
     * @return void
     */
    public function insert($name, Route $route, $priority)
    {
        $this->_sorted = false;
        $this->_count++;

        $this->_routes[$name] = array(
            'route'    => $route,
            'priority' => $priority,
            'serial'   => $this->_serial++
        );
    }

    /**
     * Remove a route
     *
     * @param  string $name
     * @return void
     */
    public function remove($name)
    {
        $this->_sorted = false;
        $this->_count--;

        unset($this->_routes['name']);
    }

    /**
     * Sort all routes
     *
     * @return void
     */
    protected function _sort()
    {
        uasort($this->_routes, array($this, '_compare'));
    }

    /**
     * Compare the priority of two routes
     *
     * @param  array $route1,
     * @param  array $route2
     * @return integer
     */
    protected function _compare(array $route1, array $route2)
    {
        if ($route1['priority'] === $route2['priority']) {
            return ($route1['serial'] > $route2['serial'] ? -1 : 1);
        }

        return ($route1['priority'] > $route2['priority'] ? -1 : 1);
    }

    /**
     * rewind(): defined by \Iterator interface
     *
     * @see    \Iterator::rewind()
     * @return void
     */
    public function rewind() {
        if (!$this->_sorted) {
            $this->_sort();
        }

        reset($this->_routes);
    }

    /**
     * current(): defined by \Iterator interface
     *
     * @see    \Iterator::current()
     * @return Route
     */
    public function current() {
        $node = current($this->_routes);
        return ($node !== false ? $node['route'] : false);
    }

    /**
     * key(): defined by \Iterator interface
     *
     * @see    \Iterator::key()
     * @return string
     */
    public function key() {
        return key($this->_routes);
    }

    /**
     * next(): defined by \Iterator interface
     *
     * @see    \Iterator::next()
     * @return Route
     */
    public function next() {
        $node = next($this->_routes);
        return ($node !== false ? $node['route'] : false);
    }

    /**
     * valid(): defined by \Iterator interface
     *
     * @see    \Iterator::valid()
     * @return boolean
     */
    public function valid() {
        return ($this->current() !== false);
    }

    /**
     * count(): defined by \Countable interface
     *
     * @see    \Countable::valid()
     * @return integer
     */
    public function count() {
        return $this->_count;
    }
}
