<?php

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_framework_TestCase as TestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\CreateHttpNotFoundModel;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use ZendTest\Mvc\Controller\TestAsset\SampleController;

class CreateHttpNotFoundModelTest extends TestCase
{
    public function setUp()
    {
        $this->request = new Request();
        $event         = new MvcEvent;

        $event->setRequest($this->request);
        $event->setResponse(new Response);
        $event->setRouteMatch(new RouteMatch(array()));

        $this->controller = new SampleController;
        $this->controller->setEvent($event);

        $this->plugin = $this->controller->plugin('createHttpNotFoundModel');
    }

    public function testBuildsModelWithErrorMessageAndSetsResponseStatusCode()
    {
        $response = new Response();
        $plugin   = new CreateHttpNotFoundModel();

        $model    = $plugin->__invoke($response);

        $this->assertInstanceOf('Zend\\View\\Model\\ViewModel', $model);
        $this->assertSame('Page not found', $model->getVariable('content'));
        $this->assertSame(404, $response->getStatusCode());
    }
}
