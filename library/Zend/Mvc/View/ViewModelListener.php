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

namespace Zend\Mvc\View;

use Zend\EventManager\EventCollection as Events,
    Zend\EventManager\ListenerAggregate,
    Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch,
    Zend\View\Model as ViewModel;

class ViewModelListener implements ListenerAggregate
{
    /**
     * Filter/inflector used to normalize names for use as template identifiers
     * 
     * @var mixed
     */
    protected $inflector;

    /**
     * Listeners we've registered
     * 
     * @var array
     */
    protected $listeners = array();

    /**
     * Attach listeners
     * 
     * @param  Events $events 
     * @return void
     */
    public function attach(Events $events)
    {
        $this->listeners[] = $events->attach('dispatch', array($this, 'insertViewModel'), -100);
    }

    /**
     * Detach listeners
     * 
     * @param  Events $events 
     * @return void
     */
    public function detach(Events $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Insert the view model into the event
     *
     * Inspects the MVC result; if it is a view model, it then either (a) adds 
     * it as a child to the default, composed view model, or (b) replaces it, 
     * if the result  is marked as terminable.
     * 
     * @param  MvcEvent $e 
     * @return void
     */
    public function insertViewModel(MvcEvent $e)
    {
        $result = $e->getResult();
        if (!$result instanceof ViewModel) {
            return;
        }

        $this->injectTemplate($e->getRouteMatch(), $result);

        $model = $e->getViewModel();

        if ($result->terminate()) {
            $e->setViewModel($result);
            return;
        }

        $model->addChild($result);
    }

    /**
     * Inject template into view model
     *
     * If a template is already present, do nothing. Otherwise, create a 
     * template identifier based on the controller in the RouteMatch, and, if
     * present, the action.
     * 
     * @param  RouteMatch $routeMatch 
     * @param  ViewModel $model 
     * @return void
     */
    protected function injectTemplate(RouteMatch $routeMatch, ViewModel $model)
    {
        $template = $model->getTemplate();
        if (!empty($template)) {
            return;
        }

        $controller = $routeMatch->getParam('controller', 'index');
        $controller = $this->deriveControllerClass($controller);
        $template   = $this->inflectName($controller);

        $action     = $routeMatch->getParam('action');
        if (null !== $action) {
            $template .= '/' . $this->inflectName($action);
        }
        $model->setTemplate($template);
    }

    /**
     * Inflect a name to a normalized value
     * 
     * @param  string $name 
     * @return string
     */
    protected function inflectName($name)
    {
        if (!$this->inflector) {
            $this->inflector = new CamelCaseToDashFilter();
        }
        $name = $this->inflector->filter($name);
        return strtolower($name);
    }

    /**
     * Determine the name of the controller
     *
     * Strip the namespace, and the suffix "Controller" if present.
     * 
     * @param  string $controller 
     * @return string
     */
    protected function deriveControllerClass($controller)
    {
        if (strstr($controller, '\\')) {
            $controller = substr($controller, strrpos($controller, '\\') + 1);
        }

        if ((10 < strlen($controller)) 
            && ('Controller' == substr($controller, -10))
        ) {
            $controller = substr($controller, 0, -10);
        }

        return $controller;
    }
}
