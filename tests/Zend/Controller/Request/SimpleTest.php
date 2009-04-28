<?php
// Call Zend_Controller_Request_SimpleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Request_SimpleTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Request/Simple.php';

/**
 * Test class for Zend_Controller_Request_Simple.
 */
class Zend_Controller_Request_SimpleTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Request_SimpleTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testSimpleRequestIsOfAbstractRequestType()
    {
        $request = new Zend_Controller_Request_Simple();
        $this->assertTrue($request instanceof Zend_Controller_Request_Abstract);
    }
    
    public function testSimpleReqestRetainsValuesPassedFromConstructor()
    {
        $request = new Zend_Controller_Request_Simple('test1', 'test2', 'test3', array('test4' => 'test5'));
        $this->assertEquals($request->getActionName(), 'test1');
        $this->assertEquals($request->getControllerName(), 'test2');
        $this->assertEquals($request->getModuleName(), 'test3');
        $this->assertEquals($request->getParam('test4'), 'test5');
    }
    
}

// Call Zend_Controller_Request_SimpleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Request_SimpleTest::main") {
    Zend_Controller_Request_SimpleTest::main();
}
