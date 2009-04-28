<?php

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

// Call Zend_Controller_Action_Helper_FlashMessengerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_FlashMessengerTest::main");
}

require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Action/Helper/FlashMessenger.php';
require_once 'Zend/Session.php';
require_once dirname(dirname(dirname(__FILE__))) . '/_files/HelperFlashMessengerController.php';

class Zend_Controller_Action_Helper_FlashMessengerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Controller_Action
     */
    public $controller;

    /**
     * @var Zend_Controller_Front
     */
    public $front;

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    public $helper;

    /**
     * @var Zend_Controller_Request_Http
     */
    public $request;

    /**
     * @var Zend_Controller_Response_Cli
     */
    public $response;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_FlashMessengerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $savePath = ini_get('session.save_path');
        if (strpos($savePath, ';')) {
            $savePath = explode(';', $savePath);
            $savePath = array_pop($savePath);
        }
        if (empty($savePath)) {
            $this->markTestSkipped('Cannot test FlashMessenger due to unavailable session save path');
        }

        if (headers_sent()) {
            $this->markTestSkipped('Cannot test FlashMessenger: cannot start session because headers already sent');
        }
        Zend_Session::start();

        $this->front      = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setControllerDirectory(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . '_files');
        $this->front->returnResponse(true);
        $this->request    = new Zend_Controller_Request_Http();
        $this->request->setControllerName('helper-flash-messenger');
        $this->response   = new Zend_Controller_Response_Cli();
        $this->controller = new HelperFlashMessengerController($this->request, $this->response, array());
        $this->helper     = new Zend_Controller_Action_Helper_FlashMessenger($this->controller);
    }

    public function testLoadFlashMessenger()
    {
        $this->markTestSkipped();
        $response = $this->front->dispatch($this->request);
        $this->assertEquals('Zend_Controller_Action_Helper_FlashMessenger123456', $response->getBody());
    }

    public function testClearMessages()
    {
        $this->markTestSkipped();
        $this->helper->addMessage('foo');
        $this->helper->addMessage('bar');
        $this->assertTrue($this->helper->hasMessages());
        $this->assertEquals(2, count($this->helper));

        $this->helper->clearMessages();
        $this->assertFalse($this->helper->hasMessages());
        $this->assertEquals(0, count($this->helper));
    }

    public function testDirectProxiesToAddMessage()
    {
        $this->markTestSkipped();
        $this->helper->direct('foo');
        $this->assertTrue($this->helper->hasMessages());
        $this->assertEquals(1, count($this->helper));
    }
}

// Call Zend_Controller_Action_Helper_FlashMessengerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_FlashMessengerTest::main") {
    Zend_Controller_Action_Helper_FlashMessengerTest::main();
}
