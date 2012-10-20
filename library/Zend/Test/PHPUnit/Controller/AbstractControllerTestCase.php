<?php

namespace Zend\Test\PHPUnit\Controller;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_ExpectationFailedException;
use Zend\Test\PHPUnit\Mvc\View\CaptureResponseListener;
use Zend\Mvc\Application;
use Zend\ModuleManager\ModuleEvent;
use Zend\Dom;

class AbstractControllerTestCase extends PHPUnit_Framework_TestCase
{
    private $application;
    private $applicationConfig;

    protected $useConsoleRequest = false;

    public function setUseConsoleRequest($boolean)
    {
        $this->useConsoleRequest = (boolean)$boolean;
    }

    public function setApplicationConfig($applicationConfig)
    {
        $this->applicationConfig = $applicationConfig;
    }

    /**
     * Get the application object
     * @return Zend\Mvc\ApplicationInterface
     */
    public function getApplication()
    {
        if(null === $this->application) {
            $appConfig = $this->applicationConfig;
            if(!$this->useConsoleRequest) {
                $consoleServiceConfig = array(
                    'service_manager' => array(
                        'factories' => array(
                            'ServiceListener' => 'Zend\Test\PHPUnit\Mvc\Service\ServiceListenerFactory',
                        ),
                    ),
                );
                $appConfig = array_replace_recursive($appConfig, $consoleServiceConfig);
            }
            $this->application = Application::init($appConfig);
            $events = $this->application->getEventManager();
            $events->attach(new CaptureResponseListener);
        }
        return $this->application;
    }

    /**
     * Get the service manager of the application object
     * @return Zend\ServiceManager\ServiceManager
     */
    public function getApplicationServiceLocator()
    {
        return $this->getApplication()->getServiceManager();
    }
    
    /**
     * Get the request object
     * @return \Zend\Stdlib\RequestInterface
     */
    public function getRequest()
    {
        return $this->getApplication()->getRequest();
    }

    public function dispatch($url)
    {
        $request = $this->getRequest();
        if($this->useConsoleRequest) {
            $params = preg_split('#\s+#', $url);
            $request->params()->exchangeArray($params);
        } else {
            $request->setUri('http://localhost' . $url);
            $request->setBaseUrl('');
        }
        $this->getApplication()->run();
    }

