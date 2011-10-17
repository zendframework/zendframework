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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router;

/**
 * Route match.
 *
 * @package    Zend_Mvc_Router
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RouteMatch
{
    /**
     * Match parameters.
     * 
     * @var array
     */
    protected $params = array();
    
    /**
     * Create a RouteMatch with given parameters.
     * 
     * @param  array $params
     * @return void
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }
       
    /**
     * Set a parameter.
     * 
     * @param  string $name
     * @param  mixed  $value 
     * @return void
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }
    
    /**
     * Get all parameters.
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Get a specific parameter.
     * 
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        
        return $default;
    }
    
    /**
     * Merge parameters from another match.
     * 
     * @param  self $match
     * @return void
     */
    public function merge(self $match)
    {
        $this->params = array_merge($this->params, $match->getParams());
        return $this;
    }
}
