<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\View\Http;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface as Events;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\View\Model\ModelInterface as ViewModel;

class InjectTemplateListener extends AbstractListenerAggregate
{
    /**
     * FilterInterface/inflector used to normalize names for use as template identifiers
     *
     * @var mixed
     */
    protected $inflector;

    /**
     * Array of controller namespace -> template mappings
     *
     * @var array
     */
    protected $controllerMap = array();

    /**
     * {@inheritDoc}
     */
    public function attach(Events $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'injectTemplate'), -90);
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
        $controller = $e->getTarget();
        if (is_object($controller)) {
            $controller = get_class($controller);
        }
        if (!$controller) {
            $controller = $routeMatch->getParam('controller', '');
        }

        $template = $this->mapController($controller);
        if (!$template) {
            $module     = $this->deriveModuleNamespace($controller);

            if ($namespace = $routeMatch->getParam(ModuleRouteListener::MODULE_NAMESPACE)) {
                $controllerSubNs = $this->deriveControllerSubNamespace($namespace);
                if (!empty($controllerSubNs)) {
                    if (!empty($module)) {
                        $module .= '/' . $controllerSubNs;
                    } else {
                        $module = $controllerSubNs;
                    }
                }
            }

            $controller = $this->deriveControllerClass($controller);
            $template   = $this->inflectName($module);

            if (!empty($template)) {
                $template .= '/';
            }
            $template  .= $this->inflectName($controller);
        }

        $action     = $routeMatch->getParam('action');
        if (null !== $action) {
            $template .= '/' . $this->inflectName($action);
        }
        $model->setTemplate($template);
    }

    public function setControllerMap(array $map)
    {
        krsort($map);
        $this->controllerMap = $map;
    }

    public function mapController($controller)
    {
        foreach ($this->controllerMap as $rule => $map) {
            if (
                false == $map
                || !($controller === $rule || strpos($controller, $rule . '\\') === 0)
            ) {
                continue;
            }

            if (is_string($map)) {
                $map = rtrim($map, '/') . '/';
                $controller = trim(substr($controller, strlen($rule) + 1), '\\');
            } else {
                $map = '';
            }

            $parts = explode('\\', $controller);
            $parts = array_diff($parts, array('Controller'));
            array_pop($parts);
            $parts[] = $this->deriveControllerClass($controller);

            $template = $map . implode('/', $parts);
            $template = trim($template, '/');
            $template =  $this->inflectName($template);
            return $template;
        }
        return false;
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
     * Determine the top-level namespace of the controller
     *
     * @param  string $controller
     * @return string
     */
    protected function deriveModuleNamespace($controller)
    {
        if (!strstr($controller, '\\')) {
            return '';
        }
        $module = substr($controller, 0, strpos($controller, '\\'));
        return $module;
    }

    /**
     * @param $namespace
     * @return string
     */
    protected function deriveControllerSubNamespace($namespace)
    {
        if (!strstr($namespace, '\\')) {
            return '';
        }
        $nsArray = explode('\\', $namespace);

        // Remove the first two elements representing the module and controller directory.
        $subNsArray = array_slice($nsArray, 2);
        if (empty($subNsArray)) {
            return '';
        }
        return implode('/', $subNsArray);
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
