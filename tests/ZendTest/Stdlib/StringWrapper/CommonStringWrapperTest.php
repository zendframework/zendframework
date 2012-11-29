<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */

namespace ZendTest\Stdlib\StringWrapper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\Exception;
use Zend\Stdlib\StringWrapper\StringWrapperInterface;

abstract class CommonStringWrapperTest extends TestCase
{

    /**
     * An instance of the string wrapper to test
     * @StringWrapperInterface
     */
    protected $stringWrapper;

    public function setUp()
    {
        if ( !($this->stringWrapper instanceof StringWrapperInterface) ) {
            $this->fail(sprintf(
                "%s isn't an instance of %s",
                get_class($this) . '::stringWrapper',
                'Zend\Stdlib\StringWrapper\StringWrapperInterface'
            ));
        }
    }

    public function strlenProvider()
    {
        return array(
            array('abcdefghijklmnopqrstuvwxyz', 'ascii', 26),
            array('abcdefghijklmnopqrstuvwxyz', 'utf-8', 26),
            array('äöüß',                       'utf-8', 4),
        );
    }

    /**
     * @dataProvider strlenProvider
     * @param string $string
     * @param string $charset
     * @param mixed  $expected
     */
    public function testStrlen($str, $charset, $expected)
    {
        if (!$this->stringWrapper->isCharsetSupported($charset)) {
            $this->markTestSkipped(
                "Charset {$charset} not supported by " . get_class($this->stringWrapper)
            );
        }

        $result = $this->stringWrapper->strlen($str, $charset);
        $this->assertSame($expected, $result);
    }

    public function substrProvider()
    {
        return array(
            array('abcdefghijkl', 1, 5, 'ascii', 'bcdef'),
            array('abcdefghijkl', 1, 5, 'utf-8', 'bcdef'),
            array('äöüß',         1, 2, 'utf-8', 'öü'),
        );
    }

    /**
     * @dataProvider substrProvider
     * @param string   $str
     * @param int      $offset
     * @param int|null $length
     * @param string   $charset
     * @param mixed    $expected
     */
    public function testSubstr($str, $offset, $length, $charset, $expected)
    {
        if (!$this->stringWrapper->isCharsetSupported($charset)) {
            $this->markTestSkipped(
                "Charset {$charset} not supported by " . get_class($this->stringWrapper)
            );
        }

        $result = $this->stringWrapper->substr($str, $offset, $length, $charset);
        $this->assertSame($expected, $result);
    }

    public function strposProvider()
    {
        return array(
            array('abcdefghijkl', 'g', 3, 'ascii', 6),
            array('abcdefghijkl', 'g', 3, 'utf-8', 6),
            array('äöüß',         'ü', 1, 'utf-8', 2),
        );
    }

    /**
     * @dataProvider strposProvider
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @param string $charset
     * @param mixed  $expected
     */
    public function testStrpos($haystack, $needle, $offset, $charset, $expected)
    {
        if (!$this->stringWrapper->isCharsetSupported($charset)) {
            $this->markTestSkipped(
                "Charset {$charset} not supported by " . get_class($this->stringWrapper)
            );
        }

        $result = $this->stringWrapper->strpos($haystack, $needle, $offset, $charset);
        $this->assertSame($expected, $result);
    }

    public function convertProvider()
    {
        return array(
            array('abc', 'ascii',       'ascii', 'abc'),
            array('abc', 'utf-8',       'ascii', 'abc'),
            array('abc', 'ascii',       'utf-8', 'abc'),
            array('€',   'iso-8859-15', 'utf-8', "\xA4"),
            array('€',   'iso-8859-16', 'utf-8', "\xA4"), // ISO-8859-16 is wrong @ mbstring
        );
    }

    /**
     * @dataProvider convertProvider
     * @param string $str
     * @param string $toCharset
     * @param string $fromCharset
     * @param mixed  $expected
     */
    public function testConvert($str, $toCharset, $fromCharset, $expected)
    {
        if (!$this->stringWrapper->isCharsetSupported($toCharset)) {
            $this->markTestSkipped(
                "Charset {$toCharset} not supported by " . get_class($this->stringWrapper)
            );
        } elseif (!$this->stringWrapper->isCharsetSupported($fromCharset)) {
            $this->markTestSkipped(
                "Charset {$fromCharset} not supported by " . get_class($this->stringWrapper)
            );
        }

        $result = $this->stringWrapper->convert($str, $toCharset, $fromCharset);
        $this->assertSame($expected, $result);
    }

