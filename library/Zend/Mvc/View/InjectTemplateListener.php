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

class InjectTemplateListener implements ListenerAggregate
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
        $this->listeners[] = $events->attach('dispatch', array($this, 'injectTemplate'), -90);
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
     * Inject a template into the view model, if none present
     *
     * Template is derived from the controller found in the route match, and, 
     * optionally, the action, if present.
     *
     * @param  MvcEvent $e 
     * @return void
     */
    public function injectTemplate(MvcEvent $e)
    {
        $model = $e->getResult();
        if (!$model instanceof ViewModel) {
            return;
        }
        
        $template = $model->getTemplate();
        if (!empty($template)) {
            return;
        }

        $routeMatch = $e->getRouteMatch();
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
