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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
     * Matched route name.
     * 
     * @var string
     */
    protected $matchedRouteName;
    
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
     * Set name of matched route.
     * 
     * @param  string $name
     * @return self
     */
    public function setMatchedRouteName($name)
    {
        $this->matchedRouteName = $name;
        return $this;
    }
    
    /**
     * Get name of matched route.
     * 
     * @return string
     */
    public function getMatchedRouteName()
    {
        return $this->matchedRouteName;
    }
       
    /**
     * Set a parameter.
     * 
     * @param  string $name
     * @param  mixed  $value 
     * @return self
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
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
}
