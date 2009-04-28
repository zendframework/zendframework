<?php
// Call Zend_Controller_Action_Helper_JsonTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_JsonTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Action/Helper/Json.php';

require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Response/Http.php';
require_once 'Zend/Json.php';
require_once 'Zend/Layout.php';

/**
 * Test class for Zend_Controller_Action_Helper_Json
 */
class Zend_Controller_Action_Helper_JsonTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_JsonTest");
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
        Zend_Controller_Action_Helper_JsonTest_Layout::$_mvcInstance = null;

        $this->response = new Zend_Controller_Response_Http();
        $this->response->headersSentThrowsException = false;

        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setResponse($this->response);

        $this->viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        Zend_Controller_Action_HelperBroker::addHelper($this->viewRenderer);
        $this->helper = new Zend_Controller_Action_Helper_Json();
        $this->helper->suppressExit = true;
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

    public function verifyJsonHeader()
    {
        $headers = $this->response->getHeaders();

        $found = false;
        foreach ($headers as $header) {
            if ('Content-Type' == $header['name']) {
                $found = true;
                $value = $header['value'];
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals('application/json', $value);
    }

    public function testJsonHelperSetsResponseHeader()
    {
        $this->helper->encodeJson('foobar');
        $this->verifyJsonHeader();
    }

    public function testJsonHelperReturnsJsonEncodedString()
    {
        $data = $this->helper->encodeJson(array('foobar'));
        $this->assertTrue(is_string($data));
        $this->assertEquals(array('foobar'), Zend_Json::decode($data));
    }

    public function testJsonHelperDisablesLayoutsAndViewRendererByDefault()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
        $this->testJsonHelperReturnsJsonEncodedString();
        $this->assertFalse($layout->isEnabled());
        $this->assertTrue($this->viewRenderer->getNoRender());
    }

    public function testJsonHelperDoesNotDisableLayoutsAndViewRendererWhenKeepLayoutFlagTrue()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
        $data = $this->helper->encodeJson(array('foobar'), true);
        $this->assertTrue($layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
    }

    public function testSendJsonSendsResponse()
    {
        $this->helper->sendJson(array('foobar'));
        $this->verifyJsonHeader();
        $response = $this->response->getBody();
        $this->assertSame(array('foobar'), Zend_Json::decode($response));
    }

    public function testDirectProxiesToSendJsonByDefault()
    {
        $this->helper->direct(array('foobar'));
        $this->verifyJsonHeader();
        $response = $this->response->getBody();
        $this->assertSame(array('foobar'), Zend_Json::decode($response));
    }

    public function testCanPreventDirectSendingResponse()
    {
        $data = $this->helper->direct(array('foobar'), false);
        $this->assertSame(array('foobar'), Zend_Json::decode($data));
        $this->verifyJsonHeader();
        $response = $this->response->getBody();
        $this->assertTrue(empty($response));
    }

    public function testCanKeepLayoutsWhenUsingDirect()
    {
        $layout = Zend_Layout::startMvc();
        $data = $this->helper->direct(array('foobar'), false, true);
        $this->assertTrue($layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
    }
}

/**
 * Zend_Layout subclass to allow resetting MVC instance
 */
class Zend_Controller_Action_Helper_JsonTest_Layout extends Zend_Layout
{
    public static $_mvcInstance;
}

// Call Zend_Controller_Action_Helper_JsonTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_JsonTest::main") {
    Zend_Controller_Action_Helper_JsonTest::main();
}
