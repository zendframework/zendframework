<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\ViewEvent;

class ViewEventTest extends TestCase
{
    protected $event;

    public function setUp()
    {
        $this->event = new ViewEvent;
    }

    public function testModelIsNullByDefault()
    {
        $this->assertNull($this->event->getModel());
    }

    public function testRendererIsNullByDefault()
    {
        $this->assertNull($this->event->getRenderer());
    }

    public function testRequestIsNullByDefault()
    {
        $this->assertNull($this->event->getRequest());
    }

    public function testResponseIsNullByDefault()
    {
        $this->assertNull($this->event->getResponse());
    }

    public function testResultIsNullByDefault()
    {
        $this->assertNull($this->event->getResult());
    }

    public function testModelIsMutable()
    {
        $model = new ViewModel();
        $this->event->setModel($model);
        $this->assertSame($model, $this->event->getModel());
    }

    public function testRendererIsMutable()
    {
        $renderer = new PhpRenderer();
        $this->event->setRenderer($renderer);
        $this->assertSame($renderer, $this->event->getRenderer());
    }

    public function testRequestIsMutable()
    {
        $request = new Request();
        $this->event->setRequest($request);
        $this->assertSame($request, $this->event->getRequest());
    }

    public function testResponseIsMutable()
    {
        $response = new Response();
        $this->event->setResponse($response);
        $this->assertSame($response, $this->event->getResponse());
    }

    public function testResultIsMutable()
    {
        $result = 'some result';
        $this->event->setResult($result);
        $this->assertSame($result, $this->event->getResult());
    }

    public function testModelIsMutableViaSetParam()
    {
        $model = new ViewModel();
        $this->event->setParam('model', $model);
        $this->assertSame($model, $this->event->getModel());
        $this->assertSame($model, $this->event->getParam('model'));
    }

    public function testRendererIsMutableViaSetParam()
    {
        $renderer = new PhpRenderer();
        $this->event->setParam('renderer', $renderer);
        $this->assertSame($renderer, $this->event->getRenderer());
        $this->assertSame($renderer, $this->event->getParam('renderer'));
    }

    public function testRequestIsMutableViaSetParam()
    {
        $request = new Request();
        $this->event->setParam('request', $request);
        $this->assertSame($request, $this->event->getRequest());
        $this->assertSame($request, $this->event->getParam('request'));
    }

    public function testResponseIsMutableViaSetParam()
    {
        $response = new Response();
        $this->event->setParam('response', $response);
        $this->assertSame($response, $this->event->getResponse());
        $this->assertSame($response, $this->event->getParam('response'));
    }

    public function testResultIsMutableViaSetParam()
    {
        $result = 'some result';
        $this->event->setParam('result', $result);
        $this->assertSame($result, $this->event->getResult());
        $this->assertSame($result, $this->event->getParam('result'));
    }

    public function testSpecializedParametersMayBeSetViaSetParams()
    {
        $model    = new ViewModel();
        $renderer = new PhpRenderer();
        $request  = new Request();
        $response = new Response();
        $result   = 'some result';

        $params   = array(
            'model'    => $model,
            'renderer' => $renderer,
            'request'  => $request,
            'response' => $response,
            'result'   => $result,
            'otherkey' => 'other value',
        );

        $this->event->setParams($params);
        $this->assertEquals($params, $this->event->getParams());

        $this->assertSame($params['model'], $this->event->getModel());
        $this->assertSame($params['model'], $this->event->getParam('model'));

        $this->assertSame($params['renderer'], $this->event->getRenderer());
        $this->assertSame($params['renderer'], $this->event->getParam('renderer'));

        $this->assertSame($params['request'], $this->event->getRequest());
        $this->assertSame($params['request'], $this->event->getParam('request'));

        $this->assertSame($params['response'], $this->event->getResponse());
        $this->assertSame($params['response'], $this->event->getParam('response'));

        $this->assertSame($params['result'], $this->event->getResult());
        $this->assertSame($params['result'], $this->event->getParam('result'));

        $this->assertEquals($params['otherkey'], $this->event->getParam('otherkey'));
    }
}
