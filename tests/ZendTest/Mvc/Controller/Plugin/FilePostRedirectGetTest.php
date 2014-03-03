<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Collection;
use Zend\Form\Form;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal as LiteralRoute;
use Zend\Mvc\Router\Http\Segment as SegmentRoute;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Stdlib\Parameters;
use ZendTest\Mvc\Controller\TestAsset\SampleController;
use ZendTest\Session\TestAsset\TestManager as SessionManager;

class FilePostRedirectGetTest extends TestCase
{
    public $form;
    public $controller;
    public $event;
    public $request;
    public $response;
    public $collection;

    public function setUp()
    {
        $this->form = new Form();

        $this->collection = new Collection('links',array(
                'count' => 1,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'ZendTest\Mvc\Controller\Plugin\TestAsset\LinksFieldset',
                ),
        ));

        $router = new SimpleRouteStack;
        $router->addRoute('home', LiteralRoute::factory(array(
            'route'    => '/',
            'defaults' => array(
                'controller' => 'ZendTest\Mvc\Controller\TestAsset\SampleController',
            )
        )));

        $router->addRoute('sub', SegmentRoute::factory(array(
            'route' => '/foo/:param',
            'defaults' => array(
                'param' => 1
            )
        )));

        $router->addRoute('ctl', SegmentRoute::factory(array(
            'route' => '/ctl/:controller',
            'defaults' => array(
                '__NAMESPACE__' => 'ZendTest\Mvc\Controller\TestAsset',
            )
        )));

        $this->controller = new SampleController();
        $this->request    = new Request();
        $this->event      = new MvcEvent();
        $this->routeMatch = new RouteMatch(array('controller' => 'controller-sample', 'action' => 'postPage'));

        $this->event->setRequest($this->request);
        $this->event->setRouteMatch($this->routeMatch);
        $this->event->setRouter($router);

        $this->sessionManager = new SessionManager();
        $this->sessionManager->destroy();

