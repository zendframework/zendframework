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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Text;
use Zend\Text;

/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Text
 */
class MultiByteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Standard cut tests
     */
    public function testWordWrapCutSingleLine()
    {
        $line = Text\MultiByte::wordWrap('äbüöcß', 2, ' ', true);
        $this->assertEquals('äb üö cß', $line);
    }

    public function testWordWrapCutMultiLine()
    {
        $line = Text\MultiByte::wordWrap('äbüöc ß äbüöcß', 2, ' ', true);
        $this->assertEquals('äb üö c ß äb üö cß', $line);
    }

    public function testWordWrapCutMultiLineShortWords()
    {
        $line = Text\MultiByte::wordWrap('Ä very long wöööööööööööörd.', 8, "\n", true);
        $this->assertEquals("Ä very\nlong\nwööööööö\nööööörd.", $line);
    }

    public function testWordWrapCutMultiLineWithPreviousNewlines()
    {
        $line = Text\MultiByte::wordWrap("Ä very\nlong wöööööööööööörd.", 8, "\n", false);
        $this->assertEquals("Ä very\nlong\nwöööööööööööörd.", $line);
    }

    /**
     * Long-Break tests
     */
    public function testWordWrapLongBreak()
    {
        $line = Text\MultiByte::wordWrap("Ä very<br>long wöö<br>öööööööö<br>öörd.", 8, '<br>', false);
        $this->assertEquals("Ä very<br>long wöö<br>öööööööö<br>öörd.", $line);
    }

    /**
     * Alternative cut tests
     */
    public function testWordWrapCutBeginningSingleSpace()
    {
        $line = Text\MultiByte::wordWrap(' äüöäöü', 3, ' ', true);
        $this->assertEquals(' äüö äöü', $line);
    }

    public function testWordWrapCutEndingSingleSpace()
    {
        $line = Text\MultiByte::wordWrap('äüöäöü ', 3, ' ', true);
        $this->assertEquals('äüö äöü ', $line);
    }

    public function testWordWrapCutEndingSingleSpaceWithNonSpaceDivider()
    {
        $line = Text\MultiByte::wordWrap('äöüäöü ', 3, '-', true);
        $this->assertEquals('äöü-äöü-', $line);
    }

    public function testWordWrapCutEndingTwoSpaces()
    {
        $line = Text\MultiByte::wordWrap('äüöäöü  ', 3, ' ', true);
        $this->assertEquals('äüö äöü  ', $line);
    }

    public function testWordWrapNoCutEndingSingleSpace()
    {
        $line = Text\Multibyte::wordWrap('12345 ', 5, '-', false);
        $this->assertEquals('12345-', $line);
    }

    public function testWordWrapNoCutEndingTwoSpaces()
    {
        $line = Text\MultiByte::wordWrap('12345  ', 5, '-', false);
        $this->assertEquals('12345- ', $line);
    }

    public function testWordWrapCutEndingThreeSpaces()
    {
        $line = Text\MultiByte::wordWrap('äüöäöü  ', 3, ' ', true);
        $this->assertEquals('äüö äöü  ', $line);
    }

    public function testWordWrapCutEndingTwoBreaks()
    {
        $line = Text\MultiByte::wordWrap('äüöäöü--', 3, '-', true);
        $this->assertEquals('äüö-äöü--', $line);
    }

    public function testWordWrapCutTab()
    {
        $line = Text\MultiByte::wordWrap("äbü\töcß", 3, ' ', true);
        $this->assertEquals("äbü \töc ß", $line);
    }

    public function testWordWrapCutNewlineWithSpace()
    {
        $line = Text\MultiByte::wordWrap("äbü\nößt", 3, ' ', true);
        $this->assertEquals("äbü \nöß t", $line);
    }

    public function testWordWrapCutNewlineWithNewline()
    {
        $line = Text\MultiByte::wordWrap("äbü\nößte", 3, "\n", true);
        $this->assertEquals("äbü\nößt\ne", $line);
    }

    /**
     * Break cut tests
     */
    public function testWordWrapCutBreakBefore()
    {
        $line = Text\MultiByte::wordWrap('foobar-foofoofoo', 8, '-', true);
        $this->assertEquals('foobar-foofoofo-o', $line);
    }

    public function testWordWrapCutBreakWith()
    {
        $line = Text\MultiByte::wordWrap('foobar-foobar', 6, '-', true);
        $this->assertEquals('foobar-foobar', $line);
    }

    public function testWordWrapCutBreakWithin()
    {
        $line = Text\MultiByte::wordWrap('foobar-foobar', 7, '-', true);
        $this->assertEquals('foobar-foobar', $line);
    }

    public function testWordWrapCutBreakWithinEnd()
    {
        $line = Text\MultiByte::wordWrap('foobar-', 7, '-', true);
        $this->assertEquals('foobar-', $line);
    }

    public function testWordWrapCutBreakAfter()
    {
        $line = Text\MultiByte::wordWrap('foobar-foobar', 5, '-', true);
        $this->assertEquals('fooba-r-fooba-r', $line);
    }

    /**
     * Standard no-cut tests
     */
    public function testWordWrapNoCutSingleLine()
    {
        $line = Text\MultiByte::wordWrap('äbüöcß', 2, ' ', false);
        $this->assertEquals('äbüöcß', $line);
    }

    public function testWordWrapNoCutMultiLine()
    {
        $line = Text\MultiByte::wordWrap('äbüöc ß äbüöcß', 2, "\n", false);
        $this->assertEquals("äbüöc\nß\näbüöcß", $line);
    }

    public function testWordWrapNoCutMultiWord()
    {
        $line = Text\MultiByte::wordWrap('äöü äöü äöü', 5, "\n", false);
        $this->assertEquals("äöü\näöü\näöü", $line);
    }

    /**
     * Break no-cut tests
     */
    public function testWordWrapNoCutBreakBefore()
    {
        $line = Text\MultiByte::wordWrap('foobar-foofoofoo', 8, '-', false);
        $this->assertEquals('foobar-foofoofoo', $line);
    }

    public function testWordWrapNoCutBreakWith()
    {
        $line = Text\MultiByte::wordWrap('foobar-foobar', 6, '-', false);
        $this->assertEquals('foobar-foobar', $line);
    }

    public function testWordWrapNoCutBreakWithin()
    {
        $line = Text\MultiByte::wordWrap('foobar-foobar', 7, '-', false);
        $this->assertEquals('foobar-foobar', $line);
    }

    public function testWordWrapNoCutBreakWithinEnd()
    {
        $line = Text\MultiByte::wordWrap('foobar-', 7, '-', false);
        $this->assertEquals('foobar-', $line);
    }

    public function testWordWrapNoCutBreakAfter()
    {
        $line = Text\MultiByte::wordWrap('foobar-foobar', 5, '-', false);
        $this->assertEquals('foobar-foobar', $line);
    }

    /**
     * Pad tests
     */
    public function testLeftPad()
    {
        $text = Text\MultiByte::strPad('äää', 5, 'ö', STR_PAD_LEFT);
        $this->assertEquals('ööäää', $text);
    }

    public function testCenterPad()
    {
        $text = Text\MultiByte::strPad('äää', 6, 'ö', STR_PAD_BOTH);
        $this->assertEquals('öäääöö', $text);
    }

    public function testRightPad()
    {
        $text = Text\MultiByte::strPad('äääöö', 5, 'ö', STR_PAD_RIGHT);
        $this->assertEquals('äääöö', $text);
    }

    public function testWordWrapInvalidArgument()
    {
        $this->setExpectedException('Zend\Text\Exception\InvalidArgumentException', "Cannot force cut when width is zero");
        Text\MultiByte::wordWrap('a', 0, "\n", true);
    }

    /**
     * @group ZF-12186
     */
    public function testPadInputLongerThanPadLength()
    {
        $text = Text\MultiByte::strPad('äääöö', 2, 'ö');
        $this->assertEquals('äääöö', $text);
    }

    /**
     * @group ZF-12186
     */
    public function testPadInputSameAsPadLength()
    {
        $text = Text\MultiByte::strPad('äääöö', 5, 'ö');
        $this->assertEquals('äääöö', $text);
    }

    /**
     * @group ZF-12186
     */
    public function testPadNegativePadLength()
    {
        $text = Text\MultiByte::strPad('äääöö', -2, 'ö');
        $this->assertEquals('äääöö', $text);
    }
}
