<?php
// Call Zend_Json_ServerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Markup_FactoryTest::main");
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/Markup.php';

/**
 * Test class for Zend_Markup
 */
class Zend_Markup_FactoryTest extends PHPUnit_Framework_TestCase
{

    public function testFactory()
    {
        Zend_Markup::addParserPath('Zend_Markup_Test_Parser', 'Zend/Markup/Test/Parser');
        Zend_Markup::addRendererPath('Zend_Markup_Test_Renderer', 'Zend/Markup/Test/Renderer');

        Zend_Markup::factory('MockParser', 'MockRenderer');
    }

}

// Call Zend_Markup_BbcodeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Markup_FactoryTest::main") {
    Zend_Markup_BbcodeTest::main();
}
