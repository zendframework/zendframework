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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router\Http;
use Zend\Controller\Router\SimpleRouteStack;
use Zend\Controller\Request;
use Zend\Loader\PluginSpecBroker;

/**
 * Simple route stack implementation.
 *
 * @package    Zend_Controller
 * @subpackage Router
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
            'literal' => __NAMESPACE__ . '\\Route\\Literal',
            'regex'   => __NAMESPACE__ . '\\Route\\Regex',
            'segment' => __NAMESPACE__ . '\\Route\\Segment',
            'part'    => __NAMESPACE__ . '\\Route\\Part',
        ));
    }
    
    /**
     * routeFromArray(): defined by SimpleRouteStack.
     *
     * @see    SimpleRouteStack::routeFromArray()
     * @param  array $specs
     * @return Route
     */
    protected function routeFromArray(array $specs)
    {
        $route = parent::routeFromArray($specs);
        
        if (isset($specs['routes'])) {
            $options = array(
                'route'         => $route,
                'may_terminate' => (isset($specs['may_terminate']) && $specs['may_terminate'])
            );

            $route = $this->pluginBroker->load('part', $options);

            foreach ($specs['routes'] as $subName => $subSpecs) {
                $route->append($subName, $this->routeFromArray($subSpecs));
            }
        }

        return $route;
    }
}