        $this->controller->setEvent($this->event);
        $this->controller->flashMessenger()->setSessionManager($this->sessionManager);
    }

    public function testReturnsFalseOnIntialGet()
    {
        $result    = $this->controller->dispatch($this->request, $this->response);
        $prgResult = $this->controller->fileprg($this->form, 'home');

        $this->assertFalse($prgResult);
    }

    public function testRedirectsToUrlOnPost()
    {
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value'
        )));

        $this->controller->dispatch($this->request, $this->response);
        $prgResultUrl = $this->controller->fileprg($this->form, '/test/getPage', true);

        $this->assertInstanceOf('Zend\Http\Response', $prgResultUrl);
        $this->assertTrue($prgResultUrl->getHeaders()->has('Location'));
        $this->assertEquals('/test/getPage', $prgResultUrl->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultUrl->getStatusCode());
    }

    public function testRedirectsToRouteOnPost()
    {
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value1'
        )));

        $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->fileprg($this->form, 'home');

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals('/', $prgResultRoute->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }

    /**
     * @expectedException Zend\Mvc\Exception\RuntimeException
     */
    public function testThrowsExceptionOnRouteWithoutRouter()
    {
        $controller = $this->controller;
        $controller = $controller->getEvent()->setRouter(new SimpleRouteStack);

        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value'
        )));

        $this->controller->dispatch($this->request, $this->response);
        $this->controller->fileprg($this->form, 'some/route');
    }

    public function testNullRouteUsesMatchedRouteName()
    {
        $this->controller->getEvent()->getRouteMatch()->setMatchedRouteName('home');

        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value1'
        )));

        $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->fileprg($this->form);

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals('/', $prgResultRoute->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }

    public function testReuseMatchedParameters()
    {
        $this->controller->getEvent()->getRouteMatch()->setMatchedRouteName('sub');

        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value1'
        )));

        $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->fileprg($this->form);

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals('/foo/1', $prgResultRoute->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }

    public function testReturnsPostOnRedirectGet()
    {
        // Do POST
        $params = array(
            'postval1' => 'value'
        );
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters($params));

        $this->form->add(array(
            'name' => 'postval1'
        ));

        $this->controller->dispatch($this->request, $this->response);
        $prgResultUrl = $this->controller->fileprg($this->form, '/test/getPage', true);

        $this->assertInstanceOf('Zend\Http\Response', $prgResultUrl);
        $this->assertTrue($prgResultUrl->getHeaders()->has('Location'));
        $this->assertEquals('/test/getPage', $prgResultUrl->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultUrl->getStatusCode());

        // Do GET
        $this->request = new Request();
        $this->controller->dispatch($this->request, $this->response);
        $prgResult = $this->controller->fileprg($this->form, '/test/getPage', true);

        $this->assertEquals($params, $prgResult);
        $this->assertEquals($params['postval1'], $this->form->get('postval1')->getValue());

        // Do GET again to make sure data is empty
        $this->request = new Request();
        $this->controller->dispatch($this->request, $this->response);
        $prgResult = $this->controller->fileprg($this->form, '/test/getPage', true);

        $this->assertFalse($prgResult);
    }

    public function testAppliesFormErrorsOnPostRedirectGet()
    {
        // Do POST
        $params = array();
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters($params));

        $this->form->add(array(
            'name' => 'postval1'
        ));
        $inputFilter = new InputFilter();
        $inputFilter->add(array(
            'name'     => 'postval1',
            'required' => true,
        ));
        $this->form->setInputFilter($inputFilter);

        $this->controller->dispatch($this->request, $this->response);
        $prgResultUrl = $this->controller->fileprg($this->form, '/test/getPage', true);
        $this->assertInstanceOf('Zend\Http\Response', $prgResultUrl);
        $this->assertTrue($prgResultUrl->getHeaders()->has('Location'));
        $this->assertEquals('/test/getPage', $prgResultUrl->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultUrl->getStatusCode());

        // Do GET
        $this->request = new Request();
        $this->controller->dispatch($this->request, $this->response);
        $prgResult = $this->controller->fileprg($this->form, '/test/getPage', true);
        $messages  = $this->form->getMessages();

        $this->assertEquals($params, $prgResult);
        $this->assertNotEmpty($messages['postval1']['isEmpty']);
    }

    public function testReuseMatchedParametersWithSegmentController()
    {
        $expects = '/ctl/sample';
        $this->request->setMethod('POST');
        $this->request->setUri($expects);
        $this->request->setPost(new Parameters(array(
            'postval1' => 'value1'
        )));

        $routeMatch = $this->event->getRouter()->match($this->request);
        $this->event->setRouteMatch($routeMatch);

        $moduleRouteListener = new ModuleRouteListener;
        $moduleRouteListener->onRoute($this->event);

        $this->controller->dispatch($this->request, $this->response);
        $prgResultRoute = $this->controller->fileprg($this->form);

        $this->assertInstanceOf('Zend\Http\Response', $prgResultRoute);
        $this->assertTrue($prgResultRoute->getHeaders()->has('Location'));
        $this->assertEquals($expects, $prgResultRoute->getHeaders()->get('Location')->getUri() , 'redirect to the same url');
        $this->assertEquals(303, $prgResultRoute->getStatusCode());
    }

    public function testFieldsetAmountInFormEqualsFieldsetsInInputFilter()
    {
        // POST
        $url = '/';
        $params = array(
            'links' => array(
                '0' => array(
                    'foobar' => 'val',
                ),
                '1' => array(
                    'foobar' => 'val',
                ),
            ),
        );
        $this->request->setMethod('POST');
        $this->request->setPost(new Parameters($params));
        $this->request->setUri($url);

        $this->form->add($this->collection);

        $routeMatch = $this->event->getRouter()->match($this->request);
        $this->event->setRouteMatch($routeMatch);

        $this->controller->dispatch($this->request, $this->response);
        $prgResultUrl = $this->controller->fileprg($this->form);

        $this->assertInstanceOf('Zend\Http\Response', $prgResultUrl);
        $this->assertTrue($prgResultUrl->getHeaders()->has('Location'));
        $this->assertEquals('/', $prgResultUrl->getHeaders()->get('Location')->getUri());
        $this->assertEquals(303, $prgResultUrl->getStatusCode());

        $this->assertCount(count($params['links']),  $this->form->get('links')->getFieldsets());
        $this->assertCount(count($this->form->get('links')->getFieldsets()),  $this->form->getInputFilter()->get('links')->getInputs());

        // GET
        $this->request = new Request();
        $form = new Form();
        $collection = new Collection('links',array(
            'count' => 1,
            'allow_add' => true,
            'target_element' => array(
                'type' => 'ZendTest\Mvc\Controller\Plugin\TestAsset\LinksFieldset',
            ),
        ));
        $form->add($collection);
        $this->controller->dispatch($this->request, $this->response);
        $prgResult = $this->controller->fileprg( $form);

        $this->assertEquals($params, $prgResult);
        $this->assertCount(count($params['links']),  $form->get('links')->getFieldsets());
        $this->assertCount(count( $form->get('links')->getFieldsets()), $form->getInputFilter()->get('links')->getInputs());
    }
}
