<?php
// Call Zend_View_Helper_JsonTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_JsonTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View/Helper/Json.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Response/Http.php';
require_once 'Zend/Json.php';
require_once 'Zend/Layout.php';

/**
 * Test class for Zend_View_Helper_Json
 */
class Zend_View_Helper_JsonTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_JsonTest");
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
        Zend_View_Helper_JsonTest_Layout::$_mvcInstance = null;

        $this->response = new Zend_Controller_Response_Http();
        $this->response->headersSentThrowsException = false;

        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setResponse($this->response);

        $this->helper = new Zend_View_Helper_Json();
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
        $this->helper->json('foobar');
        $this->verifyJsonHeader();
    }

    public function testJsonHelperReturnsJsonEncodedString()
    {
        $data = $this->helper->json(array('foobar'));
        $this->assertTrue(is_string($data));
        $this->assertEquals(array('foobar'), Zend_Json::decode($data));
    }

    public function testJsonHelperDisablesLayoutsByDefault()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        $this->testJsonHelperReturnsJsonEncodedString();
        $this->assertFalse($layout->isEnabled());
    }

    public function testJsonHelperDoesNotDisableLayoutsWhenKeepLayoutFlagTrue()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->isEnabled());
        $data = $this->helper->json(array('foobar'), true);
        $this->assertTrue($layout->isEnabled());
    }
}

/**
 * Zend_Layout subclass to allow resetting MVC instance
 */
class Zend_View_Helper_JsonTest_Layout extends Zend_Layout
{
    public static $_mvcInstance;
}

// Call Zend_View_Helper_JsonTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_JsonTest::main") {
    Zend_View_Helper_JsonTest::main();
}
