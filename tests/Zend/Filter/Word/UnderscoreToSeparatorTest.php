<?php
// Call Zend_Filter_Word_UnderscoreToSeparatorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_Word_UnderscoreToSeparatorTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Filter/Word/UnderscoreToSeparator.php';

/**
 * Test class for Zend_Filter_Word_UnderscoreToSeparator.
 */
class Zend_Filter_Word_UnderscoreToSeparatorTest extends PHPUnit_Framework_TestCase 
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_Word_UnderscoreToSeparatorTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testFilterSeparatesCamelCasedWordsDefaultSeparator()
    {
        $string   = 'underscore_separated_words';
        $filter   = new Zend_Filter_Word_UnderscoreToSeparator();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('underscore separated words', $filtered);
    }
    
    public function testFilterSeparatesCamelCasedWordsProvidedSeparator()
    {
        $string   = 'underscore_separated_words';
        $filter   = new Zend_Filter_Word_UnderscoreToSeparator(':=:');
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('underscore:=:separated:=:words', $filtered);
    }
    
}

// Call Zend_Filter_Word_UnderscoreToSeparatorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_Word_UnderscoreToSeparatorTest::main") {
    Zend_Filter_Word_UnderscoreToSeparatorTest::main();
}
