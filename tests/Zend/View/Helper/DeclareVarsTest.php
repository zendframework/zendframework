<?php
// Call Zend_View_Helper_DeclareVarsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_DeclareVarsTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/DeclareVars.php';

class Zend_View_Helper_DeclareVarsTest extends PHPUnit_Framework_TestCase 
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_DeclareVarsTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $view = new Zend_View();
        $base = str_replace('/', DIRECTORY_SEPARATOR, '/../_templates');
        $view->setScriptPath(dirname(__FILE__) . $base);
        $view->strictVars(true);
        $this->view = $view;
    }

    public function tearDown()
    {
        unset($this->view);
    }

    protected function _declareVars()
    {
        $this->view->declareVars(
            'varName1',
            'varName2',
            array(
                'varName3' => 'defaultValue',
                'varName4' => array()
            )
        );
    }

    public function testDeclareUndeclaredVars()
    {
        $this->_declareVars();

        $this->assertTrue(isset($this->view->varName1));
        $this->assertTrue(isset($this->view->varName2));
        $this->assertTrue(isset($this->view->varName3));
        $this->assertTrue(isset($this->view->varName4));

        $this->assertEquals('defaultValue', $this->view->varName3);
        $this->assertEquals(array(), $this->view->varName4);
    }

    public function testDeclareDeclaredVars()
    {
        $this->view->varName2 = 'alreadySet';
        $this->view->varName3 = 'myValue';
        $this->view->varName5 = 'additionalValue';

        $this->_declareVars();

        $this->assertTrue(isset($this->view->varName1));
        $this->assertTrue(isset($this->view->varName2));
        $this->assertTrue(isset($this->view->varName3));
        $this->assertTrue(isset($this->view->varName4));
        $this->assertTrue(isset($this->view->varName5));

        $this->assertEquals('alreadySet', $this->view->varName2);
        $this->assertEquals('myValue', $this->view->varName3);
        $this->assertEquals('additionalValue', $this->view->varName5);
    }
}

// Call Zend_View_Helper_DeclareVarsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_DeclareVarsTest::main") {
    Zend_View_Helper_DeclareVarsTest::main();
}