    public function wordWrapProvider()
    {
        return array(
            // Standard cut tests
            array('äbüöcß', 2, ' ', true, 'utf-8',
                 'äb üö cß'),
            array('äbüöc ß äbüöcß', 2, ' ', true, 'utf-8',
                  'äb üö c ß äb üö cß'),
            array('Ä very long wöööööööööööörd.', 8, "\n", true, 'utf-8',
                  "Ä very\nlong\nwööööööö\nööööörd."),
            array("Ä very\nlong wöööööööööööörd.", 8, "\n", false, 'utf-8',
                  "Ä very\nlong\nwöööööööööööörd."),
            array("Ä very<br>long wöö<br>öööööööö<br>öörd.", 8, '<br>', false, 'utf-8',
                  "Ä very<br>long wöö<br>öööööööö<br>öörd."),

            // Alternative cut tests
            array(' äüöäöü', 3, ' ', true, 'utf-8',
                  ' äüö äöü'),
            array('äüöäöü ', 3, ' ', true, 'utf-8',
                  'äüö äöü '),
            array('äöüäöü ', 3, '-', true, 'utf-8',
                  'äöü-äöü-'),
            array('äüöäöü  ', 3, ' ', true, 'utf-8',
                  'äüö äöü  '),
            array('12345 ', 5, '-', false, 'utf-8',
                  '12345-'),
            array('12345  ', 5, '-', false, 'utf-8',
                  '12345- '),
            array('äüöäöü  ', 3, ' ', true, 'utf-8',
                  'äüö äöü  '),
            array('äüöäöü--', 3, '-', true, 'utf-8',
                  'äüö-äöü--'),
            array("äbü\töcß", 3, ' ', true, 'utf-8',
                  "äbü \töc ß"),
            array("äbü\nößt", 3, ' ', true, 'utf-8',
                  "äbü \nöß t"),
            array("äbü\nößte", 3, "\n", true, 'utf-8',
                  "äbü\nößt\ne"),

            // Break cut tests
            array('foobar-foofoofoo', 8, '-', true, 'ascii',
                  'foobar-foofoofo-o'),
            array('foobar-foobar', 6, '-', true, 'ascii',
                  'foobar-foobar'),
            array('foobar-foobar', 7, '-', true, 'ascii',
                  'foobar-foobar'),
            array('foobar-', 7, '-', true, 'ascii',
                  'foobar-'),
            array('foobar-foobar', 5, '-', true, 'ascii',
                  'fooba-r-fooba-r'),

            // Standard no-cut tests
            array('äbüöcß', 2, ' ', false, 'utf-8',
                  'äbüöcß'),
            array('äbüöc ß äbüöcß', 2, "\n", false, 'utf-8',
                  "äbüöc\nß\näbüöcß"),
            array('äöü äöü äöü', 5, "\n", false, 'utf-8',
                  "äöü\näöü\näöü"),

            // Break no-cut tests
            array('foobar-foofoofoo', 8, '-', false, 'ascii',
                  'foobar-foofoofoo'),
            array('foobar-foobar', 6, '-', false, 'ascii',
                  'foobar-foobar'),
            array('foobar-foobar', 7, '-', false, 'ascii',
                  'foobar-foobar'),
            array('foobar-', 7, '-', false, 'ascii',
                  'foobar-'),
            array('foobar-foobar', 5, '-', false, 'ascii',
                  'foobar-foobar'),
        );
    }

    /**
     * @dataProvider wordWrapProvider
     * @param string  $str
     * @param integer $width
     * @param string  $break
     * @param boolean $cut
     * @param string  $charset
     * @param mixed   $expected
     */
    public function testWordWrap($string, $width, $break, $cut, $charset, $expected)
    {
        if (!$this->stringWrapper->isCharsetSupported($charset)) {
            $this->markTestSkipped(
                "Charset {$charset} not supported by " . get_class($this->stringWrapper)
            );
        }

        $result = $this->stringWrapper->wordWrap($string, $width, $break, $cut, $charset);
        $this->assertSame($expected, $result);
    }

    public function testWordWrapInvalidArgument()
    {
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            "Cannot force cut when width is zero"
        );
        $this->stringWrapper->wordWrap('a', 0, "\n", true);
    }

    public function strPadProvider()
    {
        return array(
            // single-byte
            array('aaa', 5, 'o', STR_PAD_LEFT, 'ascii', 'ooaaa'),
            array('aaa', 6, 'o', STR_PAD_BOTH, 'ascii', 'oaaaoo'),
            array('aaa', 5, 'o', STR_PAD_RIGHT, 'ascii', 'aaaoo'),

            // multi-byte
            array('äää', 5, 'ö', STR_PAD_LEFT, 'utf-8', 'ööäää'),
            array('äää', 6, 'ö', STR_PAD_BOTH, 'utf-8', 'öäääöö'),
            array('äää', 5, 'ö', STR_PAD_RIGHT, 'utf-8', 'äääöö'),

            // ZF-12186
            array('äääöö', 2, 'ö', STR_PAD_RIGHT, 'utf-8', 'äääöö'),  // PadInputLongerThanPadLength
            array('äääöö', 5, 'ö', STR_PAD_RIGHT, 'utf-8', 'äääöö'),  // PadInputSameAsPadLength
            array('äääöö', -2, 'ö', STR_PAD_RIGHT, 'utf-8', 'äääöö'), // PadNegativePadLength
        );
    }

    /**
     * @dataProvider strPadProvider
     * @param  string  $input
     * @param  integer $padLength
     * @param  string  $padString
     * @param  integer $padType
     * @param  string  $charset
     * @param mixed   $expected
     *
     * @group ZF-12186
     */
    public function testStrPad($input, $padLength, $padString, $padType, $charset, $expected)
    {
        if (!$this->stringWrapper->isCharsetSupported($charset)) {
            $this->markTestSkipped(
                "Charset {$charset} not supported by " . get_class($this->stringWrapper)
            );
        }

        $result = $this->stringWrapper->strPad($input, $padLength, $padString, $padType, $charset);
        $this->assertSame($expected, $result);
    }
}
