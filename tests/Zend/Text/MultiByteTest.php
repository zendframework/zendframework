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
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Text_MultiByteTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Text_MultiByteTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * Zend_Text_MultiByte
 */
require_once 'Zend/Text/MultiByte.php';

/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Text
 */
class Zend_Text_MultiByteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Text_MultiByteTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Standard cut tests
     */
    public function testWordWrapCutSingleLine()
    {
        $line = Zend_Text_MultiByte::wordWrap('äbüöcß', 2, ' ', true);
        $this->assertEquals('äb üö cß', $line);
    }

    public function testWordWrapCutMultiLine()
    {
        $line = Zend_Text_MultiByte::wordWrap('äbüöc ß äbüöcß', 2, ' ', true);
        $this->assertEquals('äb üö c ß äb üö cß', $line);
    }

    public function testWordWrapCutMultiLineShortWords()
    {
        $line = Zend_Text_MultiByte::wordWrap('Ä very long wöööööööööööörd.', 8, "\n", true);
        $this->assertEquals("Ä very\nlong\nwööööööö\nööööörd.", $line);
    }

    /**
     * Alternative cut tests
     */
    public function testWordWrapCutBeginningSingleSpace()
    {
        $line = Zend_Text_MultiByte::wordWrap(' äüöäöü', 3, ' ', true);
        $this->assertEquals(' äüö äöü', $line);
    }

    public function testWordWrapCutEndingSingleSpace()
    {
        $line = Zend_Text_MultiByte::wordWrap('äüöäöü ', 3, ' ', true);
        $this->assertEquals('äüö äöü ', $line);
    }

    public function testWordWrapCutEndingTwoSpaces()
    {
        $line = Zend_Text_MultiByte::wordWrap('äüöäöü  ', 3, ' ', true);
        $this->assertEquals('äüö äöü  ', $line);
    }

    public function testWordWrapCutEndingThreeSpaces()
    {
        $line = Zend_Text_MultiByte::wordWrap('äüöäöü  ', 3, ' ', true);
        $this->assertEquals('äüö äöü  ', $line);
    }

    public function testWordWrapCutEndingTwoBreaks()
    {
        $line = Zend_Text_MultiByte::wordWrap('äüöäöü--', 3, '-', true);
        $this->assertEquals('äüö-äöü--', $line);
    }

    public function testWordWrapCutTab()
    {
        $line = Zend_Text_MultiByte::wordWrap("äbü\töcß", 3, ' ', true);
        $this->assertEquals("äbü \töc ß", $line);
    }

    public function testWordWrapCutNewlineWithSpace()
    {
        $line = Zend_Text_MultiByte::wordWrap("äbü\nößt", 3, ' ', true);
        $this->assertEquals("äbü \nöß t", $line);
    }

    public function testWordWrapCutNewlineWithNewline()
    {
        $line = Zend_Text_MultiByte::wordWrap("äbü\nößte", 3, "\n", true);
        $this->assertEquals("äbü\nößt\ne", $line);
    }

    /**
     * Break cut tests
     */
    public function testWordWrapCutBreakBefore()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-foofoofoo', 8, '-', true);
        $this->assertEquals('foobar-foofoofo-o', $line);
    }

    public function testWordWrapCutBreakWith()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-foobar', 6, '-', true);
        $this->assertEquals('foobar-foobar', $line);
    }

    public function testWordWrapCutBreakWithin()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-foobar', 7, '-', true);
        $this->assertEquals('foobar-foobar', $line);
    }

    public function testWordWrapCutBreakWithinEnd()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-', 7, '-', true);
        $this->assertEquals('foobar-', $line);
    }

    public function testWordWrapCutBreakAfter()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-foobar', 5, '-', true);
        $this->assertEquals('fooba-r-fooba-r', $line);
    }

    /**
     * Standard no-cut tests
     */
    public function testWordWrapNoCutSingleLine()
    {
        $line = Zend_Text_MultiByte::wordWrap('äbüöcß', 2, ' ', false);
        $this->assertEquals('äbüöcß', $line);
    }

    public function testWordWrapNoCutMultiLine()
    {
        $line = Zend_Text_MultiByte::wordWrap('äbüöc ß äbüöcß', 2, "\n", false);
        $this->assertEquals("äbüöc\nß\näbüöcß", $line);
    }

    public function testWordWrapNoCutMultiWord()
    {
        $line = Zend_Text_MultiByte::wordWrap('äöü äöü äöü', 5, "\n", false);
        $this->assertEquals("äöü\näöü\näöü", $line);
    }

    /**
     * Break no-cut tests
     */
    public function testWordWrapNoCutBreakBefore()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-foofoofoo', 8, '-', false);
        $this->assertEquals('foobar-foofoofoo', $line);
    }

    public function testWordWrapNoCutBreakWith()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-foobar', 6, '-', false);
        $this->assertEquals('foobar-foobar', $line);
    }

    public function testWordWrapNoCutBreakWithin()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-foobar', 7, '-', false);
        $this->assertEquals('foobar-foobar', $line);
    }

    public function testWordWrapNoCutBreakWithinEnd()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-', 7, '-', false);
        $this->assertEquals('foobar-', $line);
    }

    public function testWordWrapNoCutBreakAfter()
    {
        $line = Zend_Text_MultiByte::wordWrap('foobar-foobar', 5, '-', false);
        $this->assertEquals('foobar-foobar', $line);
    }

    /**
     * Pad tests
     */
    public function testLeftPad()
    {
        $text = Zend_Text_MultiByte::strPad('äää', 5, 'ö', STR_PAD_LEFT);
        $this->assertEquals('ööäää', $text);
    }

    public function testCenterPad()
    {
        $text = Zend_Text_MultiByte::strPad('äää', 6, 'ö', STR_PAD_BOTH);
        $this->assertEquals('öäääöö', $text);
    }

    public function testRightPad()
    {
        $text = Zend_Text_MultiByte::strPad('äääöö', 5, 'ö', STR_PAD_RIGHT);
        $this->assertEquals('äääöö', $text);
    }
}

// Call Zend_Text_MultiByteTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Text_MultiByteTest::main") {
    Zend_Text_MultiByteTest::main();
}
