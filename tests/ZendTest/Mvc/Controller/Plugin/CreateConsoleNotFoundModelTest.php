<?php

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_framework_TestCase as TestCase;
use Zend\Console\Request;
use Zend\Console\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Console\RouteMatch;
use ZendTest\Mvc\Controller\TestAsset\SampleController;

class CreateConsoleNotFoundModelTest extends TestCase
{
    public function setUp()
    {
        $this->request = new Request;
        $event         = new MvcEvent;

        $event->setRequest($this->request);
        $event->setResponse(new Response);
        $event->setRouteMatch(new RouteMatch(array()));

        $this->controller = new SampleController;
        $this->controller->setEvent($event);

        $this->plugin = $this->controller->plugin('createConsoleNotFoundModel');
    }

    public function testIfCanReturnModelWithErrorMessageAndErrorLevel()
    {
        $response = $this->controller->getEvent()->getResponse();
        $model    = $this->plugin->__invoke($response);

        $this->assertInstanceOf('Zend\View\Model\ConsoleModel', $model);
        $this->assertSame("Page not found", $model->getResult());
        $this->assertSame(1, $model->getErrorLevel());
    }
}
