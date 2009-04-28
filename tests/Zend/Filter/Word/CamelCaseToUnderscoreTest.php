<?php
// Call Zend_Filter_CamelCaseToUnderscoreTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_Word_CamelCaseToUnderscoreTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Filter/Word/CamelCaseToUnderscore.php';

/**
 * Test class for Zend_Filter_Word_CamelCaseToUnderscore.
 */
class Zend_Filter_Word_CamelCaseToUnderscoreTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_Word_CamelCaseToUnderscoreTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testFilterSeparatesCamelCasedWordsWithUnderscores()
    {
        $string   = 'CamelCasedWords';
        $filter   = new Zend_Filter_Word_CamelCaseToUnderscore();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Camel_Cased_Words', $filtered);
    }
}

// Call Zend_Filter_Word_CamelCaseToUnderscoreTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_Word_CamelCaseToUnderscoreTest::main") {
    Zend_Filter_Word_CamelCaseToUnderscoreTest::main();
}
