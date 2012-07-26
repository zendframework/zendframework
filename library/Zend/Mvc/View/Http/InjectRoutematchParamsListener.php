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
 * @package    Zend_Mvc
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\View\Http;

use Zend\EventManager\EventCollection;
use Zend\EventManager\ListenerAggregate;
use Zend\Http\Request as HttpRequest;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\View\Model as ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InjectRoutematchParamsListener implements ListenerAggregate
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Should request params overwrite existing request params?
     *
     * @var bool
     */
    protected $overwrite = true;

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventCollection $events
     * @return void
     */
    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('dispatch', array($this, 'injectParams'), 90);
    }

    /**
     * Detach listeners
     *
     * @param  EventCollection $events
     * @return void
     */
    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Take parameters from RouteMatch and inject them into the request.
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function injectParams(MvcEvent $e)
    {
        $routeMatchParams = $e->getRouteMatch()->getParams();
        $request = $e->getRequest();

        /** @var $params \Zend\Stdlib\Parameters */
        if($request instanceof ConsoleRequest){
            $params = $request->params();
        }elseif($request instanceof HttpRequest){
            $params = $request->get();
        }else{
            // unsupported request type
            return;
        }

        if($this->overwrite){
            foreach($routeMatchParams as $key=>$val){
                $params->$key = $val;
            }
        }else{
            foreach($routeMatchParams as $key=>$val){
                if(!$params->offsetExists($key)){
                    $params->$key = $val;
                }
            }
        }
    }

    /**
     * Should RouteMatch parameters replace existing Request params?
     *
     * @param boolean $overwrite
     */
    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }

    /**
     * @return boolean
     */
    public function getOverwrite()
    {
        return $this->overwrite;
    }
}
