<?php
// Call Zend_Filter_DashToSeparatorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_Word_DashToSeparatorTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Filter/Word/DashToSeparator.php';

/**
 * Test class for Zend_Filter_Word_DashToSeparator.
 */
class Zend_Filter_Word_DashToSeparatorTest extends PHPUnit_Framework_TestCase 
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_Word_DashToSeparatorTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testFilterSeparatesDashedWordsWithDefaultSpaces()
    {
        $string   = 'dash-separated-words';
        $filter   = new Zend_Filter_Word_DashToSeparator();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('dash separated words', $filtered);
    }
    
    public function testFilterSeparatesDashedWordsWithSomeString()
    {
        $string   = 'dash-separated-words';
        $filter   = new Zend_Filter_Word_DashToSeparator(':-:');
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('dash:-:separated:-:words', $filtered);
    }
    
}

// Call Zend_Filter_Word_DashToSeparatorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_Word_DashToSeparatorTest::main") {
    Zend_Filter_Word_DashToSeparatorTest::main();
}
