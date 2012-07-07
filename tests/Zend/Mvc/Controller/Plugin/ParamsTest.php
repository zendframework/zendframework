<?php

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch,
    ZendTest\Mvc\Controller\TestAsset\SampleController;

class ParamsTest extends TestCase
{
    public function setUp()
    {
        $this->request = new Request;
        $event         = new MvcEvent;

        $event->setRequest($this->request);
        $event->setResponse(new Response());
        $event->setRouteMatch(new RouteMatch(array('value' => 'rm:1234')));

        $this->controller = new SampleController();
        $this->controller->setEvent($event);

        $this->plugin = $this->controller->plugin('params');
    }

    public function testFromRouteIsDefault()
    {
        $value = $this->plugin->__invoke('value');
        $this->assertEquals($value, 'rm:1234');
    }

    public function testFromRouteReturnsDefaultIfSet()
    {
        $value = $this->plugin->fromRoute('foo', 'bar');
        $this->assertEquals($value, 'bar');
    }

    public function testFromRouteReturnsExpectedValue()
    {
        $value = $this->plugin->fromRoute('value');
        $this->assertEquals($value, 'rm:1234');
    }

    public function testFromQueryReturnsDefaultIfSet()
    {
        $this->setQuery();

        $value = $this->plugin->fromQuery('foo', 'bar');
        $this->assertEquals($value, 'bar');
    }

    public function testFromQueryReturnsExpectedValue()
    {
        $this->setQuery();

        $value = $this->plugin->fromQuery('value');
        $this->assertEquals($value, 'query:1234');
    }

    public function testFromPostReturnsDefaultIfSet()
    {
        $this->setPost();

        $value = $this->plugin->fromPost('foo', 'bar');
        $this->assertEquals($value, 'bar');
    }

    public function testFromPostReturnsExpectedValue()
    {
        $this->setPost();

        $value = $this->plugin->fromPost('value');
        $this->assertEquals($value, 'post:1234');
    }

    public function testInvokeWithNoArgumentsReturnsInstance()
    {
        $this->assertSame($this->plugin, $this->plugin->__invoke());
    }

    protected function setQuery()
    {
        $this->request->setMethod(Request::METHOD_GET);
        $this->request->getQuery()->set('value', 'query:1234');

        $this->controller->dispatch($this->request);
    }

    protected function setPost()
    {
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getPost()->set('value', 'post:1234');

        $this->controller->dispatch($this->request);
    }
}
