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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router\Http;

use Zend\Loader\PluginSpecBroker,
    Zend\Http\Request,
    Zend\Mvc\Router\SimpleRouteStack;

/**
 * Tree search implementation.
 *
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TreeRouteStack extends SimpleRouteStack
{
    /**
     * init(): defined by SimpleRouteStack.
     * 
     * @see    SimpleRouteStack::init()
     * @return void
     */
    protected function init()
    {
        $this->pluginBroker->getClassLoader()->registerPlugins(array(
            'literal' => __NAMESPACE__ . '\Literal',
            'regex'   => __NAMESPACE__ . '\Regex',
            'segment' => __NAMESPACE__ . '\Segment',
            'part'    => __NAMESPACE__ . '\Part',
        ));
    }
    
    /**
     * routeFromArray(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::routeFromArray()
     * @param  mixed $specs
     * @return Route
     */
    protected function routeFromArray($specs)
    {
        $route = parent::routeFromArray($specs);
        
        if (isset($specs['routes'])) {      
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate']),
                'child_routes'  => $specs['routes'],
                'plugin_broker' => $this->pluginBroker,
            );

            $route = $this->pluginBroker->load('part', $options);
        }

        return $route;
    }
}
