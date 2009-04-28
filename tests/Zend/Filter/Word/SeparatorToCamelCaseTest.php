<?php
// Call Zend_Filter_SeparatorToCamelCaseTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_Word_SeparatorToCamelCaseTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Filter/Word/SeparatorToCamelCase.php';

/**
 * Test class for Zend_Filter_Word_SeparatorToCamelCase.
 */
class Zend_Filter_Word_SeparatorToCamelCaseTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_Word_SeparatorToCamelCaseTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testFilterSeparatesCamelCasedWordsWithSpacesByDefault()
    {
        $string   = 'camel cased words';
        $filter   = new Zend_Filter_Word_SeparatorToCamelCase();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('CamelCasedWords', $filtered);
    }

    public function testFilterSeparatesCamelCasedWordsWithProvidedSeparator()
    {
        $string   = 'camel:-:cased:-:Words';
        $filter   = new Zend_Filter_Word_SeparatorToCamelCase(':-:');
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('CamelCasedWords', $filtered);
    }
}

// Call Zend_Filter_Word_SeparatorToCamelCaseTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_Word_SeparatorToCamelCaseTest::main") {
    Zend_Filter_Word_SeparatorToCamelCaseTest::main();
}
