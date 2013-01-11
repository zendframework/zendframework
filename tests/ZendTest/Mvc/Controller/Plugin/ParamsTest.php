<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use ZendTest\Mvc\Controller\TestAsset\SampleController;

class ParamsTest extends TestCase
{
    public function setUp()
    {
        $this->request = new Request;
        $event         = new MvcEvent;

        $event->setRequest($this->request);
        $event->setResponse(new Response());
        $event->setRouteMatch(new RouteMatch(array(
            'value' => 'rm:1234',
            'other' => '1234:rm',
        )));

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

    public function testFromRouteNotReturnsExpectedValueWithDefault()
    {
        $value = $this->plugin->fromRoute('value', 'default');
        $this->assertEquals($value, 'rm:1234');
    }

    public function testFromRouteReturnsAllIfEmpty()
    {
        $value = $this->plugin->fromRoute();
        $this->assertEquals($value, array('value' => 'rm:1234', 'other' => '1234:rm'));
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

    public function testFromQueryReturnsExpectedValueWithDefault()
    {
        $this->setQuery();

        $value = $this->plugin->fromQuery('value', 'default');
        $this->assertEquals($value, 'query:1234');
    }

    public function testFromQueryReturnsAllIfEmpty()
    {
        $this->setQuery();

        $value = $this->plugin->fromQuery();
        $this->assertEquals($value, array('value' => 'query:1234', 'other' => '1234:other'));
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

    public function testFromPostReturnsExpectedValueWithDefault()
    {
        $this->setPost();

        $value = $this->plugin->fromPost('value', 'default');
        $this->assertEquals($value, 'post:1234');
    }

    public function testFromPostReturnsAllIfEmpty()
    {
        $this->setPost();

        $value = $this->plugin->fromPost();
        $this->assertEquals($value, array('value' => 'post:1234', 'other' => '2345:other'));
    }

    public function testFromFilesReturnsExpectedValue()
    {
        $file = array(
            'name'     => 'test.txt',
            'type'     => 'text/plain',
            'size'     => 0,
            'tmp_name' => '/tmp/' . uniqid(),
            'error'    => UPLOAD_ERR_OK,
        );
        $this->request->getFiles()->set('test', $file);
        $this->controller->dispatch($this->request);

        $value = $this->plugin->fromFiles('test');
        $this->assertEquals($value, $file);
    }

    public function testFromFilesReturnsAllIfEmpty()
    {
        $file = array(
            'name'     => 'test.txt',
            'type'     => 'text/plain',
            'size'     => 0,
            'tmp_name' => '/tmp/' . uniqid(),
            'error'    => UPLOAD_ERR_OK,
        );

        $file2 = array(
            'name'     => 'file2.txt',
            'type'     => 'text/plain',
            'size'     => 1,
            'tmp_name' => '/tmp/' . uniqid(),
            'error'    => UPLOAD_ERR_OK,
        );
        $this->request->getFiles()->set('file', $file);
        $this->request->getFiles()->set('file2', $file2);
        $this->controller->dispatch($this->request);

        $value = $this->plugin->fromFiles();
        $this->assertEquals($value, array('file' => $file, 'file2' => $file2));
    }

    public function testFromHeaderReturnsExpectedValue()
    {
        $header = new GenericHeader('X-TEST', 'test');
        $this->request->getHeaders()->addHeader($header);
        $this->controller->dispatch($this->request);

        $value = $this->plugin->fromHeader('X-TEST');
        $this->assertSame($value, $header);
    }

    public function testFromHeaderReturnsAllIfEmpty()
    {
        $header = new GenericHeader('X-TEST', 'test');
        $header2 = new GenericHeader('OTHER-TEST', 'value:12345');

        $this->request->getHeaders()->addHeader($header);
        $this->request->getHeaders()->addHeader($header2);

        $this->controller->dispatch($this->request);

        $value = $this->plugin->fromHeader();
        $this->assertSame($value, array('X-TEST' => 'test', 'OTHER-TEST' => 'value:12345'));
    }

    public function testInvokeWithNoArgumentsReturnsInstance()
    {
        $this->assertSame($this->plugin, $this->plugin->__invoke());
    }

    protected function setQuery()
    {
        $this->request->setMethod(Request::METHOD_GET);
        $this->request->getQuery()->set('value', 'query:1234');
        $this->request->getQuery()->set('other', '1234:other');

        $this->controller->dispatch($this->request);
    }

    protected function setPost()
    {
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getPost()->set('value', 'post:1234');
        $this->request->getPost()->set('other', '2345:other');

        $this->controller->dispatch($this->request);
    }
}
