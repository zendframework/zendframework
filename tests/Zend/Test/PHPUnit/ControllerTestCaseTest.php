<?php
// Call Zend_Test_PHPUnit_ControllerTestCaseTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Test_PHPUnit_ControllerTestCaseTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Test_PHPUnit_ControllerTestCase */
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Session */
require_once 'Zend/Session.php';

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Test class for Zend_Test_PHPUnit_ControllerTestCase.
 */
class Zend_Test_PHPUnit_ControllerTestCaseTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Test_PHPUnit_ControllerTestCaseTest");
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
        $_SESSION = array();
        $this->setExpectedException(null);
        $this->testCase = new Zend_Test_PHPUnit_ControllerTestCaseTest_Concrete();
        $this->testCase->reset();
        $this->testCase->bootstrap = array($this, 'bootstrap');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        $registry = Zend_Registry::getInstance();
        if (isset($registry['router'])) {
            unset($registry['router']);
        }
        if (isset($registry['dispatcher'])) {
            unset($registry['dispatcher']);
        }
        if (isset($registry['plugin'])) {
            unset($registry['plugin']);
        }
        if (isset($registry['viewRenderer'])) {
            unset($registry['viewRenderer']);
        }
        Zend_Session::$_unitTestEnabled = false;
        session_id(uniqid());
    }

    public function bootstrap()
    {
    }

    public function testGetFrontControllerShouldReturnFrontController()
    {
        $controller = $this->testCase->getFrontController();
        $this->assertTrue($controller instanceof Zend_Controller_Front);
    }

    public function testGetFrontControllerShouldReturnSameFrontControllerObjectOnRepeatedCalls()
    {
        $controller = $this->testCase->getFrontController();
        $this->assertTrue($controller instanceof Zend_Controller_Front);
        $test = $this->testCase->getFrontController();
        $this->assertSame($controller, $test);
    }

    public function testGetRequestShouldReturnRequestTestCase()
    {
        $request = $this->testCase->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_HttpTestCase);
    }

    public function testGetRequestShouldReturnSameRequestObjectOnRepeatedCalls()
    {
        $request = $this->testCase->getRequest();
        $this->assertTrue($request instanceof Zend_Controller_Request_HttpTestCase);
        $test = $this->testCase->getRequest();
        $this->assertSame($request, $test);
    }

    public function testGetResponseShouldReturnResponseTestCase()
    {
        $response = $this->testCase->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_HttpTestCase);
    }

    public function testGetResponseShouldReturnSameResponseObjectOnRepeatedCalls()
    {
        $response = $this->testCase->getResponse();
        $this->assertTrue($response instanceof Zend_Controller_Response_HttpTestCase);
        $test = $this->testCase->getResponse();
        $this->assertSame($response, $test);
    }

    public function testGetQueryShouldReturnQueryTestCase()
    {
        $query = $this->testCase->getQuery();
        $this->assertTrue($query instanceof Zend_Dom_Query);
    }

    public function testGetQueryShouldReturnSameQueryObjectOnRepeatedCalls()
    {
        $query = $this->testCase->getQuery();
        $this->assertTrue($query instanceof Zend_Dom_Query);
        $test = $this->testCase->getQuery();
        $this->assertSame($query, $test);
    }

    public function testOverloadingShouldReturnRequestResponseAndFrontControllerObjects()
    {
        $request         = $this->testCase->getRequest();
        $response        = $this->testCase->getResponse();
        $frontController = $this->testCase->getFrontController();
        $this->assertSame($request, $this->testCase->request);
        $this->assertSame($response, $this->testCase->response);
        $this->assertSame($frontController, $this->testCase->frontController);
    }

    public function testOverloadingShouldPreventSettingRequestResponseAndFrontControllerObjects()
    {
        try {
            $this->testCase->request = new Zend_Controller_Request_Http();
            $this->fail('Setting request object as public property should raise exception');
        } catch (Exception $e) {
            $this->assertContains('not allow', $e->getMessage());
        }

        try {
            $this->testCase->response = new Zend_Controller_Response_Http();
            $this->fail('Setting response object as public property should raise exception');
        } catch (Exception $e) {
            $this->assertContains('not allow', $e->getMessage());
        }

        try {
            $this->testCase->frontController = Zend_Controller_Front::getInstance();
            $this->fail('Setting front controller as public property should raise exception');
        } catch (Exception $e) {
            $this->assertContains('not allow', $e->getMessage());
        }
    }

    public function testResetShouldResetMvcState()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        require_once 'Zend/Controller/Dispatcher/Standard.php';
        require_once 'Zend/Controller/Plugin/ErrorHandler.php';
        require_once 'Zend/Controller/Router/Rewrite.php';
        $request    = $this->testCase->getRequest();
        $response   = $this->testCase->getResponse();
        $router     = new Zend_Controller_Router_Rewrite();
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $plugin     = new Zend_Controller_Plugin_ErrorHandler();
        $controller = $this->testCase->getFrontController();
        $controller->setParam('foo', 'bar')
                   ->registerPlugin($plugin)
                   ->setRouter($router)
                   ->setDispatcher($dispatcher);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->testCase->reset();
        $test = $controller->getRouter();
        $this->assertNotSame($router, $test);
        $test = $controller->getDispatcher();
        $this->assertNotSame($dispatcher, $test);
        $this->assertFalse($controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $test = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $this->assertNotSame($viewRenderer, $test);
        $this->assertNull($controller->getRequest());
        $this->assertNull($controller->getResponse());
        $this->assertNotSame($request, $this->testCase->getRequest());
        $this->assertNotSame($response, $this->testCase->getResponse());
    }

    public function testBootstrapShouldSetRequestAndResponseTestCaseObjects()
    {
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $request    = $controller->getRequest();
        $response   = $controller->getResponse();
        $this->assertSame($this->testCase->getRequest(), $request);
        $this->assertSame($this->testCase->getResponse(), $response);
    }

    public function testBootstrapShouldIncludeBootstrapFileSpecifiedInPublicBootstrapProperty()
    {
        $this->testCase->bootstrap = dirname(__FILE__) . '/_files/bootstrap.php';
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $this->assertSame(Zend_Registry::get('router'), $controller->getRouter());
        $this->assertSame(Zend_Registry::get('dispatcher'), $controller->getDispatcher());
        $this->assertSame(Zend_Registry::get('plugin'), $controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $this->assertSame(Zend_Registry::get('viewRenderer'), Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'));
    }

    public function testBootstrapShouldInvokeCallbackSpecifiedInPublicBootstrapProperty()
    {
        $this->testCase->bootstrap = array($this, 'bootstrapCallback');
        $this->testCase->bootstrap();
        $controller = $this->testCase->getFrontController();
        $this->assertSame(Zend_Registry::get('router'), $controller->getRouter());
        $this->assertSame(Zend_Registry::get('dispatcher'), $controller->getDispatcher());
        $this->assertSame(Zend_Registry::get('plugin'), $controller->getPlugin('Zend_Controller_Plugin_ErrorHandler'));
        $this->assertSame(Zend_Registry::get('viewRenderer'), Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer'));
    }

    public function bootstrapCallback()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        require_once 'Zend/Controller/Dispatcher/Standard.php';
        require_once 'Zend/Controller/Front.php';
        require_once 'Zend/Controller/Plugin/ErrorHandler.php';
        require_once 'Zend/Controller/Router/Rewrite.php';
        require_once 'Zend/Registry.php';
        $router     = new Zend_Controller_Router_Rewrite();
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $plugin     = new Zend_Controller_Plugin_ErrorHandler();
        $controller = Zend_Controller_Front::getInstance();
        $controller->setParam('foo', 'bar')
                   ->registerPlugin($plugin)
                   ->setRouter($router)
                   ->setDispatcher($dispatcher);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        Zend_Registry::set('router', $router);
        Zend_Registry::set('dispatcher', $dispatcher);
        Zend_Registry::set('plugin', $plugin);
        Zend_Registry::set('viewRenderer', $viewRenderer);
    }

    public function testDispatchShouldDispatchSpecifiedUrl()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/bar');
        $request  = $this->testCase->getRequest();
        $response = $this->testCase->getResponse();
        $content  = $response->getBody();
        $this->assertEquals('zend-test-php-unit-foo', $request->getControllerName(), $content);
        $this->assertEquals('bar', $request->getActionName());
        $this->assertContains('FooController::barAction', $content, $content);
    }

    public function testAssertQueryShouldDoNothingForValidResponseContent()
    {
        $this->testCase->getFrontController()->setControllerDirectory(realpath(dirname(__FILE__)) . '/_files/application/controllers', 'default');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $body = $this->testCase->getResponse()->getBody();
        $this->testCase->assertQuery('div#foo legend.bar', $body);
        $this->testCase->assertQuery('div#foo legend.baz', $body);
        $this->testCase->assertQuery('div#foo legend.bat', $body);
        $this->testCase->assertNotQuery('div#foo legend.bogus', $body);
        $this->testCase->assertQueryContentContains('legend.bat', 'La di da', $body);
        $this->testCase->assertNotQueryContentContains('legend.bat', 'La do da', $body);
        $this->testCase->assertQueryContentRegex('legend.bat', '/d[a|i]/i', $body);
        $this->testCase->assertNotQueryContentRegex('legend.bat', '/d[o|e]/i', $body);
        $this->testCase->assertQueryCountMin('div#foo legend.bar', 2, $body);
        $this->testCase->assertQueryCount('div#foo legend.bar', 2, $body);
        $this->testCase->assertQueryCountMin('div#foo legend.bar', 2, $body);
        $this->testCase->assertQueryCountMax('div#foo legend.bar', 2, $body);
    }

    /**
     * @group ZF-4673
     */
    public function testAssertionsShouldIncreasePhpUnitAssertionCounter()
    {
        $this->testAssertQueryShouldDoNothingForValidResponseContent();
        $this->assertTrue(0 < $this->testCase->getNumAssertions());
        $this->assertTrue(12 <= $this->testCase->getNumAssertions());
    }

    public function testAssertQueryShouldThrowExceptionsForInValidResponseContent()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        try {
            $this->testCase->assertNotQuery('div#foo legend.bar');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertQuery('div#foo legend.bogus');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertNotQueryContentContains('legend.bat', 'La di da');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertQueryContentContains('legend.bat', 'La do da');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertNotQueryContentRegex('legend.bat', '/d[a|i]/i');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertQueryContentRegex('legend.bat', '/d[o|e]/i');
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertQueryCountMin('div#foo legend.bar', 3);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertQueryCount('div#foo legend.bar', 1);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertQueryCountMin('div#foo legend.bar', 3);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertQueryCountMax('div#foo legend.bar', 1);
            $this->fail('Invalid assertions should throw exceptions');
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
    }

    public function testAssertXpathShouldDoNothingForValidResponseContent()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertXpath("//div[@id='foo']//legend[contains(@class, ' bar ')]");
        $this->testCase->assertXpath("//div[@id='foo']//legend[contains(@class, ' baz ')]");
        $this->testCase->assertXpath("//div[@id='foo']//legend[contains(@class, ' bat ')]");
        $this->testCase->assertNotXpath("//div[@id='foo']//legend[contains(@class, ' bogus ')]");
        $this->testCase->assertXpathContentContains("//legend[contains(@class, ' bat ')]", "La di da");
        $this->testCase->assertNotXpathContentContains("//legend[contains(@class, ' bat ')]", "La do da");
        $this->testCase->assertXpathContentRegex("//legend[contains(@class, ' bat ')]", "/d[a'i]/i");
        $this->testCase->assertNotXpathContentRegex("//legend[contains(@class, ' bat ')]", "/d[o'e]/i");
        $this->testCase->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->testCase->assertXpathCount("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->testCase->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
        $this->testCase->assertXpathCountMax("//div[@id='foo']//legend[contains(@class, ' bar ')]", 2);
    }

    public function testAssertXpathShouldThrowExceptionsForInValidResponseContent()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        try {
            $this->testCase->assertNotXpath("//div[@id='foo']//legend[contains(@class, ' bar ')]");
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpath("//div[@id='foo']//legend[contains(@class, ' bogus ')]");
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bogus ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertNotXpathContentContains("//legend[contains(@class, ' bat ')]", "La di da");
            $this->fail("Invalid assertions should throw exceptions; assertion against //legend[contains(@class, ' bat ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathContentContains("//legend[contains(@class, ' bat ')]", 'La do da');
            $this->fail("Invalid assertions should throw exceptions; assertion against //legend[contains(@class, ' bat ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertNotXpathContentRegex("//legend[contains(@class, ' bat ')]", '/d[a|i]/i');
            $this->fail("Invalid assertions should throw exceptions; assertion against //legend[contains(@class, ' bat ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathContentRegex("//legend[contains(@class, ' bat ')]", '/d[o|e]/i');
            $this->fail("Invalid assertions should throw exceptions; assertion against //legend[contains(@class, ' bat ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 3);
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathCount("//div[@id='foo']//legend[contains(@class, ' bar ')]", 1);
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathCountMin("//div[@id='foo']//legend[contains(@class, ' bar ')]", 3);
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
        try {
            $this->testCase->assertXpathCountMax("//div[@id='foo']//legend[contains(@class, ' bar ')]", 1);
            $this->fail("Invalid assertions should throw exceptions; assertion against //div[@id='foo']//legend[contains(@class, ' bar ')] failed");
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
        }
    }

    public function testRedirectAssertionsShouldDoNothingForValidAssertions()
    {
        $this->testCase->getResponse()->setRedirect('/foo');
        $this->testCase->assertRedirect();
        $this->testCase->assertRedirectTo('/foo', var_export($this->testCase->getResponse()->sendHeaders(), 1));
        $this->testCase->assertRedirectRegex('/FOO$/i');

        $this->testCase->reset();
        $this->testCase->assertNotRedirect();
        $this->testCase->assertNotRedirectTo('/foo');
        $this->testCase->assertNotRedirectRegex('/FOO$/i');
        $this->testCase->getResponse()->setRedirect('/foo');
        $this->testCase->assertNotRedirectTo('/bar');
        $this->testCase->assertNotRedirectRegex('/bar/i');
    }

    public function testHeaderAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getResponse()->setHeader('Content-Type', 'x-application/my-foo');
        $this->testCase->assertResponseCode(200);
        $this->testCase->assertNotResponseCode(500);
        $this->testCase->assertHeader('Content-Type');
        $this->testCase->assertNotHeader('X-Bogus');
        $this->testCase->assertHeaderContains('Content-Type', 'my-foo');
        $this->testCase->assertNotHeaderContains('Content-Type', 'my-bar');
        $this->testCase->assertHeaderRegex('Content-Type', '#^[a-z-]+/[a-z-]+$#i');
        $this->testCase->assertNotHeaderRegex('Content-Type', '#^\d+#i');
    }

    public function testHeaderAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getResponse()->setHeader('Content-Type', 'x-application/my-foo');
        try {
            $this->testCase->assertResponseCode(500);
            $this->fail();
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->testCase->assertNotResponseCode(200);
            $this->fail();
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->testCase->assertNotHeader('Content-Type');
            $this->fail();
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->testCase->assertHeader('X-Bogus');
            $this->fail();
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->testCase->assertNotHeaderContains('Content-Type', 'my-foo');
            $this->fail();
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->testCase->assertHeaderContains('Content-Type', 'my-bar');
            $this->fail();
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->testCase->assertNotHeaderRegex('Content-Type', '#^[a-z-]+/[a-z-]+$#i');
            $this->fail();
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
        try {
            $this->testCase->assertHeaderRegex('Content-Type', '#^\d+#i');
            $this->fail();
        } catch (Zend_Test_PHPUnit_Constraint_Exception $e) {
            $this->assertContains('Failed', $e->getMessage());
        }
    }

    public function testModuleAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertModule('default');
        $this->testCase->assertNotModule('zend-test-php-unit-foo');
    }

    public function testModuleAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError');
        $this->testCase->assertModule('zend-test-php-unit-foo');
        $this->testCase->assertNotModule('default');
    }

    public function testControllerAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertController('zend-test-php-unit-foo');
        $this->testCase->assertNotController('baz');
    }

    public function testControllerAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError');
        $this->testCase->assertController('baz');
        $this->testCase->assertNotController('zend-test-php-unit-foo');
    }

    public function testActionAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertAction('baz');
        $this->testCase->assertNotAction('zend-test-php-unit-foo');
    }

    public function testActionAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/baz');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError');
        $this->testCase->assertAction('foo');
        $this->testCase->assertNotAction('baz');
    }

    public function testRouteAssertionShouldDoNothingForValidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $this->testCase->assertRoute('default');
        $this->testCase->assertNotRoute('zend-test-php-unit-foo');
    }

    public function testRouteAssertionShouldThrowExceptionForInvalidComparison()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/foo/baz');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError');
        $this->testCase->assertRoute('foo');
        $this->testCase->assertNotRoute('default');
    }

    public function testResetShouldResetSessionArray()
    {
        $this->assertTrue(empty($_SESSION));
        $_SESSION = array('foo' => 'bar', 'bar' => 'baz');
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $_SESSION, var_export($_SESSION, 1));
        $this->testCase->reset();
        $this->assertTrue(empty($_SESSION));
    }

    public function testResetShouldUnitTestEnableZendSession()
    {
        $this->testCase->reset();
        $this->assertTrue(Zend_Session::$_unitTestEnabled);
    }

    public function testResetResponseShouldClearResponseObject()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $response = $this->testCase->getResponse();
        $this->testCase->resetResponse();
        $test = $this->testCase->getResponse();
        $this->assertNotSame($response, $test);
    }

    /**
     * @group ZF-4511
     */
    public function testResetRequestShouldClearRequestObject()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $request = $this->testCase->getRequest();
        $this->testCase->resetRequest();
        $test = $this->testCase->getRequest();
        $this->assertNotSame($request, $test);
    }

    public function testResetResponseShouldClearAllViewPlaceholders()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $view = $viewRenderer->view;
        $view->addHelperPath('Zend/Dojo/View/Helper', 'Zend_Dojo_View_Helper');
        $view->dojo()->setCdnVersion('1.1.0')
                     ->requireModule('dojo.parser')
                     ->enable();
        $view->headTitle('Foo');
        $this->testCase->dispatch('/zend-test-php-unit-foo/baz');
        $response = $this->testCase->getResponse();
        $this->testCase->resetResponse();

        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper', 'Zend_Dojo_View_Helper');
        $this->assertFalse($view->dojo()->isEnabled(), 'Dojo is enabled? ', $view->dojo());
        $this->assertNotContains('Foo', $view->headTitle()->__toString(), 'Head title persisted?');
    }

    /**
     * @group ZF-4070
     */
    public function testQueryParametersShouldPersistFollowingDispatch()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
                ->setQuery('james', 'bond');

        $this->assertEquals('proper', $request->getQuery('mr'), '(pre) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(pre) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));

        $this->testCase->dispatch('/');

        $this->assertEquals('proper', $request->getQuery('mr'), '(post) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(post) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));
    }

    /**
     * @group ZF-4070
     */
    public function testQueryStringShouldNotOverwritePreviouslySetQueryParameters()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
                ->setQuery('james', 'bond');

        $this->assertEquals('proper', $request->getQuery('mr'), '(pre) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(pre) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));

        $this->testCase->dispatch('/?spy=super');

        $this->assertEquals('super', $request->getQuery('spy'), '(post) Failed retrieving spy parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('proper', $request->getQuery('mr'), '(post) Failed retrieving mr parameter: ' . var_export($request->getQuery(), 1));
        $this->assertEquals('bond', $request->getQuery('james'), '(post) Failed retrieving james parameter: ' . var_export($request->getQuery(), 1));
    }

    /**
     * @group ZF-3979
     */
    public function testSuperGlobalArraysShouldBeClearedDuringSetUp()
    {
        $this->testCase->getFrontController()->setControllerDirectory(dirname(__FILE__) . '/_files/application/controllers');
        $request = $this->testCase->request;
        $request->setQuery('mr', 'proper')
                ->setPost('foo', 'bar')
                ->setCookie('bar', 'baz');

        $this->testCase->setUp();
        $this->assertNull($request->getQuery('mr'), 'Retrieved mr get parameter: ' . var_export($request->getQuery(), 1));
        $this->assertNull($request->getPost('foo'), 'Retrieved foo post parameter: ' . var_export($request->getPost(), 1));
        $this->assertNull($request->getCookie('bar'), 'Retrieved bar cookie parameter: ' . var_export($request->getCookie(), 1));
    }
}

// Concrete test case class for testing purposes
class Zend_Test_PHPUnit_ControllerTestCaseTest_Concrete extends Zend_Test_PHPUnit_ControllerTestCase
{
}

// Call Zend_Test_PHPUnit_ControllerTestCaseTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Test_PHPUnit_ControllerTestCaseTest::main") {
    Zend_Test_PHPUnit_ControllerTestCaseTest::main();
}
