<?php
// Call Zend_Controller_Request_HttpTestCaseTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Request_HttpTestCaseTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Controller_Request_HttpTestCase */
require_once 'Zend/Controller/Request/HttpTestCase.php';

/**
 * Test class for Zend_Controller_Request_HttpTestCase.
 */
class Zend_Controller_Request_HttpTestCaseTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Request_HttpTestCaseTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->request = new Zend_Controller_Request_HttpTestCase();
        $_GET    = array();
        $_POST   = array();
        $_COOKIE = array();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testGetRequestUriShouldNotAttemptToAutoDiscoverFromEnvironment()
    {
        $this->assertNull($this->request->getRequestUri());
    }

    public function testGetPathInfoShouldNotAttemptToAutoDiscoverFromEnvironment()
    {
        $pathInfo = $this->request->getPathInfo();
        $this->assertTrue(empty($pathInfo));
    }

    public function testGetShouldBeEmptyByDefault()
    {
        $post = $this->request->getQuery();
        $this->assertTrue(is_array($post));
        $this->assertTrue(empty($post));
    }

    public function testShouldAllowSpecifyingGetParameters()
    {
        $this->testGetShouldBeEmptyByDefault();
        $expected = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        );
        $this->request->setQuery($expected);

        $test = $this->request->getQuery();
        $this->assertSame($expected, $test);

        $this->request->setQuery('bat', 'bogus');
        $this->assertEquals('bogus', $this->request->getQuery('bat'));
        $test = $this->request->getQuery();
        $this->assertEquals(4, count($test));
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $test[$key]);
        }
    }

    public function testShouldPopulateGetSuperglobal()
    {
        $this->testShouldAllowSpecifyingGetParameters();
        $expected = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'bogus',
        );
        $this->assertEquals($expected, $_GET);
    }

    public function testShouldAllowClearingQuery()
    {
        $this->testShouldPopulateGetSuperglobal();
        $this->request->clearQuery();
        $test = $this->request->getQuery();
        $this->assertTrue(is_array($test));
        $this->assertTrue(empty($test));
    }

    public function testPostShouldBeEmptyByDefault()
    {
        $post = $this->request->getPost();
        $this->assertTrue(is_array($post));
        $this->assertTrue(empty($post));
    }

    public function testShouldAllowSpecifyingPostParameters()
    {
        $this->testPostShouldBeEmptyByDefault();
        $expected = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        );
        $this->request->setPost($expected);

        $test = $this->request->getPost();
        $this->assertSame($expected, $test);

        $this->request->setPost('bat', 'bogus');
        $this->assertEquals('bogus', $this->request->getPost('bat'));
        $test = $this->request->getPost();
        $this->assertEquals(4, count($test));
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $test[$key]);
        }
    }

    public function testShouldPopulatePostSuperglobal()
    {
        $this->testShouldAllowSpecifyingPostParameters();
        $expected = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'bogus',
        );
        $this->assertEquals($expected, $_POST);
    }

    public function testShouldAllowClearingPost()
    {
        $this->testShouldPopulatePostSuperglobal();
        $this->request->clearPost();
        $test = $this->request->getPost();
        $this->assertTrue(is_array($test));
        $this->assertTrue(empty($test));
    }

    public function testRawPostBodyShouldBeNullByDefault()
    {
        $this->assertNull($this->request->getRawBody());
    }

    public function testShouldAllowSpecifyingRawPostBody()
    {
        $this->request->setRawBody('Some content for the body');
        $this->assertEquals('Some content for the body', $this->request->getRawBody());
    }

    public function testShouldAllowClearingRawPostBody()
    {
        $this->testShouldAllowSpecifyingRawPostBody();
        $this->request->clearRawBody();
        $this->assertNull($this->request->getRawBody());
    }

    public function testHeadersShouldBeEmptyByDefault()
    {
        $headers = $this->request->getHeaders();
        $this->assertTrue(is_array($headers));
        $this->assertTrue(empty($headers));
    }

    public function testShouldAllowSpecifyingRequestHeaders()
    {
        $headers = array(
            'Content-Type'     => 'text/html',
            'Content-Encoding' => 'utf-8',
        );
        $this->request->setHeaders($headers);
        $test = $this->request->getHeaders();
        $this->assertTrue(is_array($test));
        $this->assertEquals(2, count($test));
        foreach ($headers as $key => $value) {
            $this->assertEquals($value, $this->request->getHeader($key));
        }
        $this->request->setHeader('X-Requested-With', 'XMLHttpRequest');
        $test = $this->request->getHeaders();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test));
        $this->assertEquals('XMLHttpRequest', $this->request->getHeader('X-Requested-With'));
    }

    public function testShouldAllowClearingRequestHeaders()
    {
        $this->testShouldAllowSpecifyingRequestHeaders();
        $this->request->clearHeaders();
        $headers = $this->request->getHeaders();
        $this->assertTrue(is_array($headers));
        $this->assertTrue(empty($headers));
    }

    public function testCookiesShouldBeEmptyByDefault()
    {
        $cookies = $this->request->getCookie();
        $this->assertTrue(is_array($cookies));
        $this->assertTrue(empty($cookies));
    }

    public function testShouldAllowSpecifyingCookies()
    {
        $cookies = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat'
        );
        $this->request->setCookies($cookies);
        $test = $this->request->getCookie();
        $this->assertEquals($cookies, $test);

        $this->request->setCookie('bat', 'bogus');
        $this->assertEquals('bogus', $this->request->getCookie('bat'));
    }

    public function testShouldPopulateCookieSuperGlobal()
    {
        $cookies = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'bogus',
        );
        $this->testShouldAllowSpecifyingCookies();
        $this->assertEquals($cookies, $_COOKIE);
    }

    public function testShouldAllowClearingAllCookies()
    {
        $this->testShouldAllowSpecifyingCookies();
        $this->request->clearCookies();
        $test = $this->request->getCookie();
        $this->assertTrue(is_array($test));
        $this->assertTrue(empty($test));
    }

    public function testRequestMethodShouldBeNullByDefault()
    {
        $this->assertNull($this->request->getMethod());
    }

    public function testShouldAllowSpecifyingRequestMethod()
    {
        $this->testRequestMethodShouldBeNullByDefault();
        $this->request->setMethod('GET');
        $this->assertTrue($this->request->isGet());
        $this->request->setMethod('POST');
        $this->assertTrue($this->request->isPost());
        $this->request->setMethod('PUT');
        $this->assertTrue($this->request->isPut());
        $this->request->setMethod('OPTIONS');
        $this->assertTrue($this->request->isOptions());
        $this->request->setMethod('HEAD');
        $this->assertTrue($this->request->isHead());
        $this->request->setMethod('DELETE');
        $this->assertTrue($this->request->isDelete());
    }
}

// Call Zend_Controller_Request_HttpTestCaseTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Request_HttpTestCaseTest::main") {
    Zend_Controller_Request_HttpTestCaseTest::main();
}
