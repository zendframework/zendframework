<?php
// Call Zend_Filter_CamelCaseToDashTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_Word_CamelCaseToDashTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Filter/Word/CamelCaseToDash.php';

/**
 * Test class for Zend_Filter_Word_CamelCaseToDash.
 */
class Zend_Filter_Word_CamelCaseToDashTest extends PHPUnit_Framework_TestCase 
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_Word_CamelCaseToDashTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testFilterSeparatesCamelCasedWordsWithDashes()
    {
        $string   = 'CamelCasedWords';
        $filter   = new Zend_Filter_Word_CamelCaseToDash();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Camel-Cased-Words', $filtered);
    }
}

// Call Zend_Filter_Word_CamelCaseToDashTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_Word_CamelCaseToDashTest::main") {
    Zend_Filter_Word_CamelCaseToDashTest::main();
}
