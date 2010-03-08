<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Filter_CamelCaseToUnderscoreTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_Word_CamelCaseToUnderscoreTest::main");
}



/**
 * Test class for Zend_Filter_Word_CamelCaseToUnderscore.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
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

    public function testFilterSeperatingNumbersToUnterscore()
    {
        $string = 'PaTitle';
        $filter   = new Zend_Filter_Word_CamelCaseToUnderscore();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Pa_Title', $filtered);

        $string = 'Pa2Title';
        $filter   = new Zend_Filter_Word_CamelCaseToUnderscore();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Pa2_Title', $filtered);

        $string = 'Pa2aTitle';
        $filter   = new Zend_Filter_Word_CamelCaseToUnderscore();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Pa2a_Title', $filtered);
    }
}

// Call Zend_Filter_Word_CamelCaseToUnderscoreTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_Word_CamelCaseToUnderscoreTest::main") {
    Zend_Filter_Word_CamelCaseToUnderscoreTest::main();
}
