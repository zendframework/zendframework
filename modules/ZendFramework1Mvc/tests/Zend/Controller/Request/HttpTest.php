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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Request;
use Zend\Controller\Request;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Request
 */
class HTTPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Controller_Request_HTTP
     */
    protected $_request;

    /**
     * Original $_SERVER
     * @var array
     */
    protected $_origServer;

    public function setUp()
    {
        $this->_origServer = $_SERVER;
        $_GET  = array();
        $_POST = array();
        $_SERVER = array(
            'SCRIPT_FILENAME' => __FILE__,
            'PHP_SELF'        => __FILE__,
        );
        $this->_request = new Request\Http('http://framework.zend.com/news/3?var1=val1&var2=val2#anchor');
    }

    public function tearDown()
    {
        unset($this->_request);
        $_SERVER = $this->_origServer;
    }

    public function testSetGetControllerKey()
    {
        $this->_request->setControllerKey('controller');
        $this->assertEquals('controller', $this->_request->getControllerKey());

        $this->_request->setControllerKey('foo');
        $this->assertEquals('foo', $this->_request->getControllerKey());
    }

    public function testSetGetActionKey()
    {
        $this->_request->setActionKey('action');
        $this->assertEquals('action', $this->_request->getActionKey());

        $this->_request->setActionKey('foo');
        $this->assertEquals('foo', $this->_request->getActionKey());
    }

    public function testSetGetControllerName()
    {
        $this->_request->setControllerName('foo');
        $this->assertEquals('foo', $this->_request->getControllerName());

        $this->_request->setControllerName('bar');
        $this->assertEquals('bar', $this->_request->getControllerName());
    }

    public function testSetGetActionName()
    {
        $this->_request->setActionName('foo');
        $this->assertEquals('foo', $this->_request->getActionName());

        $this->_request->setActionName('bar');
        $this->assertEquals('bar', $this->_request->getActionName());
    }

    public function test__Get()
    {
        $_POST['baz']   = 'boo';
        $_COOKIE['bal'] = 'peen';
        $this->_request->setParam('foo', 'bar');

        foreach ($_ENV as $envKey => $expected) {
            if (isset($_ENV[$envKey]) && !empty($_ENV[$envKey])) {
                $expEnvKey = $envKey;
                break;
            }
        }
        $_SERVER['REQUEST_TIME'] = date('Y-m-d H:i:s');

        $this->assertEquals('bar', $this->_request->foo);
        $this->assertEquals('val1', $this->_request->var1);
        $this->assertEquals('boo', $this->_request->baz);
        $this->assertEquals('peen', $this->_request->bal);
        $this->assertEquals($_SERVER['REQUEST_TIME'], $this->_request->REQUEST_TIME);
        $this->assertEquals($this->_request->getPathInfo(), $this->_request->PATH_INFO, $this->_request->PATH_INFO);
        $this->assertEquals($this->_request->getRequestUri(), $this->_request->REQUEST_URI, $this->_request->REQUEST_URI);
        if (isset($expEnvKey)) {
            $this->assertEquals($expected, $this->_request->$expEnvKey);
        }
    }

    public function testGetIsAlias()
    {
        $this->assertEquals('val1', $this->_request->get('var1'));
    }

    public function testSetIsAlias()
    {
        try {
            $this->_request->set('foo', 'bar');
            $this->fail('set() should alias to __set(), and throw an exception');
        } catch (\Exception $e) {
            // success
        }
    }

    public function test__Isset()
    {
        $_POST['baz']   = 'boo';
        $_COOKIE['bal'] = 'peen';
        $this->_request->setParam('foo', 'bar');

        foreach ($_ENV as $envKey => $expected) {
            if (isset($_ENV[$envKey]) && !empty($_ENV[$envKey])) {
                $expEnvKey = $envKey;
                break;
            }
        }
        $_SERVER['REQUEST_TIME'] = date('Y-m-d H:i:s');

        $this->assertTrue(isset($this->_request->foo));
        $this->assertTrue(isset($this->_request->var1));
        $this->assertTrue(isset($this->_request->baz));
        $this->assertTrue(isset($this->_request->bal));
        $this->assertTrue(isset($this->_request->REQUEST_TIME));
        $this->assertFalse(isset($this->_request->bogosity));
        if (isset($expEnvKey)) {
            $this->assertTrue(isset($this->_request->$expEnvKey));
        }
    }

    public function testHasIsAlias()
    {
        $this->assertTrue($this->_request->has('var1'));
    }

    public function test__SetThrowsException()
    {
        try {
            $this->_request->foo = 'bar';
            $this->fail('__set() should throw an exception');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testClearParams()
    {
        $this->_request->setParam('foo', 'bar');
        $this->_request->clearParams();
        $this->assertNull($this->_request->getParam('foo'));
    }

    public function testSetGetParam()
    {
        $this->_request->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_request->getParam('foo'));
    }

    public function testSetGetParams()
    {
        $params = array(
            'foo' => 'bar',
            'boo' => 'bah',
            'fee' => 'fi'
        );
        $this->_request->setParams($params);
        $received = $this->_request->getParams();
        $this->assertSame($params, array_intersect_assoc($params, $received));
    }

    public function testGetParamsWithNoGetOrPost()
    {
        unset($_GET, $_POST);
        $params = array(
            'foo' => 'bar',
            'boo' => 'bah',
            'fee' => 'fi'
        );
        $this->_request->setParams($params);
        $received = $this->_request->getParams();
        $this->assertSame($params, array_intersect_assoc($params, $received));
    }

    public function testGetParamsWithGetAndPost()
    {
        $_GET = array(
            'get' => true
        );
        $_POST = array(
            'post' => true
        );
        $params = array(
            'foo' => 'bar',
            'boo' => 'bah',
            'fee' => 'fi'
        );
        $this->_request->setParams($params);

        $expected = $params + $_GET + $_POST;
        $received = $this->_request->getParams();
        $this->assertSame($params, array_intersect_assoc($params, $received));
    }

    /**
     * @group ZF-3750
     */
    public function testGetParamsWithGetOrPost()
    {
        $_GET = array(
            'get' => true
        );
        $_POST = array(
            'post' => true
        );

        $this->_request->setParamSources(array('_GET'));
        $params = $this->_request->getParams();
        $this->assertArrayHasKey('get', $params);
        $this->assertArrayNotHasKey('post', $params);

        $this->_request->setParamSources(array('_POST'));
        $params = $this->_request->getParams();
        $this->assertArrayHasKey('post', $params);
        $this->assertArrayNotHasKey('get', $params);
    }

    public function testConstructSetsRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/mycontroller/myaction?foo=bar';
        $request = new Request\Http();
        $this->assertEquals('/mycontroller/myaction?foo=bar', $request->getRequestUri());
    }

    public function testSetRequestUriDoesNotPassUriThroughUrldecode()
    {
        $request = new Request\Http();
        $request->setRequestUri('/foo/bar?foo=bar%20baz');
        $requestUri = $request->getRequestUri();
        $this->assertNotEquals('/foo/bar?foo=bar baz', $requestUri);
        $this->assertEquals('/foo/bar?foo=bar%20baz', $requestUri);
    }

    public function testGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('POST', $this->_request->getMethod());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertEquals('GET', $this->_request->getMethod());
    }

    public function testIsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue($this->_request->isPost());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse($this->_request->isPost());
    }

    public function testIsGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue($this->_request->isGet());
        $this->assertFalse($this->_request->isPost());
    }

    public function testIsPut()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $this->assertTrue($this->_request->isPut());
        $this->assertFalse($this->_request->isGet());
    }

    public function testIsDelete()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $this->assertTrue($this->_request->isDelete());
        $this->assertFalse($this->_request->isGet());
    }

    public function testIsHead()
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $this->assertTrue($this->_request->isHead());
        $this->assertFalse($this->_request->isGet());
    }

    public function testIsOptions()
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $this->assertTrue($this->_request->isOptions());
        $this->assertFalse($this->_request->isGet());
    }

    public function testGetRawBodyReturnsFalseWithNoPost()
    {
        \ZendTest\AllTests\StreamWrapper\PHPInput::mockInput('');
        $this->assertFalse($this->_request->getRawBody());
        stream_wrapper_restore('php');
    }

    public function testGetQuery()
    {
        $this->assertEquals('val1', $this->_request->getQuery('var1'));

        $this->assertEquals('foo', $this->_request->getQuery('BAR', 'foo'));

        $expected = array('var1' => 'val1', 'var2' => 'val2');
        $this->assertEquals( $expected, $this->_request->getQuery());
    }


    public function testGetPost()
    {
        $_POST['post1'] = 'val1';
        $this->assertEquals('val1', $this->_request->getPost('post1'));

        $this->assertEquals('foo', $this->_request->getPost('BAR', 'foo'));

        $_POST['post2'] = 'val2';
        $expected = array('post1' => 'val1', 'post2' => 'val2');
        $this->assertEquals($expected, $this->_request->getPost());

    }

    public function testGetPathInfo()
    {
        $this->assertEquals('/news/3', $this->_request->getPathInfo(), 'Base URL: ' . var_export($this->_request->getBaseUrl(), 1));
    }

    public function testSetPathInfo()
    {
        $this->_request->setPathInfo('/archives/past/4');
        $this->assertEquals('/archives/past/4', $this->_request->getPathInfo());
    }

    public function testPathInfoNeedingBaseUrl()
    {
        $request = new Request\Http('http://localhost/test/index.php/ctrl-name/act-name');
        $this->assertEquals('/test/index.php/ctrl-name/act-name', $request->getRequestUri());
        $request->setBaseUrl('/test/index.php');
        $this->assertEquals('/test/index.php', $request->getBaseUrl());

        $requestUri = $request->getRequestUri();
        $baseUrl    = $request->getBaseUrl();
        $pathInfo   = substr($requestUri, strlen($baseUrl));
        $this->assertTrue($pathInfo ? true : false);

        $this->assertEquals('/ctrl-name/act-name', $request->getPathInfo(), "Expected $pathInfo;");
    }

    public function testGetSetAlias()
    {
        $this->_request->setAlias('controller', 'var1');
        $this->assertEquals('var1', $this->_request->getAlias('controller'));
    }

    public function testGetAliases()
    {
        $this->_request->setAlias('controller', 'var1');
        $this->_request->setAlias('action', 'var2');
        $this->assertSame(array('controller' => 'var1', 'action' => 'var2'), $this->_request->getAliases());
    }

    public function testGetRequestUri()
    {
        $this->assertEquals('/news/3?var1=val1&var2=val2', $this->_request->getRequestUri());
    }

    public function testSetRequestUri()
    {
        $this->_request->setRequestUri('/archives/past/4?set=this&unset=that');
        $this->assertEquals('/archives/past/4?set=this&unset=that', $this->_request->getRequestUri());
        $this->assertEquals('this', $this->_request->getQuery('set'));
        $this->assertEquals('that', $this->_request->getQuery('unset'));
    }

    public function testGetBaseUrl()
    {
        $this->assertSame('', $this->_request->getBaseUrl());
    }

    /*
     * Tests if an empty string gets returned when no basepath is set on the request.
     * This is important on windows, where before this fix '\' was returned instead of an empty string.
     * @group ZF-4810
     */
    public function testGetBasePathIsEmptyStringIfNoneSet()
    {
        $request = new Request\Http();
        $this->assertEquals('', $request->getBasePath());
    }

    public function testSetBaseUrl()
    {
        $this->_request->setBaseUrl('/news');
        $this->assertEquals('/news', $this->_request->getBaseUrl());
    }

    public function testSetBaseUrlUsingPhpSelf()
    {
        $_SERVER['REQUEST_URI']     = '/index.php/news/3?var1=val1&var2=val2';
        $_SERVER['SCRIPT_NAME']     = '/home.php';
        $_SERVER['PHP_SELF']        = '/index.php/news/3';
        $_SERVER['SCRIPT_FILENAME'] = '/var/web/html/index.php';
        $_GET = array(
            'var1' => 'val1',
            'var2' => 'val2'
        );
        $request = new Request\Http();

        $this->assertEquals('/index.php', $request->getBaseUrl());
    }

    public function testSetBaseUrlUsingOrigScriptName()
    {
        $_SERVER['REQUEST_URI']     = '/index.php/news/3?var1=val1&var2=val2';
        $_SERVER['SCRIPT_NAME']     = '/home.php';
        $_SERVER['PHP_SELF']        = '/home.php';
        $_SERVER['ORIG_SCRIPT_NAME']= '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = '/var/web/html/index.php';
        $_GET = array(
            'var1' => 'val1',
            'var2' => 'val2'
        );
        $request = new Request\Http();

        $this->assertEquals('/index.php', $request->getBaseUrl());
    }

    public function testSetBaseUrlAutoDiscoveryUsingRequestUri()
    {
        $_SERVER['REQUEST_URI']     = '/index.php/news/3?var1=val1&var2=val2';
        $_SERVER['PHP_SELF']        = '/index.php/news/3';
        $_SERVER['SCRIPT_FILENAME'] = '/var/web/html/index.php';
        $_GET = array(
            'var1' => 'val1',
            'var2' => 'val2'
        );
        $request = new Request\Http();

        $this->assertEquals('/index.php', $request->getBaseUrl());
    }

    public function testSetBaseUrlAutoDiscoveryUsingXRewriteUrl()
    {
        unset($_SERVER['REQUEST_URI']);
        $_SERVER['HTTP_X_REWRITE_URL'] = '/index.php/news/3?var1=val1&var2=val2';
        $_SERVER['PHP_SELF']           = '/index.php/news/3';
        $_SERVER['SCRIPT_FILENAME']    = '/var/web/html/index.php';
        $_GET = array(
            'var1' => 'val1',
            'var2' => 'val2'
        );
        $request = new Request\Http();

        $this->assertEquals('/index.php', $request->getBaseUrl());
    }

    public function testSetBaseUrlAutoDiscoveryUsingOrigPathInfo()
    {
        unset($_SERVER['REQUEST_URI']);
        $_SERVER['ORIG_PATH_INFO']  = '/index.php/news/3';
        $_SERVER['QUERY_STRING']    = 'var1=val1&var2=val2';
        $_SERVER['PHP_SELF']        = '/index.php/news/3';
        $_SERVER['SCRIPT_FILENAME'] = '/var/web/html/index.php';
        $_GET = array(
            'var1' => 'val1',
            'var2' => 'val2'
        );
        $request = new Request\Http();

        $this->assertEquals('/index.php', $request->getBaseUrl());
    }
    /**
     * @group ZF-7824
     */
    public function testSetBaseUrlWithScriptNameAsGetParam()
    {
        $request = new Request\Http;

        $_SERVER['REQUEST_URI']     = '/article/archive?foo=index.php';
        $_SERVER['QUERY_STRING']    = 'foo=index.php';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/zftests/index.php';

        $this->assertEquals('/article/archive', $request->getPathInfo());
    }

    public function testGetSetBasePath()
    {
        $this->_request->setBasePath('/news');
        $this->assertEquals('/news', $this->_request->getBasePath());
    }

    public function testBasePathAutoDiscovery()
    {
        $_SERVER['REQUEST_URI']     = '/html/index.php/news/3?var1=val1&var2=val2';
        $_SERVER['PHP_SELF']        = '/html/index.php/news/3';
        $_SERVER['SCRIPT_FILENAME'] = '/var/web/html/index.php';
        $_GET = array(
            'var1' => 'val1',
            'var2' => 'val2'
        );
        $request = new Request\Http();

        $this->assertEquals('/html', $request->getBasePath(), $request->getBaseUrl());
    }

    public function testBasePathAutoDiscoveryWithPhpFile()
    {
        $_SERVER['REQUEST_URI']     = '/dir/action';
        $_SERVER['PHP_SELF']        = '/dir/index.php';
        $_SERVER['SCRIPT_FILENAME'] = '/var/web/dir/index.php';
        $request = new Request\Http();

        $this->assertEquals('/dir', $request->getBasePath(), $request->getBaseUrl());
    }

    public function testGetCookie()
    {
        $_COOKIE['foo'] = 'bar';
        $this->assertSame('bar', $this->_request->getCookie('foo'));
        $this->assertEquals('foo', $this->_request->getCookie('BAR', 'foo'));
        $this->assertEquals($_COOKIE, $this->_request->getCookie());
    }

    public function testGetServer()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->assertEquals($_SERVER['REQUEST_METHOD'], $this->_request->getServer('REQUEST_METHOD'));
        }
        $this->assertEquals('foo', $this->_request->getServer('BAR', 'foo'));
        $this->assertEquals($_SERVER, $this->_request->getServer());
    }

    public function testGetEnv()
    {
        if (isset($_ENV['PATH'])) {
            $this->assertEquals($_ENV['PATH'], $this->_request->getEnv('PATH'));
        }
        $this->assertEquals('foo', $this->_request->getEnv('BAR', 'foo'));
        $this->assertEquals($_ENV, $this->_request->getEnv());
    }

    public function testGetHeader()
    {
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'UTF-8';
        $_SERVER['HTTP_CONTENT_TYPE']    = 'text/json';

        $this->assertEquals('UTF-8', $this->_request->getHeader('Accept-Encoding'));
        $this->assertEquals('text/json', $this->_request->getHeader('Content-Type'));

        $this->assertFalse($this->_request->getHeader('X-No-Such-Thing'));
    }

    public function testGetHeaderThrowsExceptionWithNoInput()
    {
        try {
            // Suppressing warning
            $header = @$this->_request->getHeader();
            $this->fail('getHeader() should fail with no arguments)');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testIsXmlHttpRequest()
    {
        $this->assertFalse($this->_request->isXmlHttpRequest());
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->_request->isXmlHttpRequest());
    }

    public function testSetNullParamUnsetsKey()
    {
        $this->_request->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_request->getParam('foo'));
        $this->_request->setParam('foo', null);
        $params = $this->_request->getParams();
        $this->assertFalse(isset($params['foo']));
    }

    public function testSetNullParamsUnsetsKeys()
    {
        $this->_request->setParams(array('foo' => 'bar', 'bar' => 'baz'));
        $this->assertEquals('bar', $this->_request->getParam('foo'));
        $this->assertEquals('baz', $this->_request->getParam('bar'));
        $this->_request->setParams(array('foo' => null));
        $params = $this->_request->getParams();
        $this->assertFalse(isset($params['foo']));
        $this->assertTrue(isset($params['bar']));
    }

    public function testGetAliasedParamZF2455()
    {
        $this->_request->setParam('controller', 'value');
        $this->_request->setAlias('var1', 'controller');

        $this->assertEquals('value', $this->_request->getParam('controller'));
        $this->assertEquals('value', $this->_request->getParam('var1'));
    }

    public function testCanDetectFlashRequests()
    {
        $this->assertFalse($this->_request->isFlashRequest());
        $_SERVER['HTTP_USER_AGENT'] = 'Shockwave Flash';
        $this->assertTrue($this->_request->isFlashRequest());

        $_SERVER['HTTP_USER_AGENT'] = 'Adobe Flash Player 10';
        $this->assertTrue($this->_request->isFlashRequest());
    }

    /**
     * @group ZF-1798
     */
    public function testGetAndPostBothInDefaultParamSources()
    {
        $this->assertEquals(array('_GET', '_POST'), $this->_request->getParamSources());
    }

    /**
     * @group ZF-1798
     */
    public function testCanSetParamSources()
    {
        $this->testGetAndPostBothInDefaultParamSources();
        $this->_request->setParamSources(array());
        $this->assertSame(array(), $this->_request->getParamSources());
        $this->_request->setParamSources(array('_GET'));
        $this->assertSame(array('_GET'), $this->_request->getParamSources());
    }

    /**
     * @group ZF-1798
     */
    public function testParamSourcesHonoredByGetParam()
    {
        $_GET  = array('foo' => 'bar');
        $_POST = array('foo' => 'baz');
        $this->_request->setParamSources(array('_POST'));
        $this->assertEquals('baz', $this->_request->getParam('foo'));
    }

    /**
     * @group ZF-3161
     * @group ZFI-233
     * @group ZF-5818
     */
    public function testStrippingHttpProtocolAndHostFromRequestUriOnlyWhenPresentAtBeginningOfUri()
    {
        $_SERVER['REQUEST_URI'] = 'http://foo.example.com/foo/bar?r=http://foo.example.com/bar/baz';
        $_SERVER['HTTP_HOST']   = 'foo.example.com';
        $request = new Request\Http();
        $test = $request->getRequestUri();
        $this->assertEquals('/foo/bar?r=http://foo.example.com/bar/baz', $test);

        $_SERVER['REQUEST_URI'] = '/foo/bar?r=http://foo.example.com/bar/baz';
        $_SERVER['HTTP_HOST']   = 'foo.example.com';
        $request = new Request\Http();
        $test = $request->getRequestUri();
        $this->assertEquals('/foo/bar?r=http://foo.example.com/bar/baz', $test);
    }

    /**
     * @group ZF-3161
     * @group ZFI-233
     * @group ZF-5818
     */
    public function testStrippingHttpsProtocolAndHostFromRequestUriOnlyWhenPresentAtBeginningOfUri()
    {
        $_SERVER['REQUEST_URI'] = 'https://foo.example.com/foo/bar?r=https://foo.example.com/bar/baz';
        $_SERVER['HTTP_HOST']   = 'foo.example.com';
        $_SERVER['HTTPS']       = 'on';
        $request = new Request\Http();
        $test = $request->getRequestUri();
        $this->assertEquals('/foo/bar?r=https://foo.example.com/bar/baz', $test);

        $_SERVER['REQUEST_URI'] = '/foo/bar?r=https://foo.example.com/bar/baz';
        $_SERVER['HTTP_HOST']   = 'foo.example.com';
        $_SERVER['HTTPS']       = 'on';
        $request = new Request\Http();
        $test = $request->getRequestUri();
        $this->assertEquals('/foo/bar?r=https://foo.example.com/bar/baz', $test);
    }

    /**
     * @group ZF-3161
     * @group ZFI-233
     * @group ZF-5818
     */
    public function testStrippingHttpProtocolHostAndNonStandardPortFromRequestUriOnlyWhenPresentAtBeginningOfUri()
    {
        $_SERVER['REQUEST_URI'] = 'http://foo.example.com:8888/foo/bar?r=http://foo.example.com:8888/bar/baz';
        $_SERVER['HTTP_HOST']   = '';
        $_SERVER['SERVER_NAME'] = 'foo.example.com';
        $_SERVER['SERVER_PORT'] = '8888';
        $request = new Request\Http();
        $test = $request->getRequestUri();
        $this->assertEquals('/foo/bar?r=http://foo.example.com:8888/bar/baz', $test);

        $_SERVER['REQUEST_URI'] = '/foo/bar?r=https://foo.example.com:8888/bar/baz';
        $_SERVER['HTTP_HOST']   = '';
        $_SERVER['SERVER_NAME'] = 'foo.example.com';
        $_SERVER['SERVER_PORT'] = '8888';
        $request = new Request\Http();
        $test = $request->getRequestUri();
        $this->assertEquals('/foo/bar?r=https://foo.example.com:8888/bar/baz', $test);
    }

    /**
     * @group ZF-7092
     */
    public function testGetClientIp()
    {
        $request = new Request\Http();

        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.10';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.11';
        $_SERVER['REMOTE_ADDR'] = '192.168.1.12';

        $this->assertEquals('192.168.1.10', $request->getClientIp());

        $_SERVER['HTTP_CLIENT_IP'] = '';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.11';
        $_SERVER['REMOTE_ADDR'] = '192.168.1.12';

        $this->assertEquals('192.168.1.11', $request->getClientIp());

        $_SERVER['HTTP_CLIENT_IP'] = '';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '';
        $_SERVER['REMOTE_ADDR'] = '192.168.1.12';

        $this->assertEquals('192.168.1.12', $request->getClientIp());
    }

    /**
     * @group ZF-7117
     */
    public function testGetClientIpNoProxyCheck()
    {
        $request = new Request\Http();

        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.10';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.11';
        $_SERVER['REMOTE_ADDR'] = '192.168.1.12';

        $this->assertEquals('192.168.1.12', $request->getClientIp(false));
    }

    /**
     * @group ZF-7756
     */
    public function testCallingGetRawBodyMultipleTimesShouldReturnSameValue()
    {
        \ZendTest\AllTests\StreamWrapper\PHPInput::mockInput('foobar');
        $request = new Request\Http();
        $first = $request->getRawBody();
        $this->assertSame($first, $request->getRawBody());
        stream_wrapper_restore('php');
    }

    /**
     * @group ZF-5107
     */
    public function testGetParamsShouldHonorParamSourcesSetting()
    {
        $_GET  = array('foo' => 'bar');
        $_POST = array('foo' => 'baz');
        $this->_request->setParamSources(array('_POST'));
        $params = $this->_request->getParams();
        $this->assertEquals(array('foo' => 'baz'), $params);
    }

    /**
     * @group ZF-9091
     */
    public function testSetPathInfoShouldNotStripBaseUrlIfBaseUrlNotInRequestUri()
    {
        $request = new Request\Http();
        $request->setBaseUrl('/app');
        $_SERVER['REQUEST_URI'] = '/index/index';
        $pathInfo = $request->getPathInfo();
        $this->assertEquals('/index/index', $pathInfo);
    }
}

