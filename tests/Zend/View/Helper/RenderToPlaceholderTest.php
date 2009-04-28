<?php
// Call Zend_View_Helper_RenderToPlaceholderTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_RenderToPlaceholderTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/Placeholder.php';


class Zend_View_Helper_RenderToPlaceholderTest extends PHPUnit_Framework_TestCase 
{

    protected $_view = null;

    public function setUp()
    {
        $this->_view = new Zend_View(array('scriptPath'=>dirname(__FILE__).'/_files/scripts/'));
    }

    public function testDefaultEmpty()
    {
        $this->_view->renderToPlaceholder('rendertoplaceholderscript.phtml', 'fooPlaceholder');
        $placeholder = new Zend_View_Helper_Placeholder();
        $this->assertEquals("Foo Bar\n", $placeholder->placeholder('fooPlaceholder')->getValue());
    }

}