    public function assertModulesLoaded(array $modules)
    {
        $moduleManager = $this->getApplicationServiceLocator()->get('ModuleManager');
        $modulesLoaded = $moduleManager->getModules();
        $list = array_diff($modules, $modulesLoaded);
        if($list) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Several modules are not loaded "%s"', implode(', ', $list)
            ));
        }
        $this->assertEquals(count($list), 0);
    }

    public function assertNotModulesLoaded(array $modules)
    {
        $moduleManager = $this->getApplicationServiceLocator()->get('ModuleManager');
        $modulesLoaded = $moduleManager->getModules();
        $list = array_intersect($modules, $modulesLoaded);
        if($list) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Several modules WAS not loaded "%s"', implode(', ', $list)
            ));
        }
        $this->assertEquals(count($list), 0);
    }

    protected function getResponseStatusCode()
    {
        $response = $this->getApplication()->getResponse();
        if($this->useConsoleRequest) {
            $match = $response->getErrorLevel();
            if(null === $match) {
                $match = 0;
            }
            return $match;
        }
        return $response->getStatusCode();
    }

    public function assertResponseStatusCode($code)
    {
        if($this->useConsoleRequest) {
            if(!in_array($code, array(0, 1))) {
                throw new PHPUnit_Framework_ExpectationFailedException(
                    'Console status code assert value must be O (valid) or 1 (error)'
                );
            }
        }
        $match = $this->getResponseStatusCode();
        if($code != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response code "%s", actual status code is "%s"',
                $code,
                $match
            ));
        }
        $this->assertEquals($code, $match);
    }

    public function assertNotResponseStatusCode($code)
    {
        if($this->useConsoleRequest) {
            if(!in_array($code, array(0, 1))) {
                throw new PHPUnit_Framework_ExpectationFailedException(
                    'Console status code assert value must be O (valid) or 1 (error)'
                );
            }
        }
        $match = $this->getResponseStatusCode();
        if($code == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response code was NOT "%s"',
                $code
            ));
        }
        $this->assertNotEquals($code, $match);
    }

    protected function getControllerFullClassName()
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $controllerIdentifier = $routeMatch->getParam('controller');
        $controllerManager = $this->getApplicationServiceLocator()->get('ControllerLoader');
        $controllerClass = $controllerManager->get($controllerIdentifier);
        return get_class($controllerClass);
    }

    public function assertModule($module)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $match = strtolower($match);
        $module = strtolower($module);
        if($module != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting module "%s", actual module is "%s"',
                $module,
                $match
            ));
        }
        $this->assertEquals($module, $match);
    }

    public function assertNotModule($module)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $match = strtolower($match);
        $module = strtolower($module);
        if($module == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting module was NOT "%s"',
                $module
            ));
        }
        $this->assertNotEquals($module, $match);
    }

    public function assertControllerClass($controller)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match = substr($controllerClass, strrpos($controllerClass, '\\')+1);
        $match = strtolower($match);
        $controller = strtolower($controller);
        if($controller != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting controller class "%s", actual controller class is "%s"',
                $controller,
                $match
            ));
        }
        $this->assertEquals($controller, $match);
    }

    public function assertNotControllerClass($controller)
    {
        $controllerClass = $this->getControllerFullClassName();
        $match = substr($controllerClass, strrpos($controllerClass, '\\')+1);
        $match = strtolower($match);
        $controller = strtolower($controller);
        if($controller == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting controller class was NOT "%s"',
                $controller
            ));
        }
        $this->assertNotEquals($controller, $match);
    }

    public function assertControllerName($controller)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getParam('controller');
        $match = strtolower($match);
        $controller = strtolower($controller);
        if($controller != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting controller name "%s", actual controller is "%s"',
                $controller,
                $match
            ));
        }
        $this->assertEquals($controller, $match);
    }

    public function assertNotControllerName($controller)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getParam('controller');
        $match = strtolower($match);
        $controller = strtolower($controller);
        if($controller == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting controller name was NOT "%s"',
                $controller
            ));
        }
        $this->assertNotEquals($controller, $match);
    }

    public function assertActionName($action)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getParam('action');
        $match = strtolower($match);
        $action = strtolower($action);
        if($action != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting action name "%s", actual action is "%s"',
                $action,
                $match
            ));
        }
        $this->assertEquals($action, $match);
    }

    public function assertNotActionName($action)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getParam('action');
        $match = strtolower($match);
        $action = strtolower($action);
        if($action == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting action name was NOT "%s"',
                $action
            ));
        }
        $this->assertNotEquals($action, $match);
    }

    public function assertMatchedRouteName($route)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getMatchedRouteName();
        $match = strtolower($match);
        $route = strtolower($route);
        if($route != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting matched route was "%s", actual route is "%s"',
                $route,
                $match
            ));
        }
        $this->assertEquals($route, $match);
    }

    public function assertNotMatchedRouteName($route)
    {
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $match = $routeMatch->getMatchedRouteName();
        $match = strtolower($match);
        $route = strtolower($route);
        if($route == $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting route matched was NOT "%s"', $route
            ));
        }
        $this->assertNotEquals($route, $match);
    }

    protected function query($path)
    {
        $response = $this->getApplication()->getResponse();
        $dom = new Dom\Query($response->getContent());
        $result = $dom->execute($path);
        return count($result);
    }

    public function assertQuery($path)
    {
        $match = $this->query($path);
        if(!$match > 0) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s EXISTS', $path
            ));
        }
        $this->assertEquals(true, $match > 0);
    }

    public function assertNotQuery($path)
    {
        $match = $this->query($path);
        if($match != 0) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s DOES NOT EXIST', $path
            ));
        }
        $this->assertEquals(0, $match);
    }

    public function assertQueryCount($path, $count)
    {
        $match = $this->query($path);
        if($match != $count) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s OCCURS EXACTLY %d times',
                $path, $count
            ));
        }
        $this->assertEquals($match, $count);
    }

    public function assertQueryCountMin($path, $count)
    {
        $match = $this->query($path);
        if($match < $count) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s OCCURS AT LEAST %d times',
                $path, $count
            ));
        }
        $this->assertEquals(true, $match >= $count);
    }

    public function assertQueryCountMax($path, $count)
    {
        $match = $this->query($path);
        if($match > $count) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting node DENOTED BY %s OCCURS AT MOST %d times',
                $path, $count
            ));
        }
        $this->assertEquals(true, $match <= $count);
    }
}
