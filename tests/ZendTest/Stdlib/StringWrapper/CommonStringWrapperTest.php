<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */

namespace ZendTest\Stdlib\StringWrapper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\ErrorHandler;
use Zend\Stdlib\Exception;
use Zend\Stdlib\StringWrapper\StringWrapperInterface;

abstract class CommonStringWrapperTest extends TestCase
{
    abstract protected function getWrapper($encoding = null, $convertEncoding = null);

    public function strlenProvider()
    {
        return array(
            array('ascii', 'abcdefghijklmnopqrstuvwxyz', 26),
            array('utf-8', 'abcdefghijklmnopqrstuvwxyz', 26),
            array('utf-8', 'äöüß',                       4),
        );
    }

    /**
     * @dataProvider strlenProvider
     * @param string $encoding
     * @param string $string
     * @param mixed  $expected
     */
    public function testStrlen($encoding, $str, $expected)
    {
        $wrapper = $this->getWrapper($encoding);
        if (!$wrapper) {
            $this->markTestSkipped("Encoding {$encoding} not supported");
        }

        $result = $wrapper->strlen($str);
        $this->assertSame($expected, $result);
    }

    public function substrProvider()
    {
        return array(
            array('ascii', 'abcdefghijkl', 1, 5, 'bcdef'),
            array('utf-8', 'abcdefghijkl', 1, 5, 'bcdef'),
            array('utf-8', 'äöüß',         1, 2, 'öü'),
        );
    }

    /**
     * @dataProvider substrProvider
     * @param string   $encoding
     * @param string   $str
     * @param int      $offset
     * @param int|null $length
     * @param mixed    $expected
     */
    public function testSubstr($encoding, $str, $offset, $length, $expected)
    {
        $wrapper = $this->getWrapper($encoding);
        if (!$wrapper) {
            $this->markTestSkipped("Encoding {$encoding} not supported");
        }

        $result = $wrapper->substr($str, $offset, $length);
        $this->assertSame($expected, $result);
    }

    public function strposProvider()
    {
        return array(
            array('ascii', 'abcdefghijkl', 'g', 3, 6),
            array('utf-8', 'abcdefghijkl', 'g', 3, 6),
            array('utf-8', 'äöüß',         'ü', 1, 2),
        );
    }

    /**
     * @dataProvider strposProvider
     * @param string $encoding
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @param mixed  $expected
     */
    public function testStrpos($encoding, $haystack, $needle, $offset, $expected)
    {
        $wrapper = $this->getWrapper($encoding);
        if (!$wrapper) {
            $this->markTestSkipped("Encoding {$encoding} not supported");
        }

        $result = $wrapper->strpos($haystack, $needle, $offset);
        $this->assertSame($expected, $result);
    }

    public function convertProvider()
    {
        return array(
            array('ascii', 'ascii', 'abc', 'abc'),
            array('ascii', 'utf-8', 'abc', 'abc'),
            array('utf-8', 'ascii', 'abc', 'abc'),
            array('utf-8', 'iso-8859-15', '€',   "\xA4"),
            array('utf-8', 'iso-8859-16', '€',   "\xA4"), // ISO-8859-16 is wrong @ mbstring
        );
    }

    /**
     * @dataProvider convertProvider
     * @param string $str
     * @param string $encoding
     * @param string $convertEncoding
     * @param mixed  $expected
     */
    public function testConvert($encoding, $convertEncoding, $str, $expected)
    {
        $wrapper = $this->getWrapper($encoding, $convertEncoding);
        if (!$wrapper) {
            $this->markTestSkipped("Encoding {$encoding} or {$convertEncoding} not supported");
        }

        $result = $wrapper->convert($str);
        $this->assertSame($expected, $result);

        // backword
        $result = $wrapper->convert($expected, true);
        $this->assertSame($str, $result);
    }

    public function wordWrapProvider()
    {
        return array(
            // Standard cut tests
            'cut-single-line' =>
                array('utf-8', 'äbüöcß', 2, ' ', true, 'äb üö cß'),
            'cut-multi-line' =>
                array('utf-8', 'äbüöc ß äbüöcß', 2, ' ', true, 'äb üö c ß äb üö cß'),
            'cut-multi-line-short-words' =>
                array('utf-8', 'Ä very long wöööööööööööörd.', 8, "\n", true,
                      "Ä very\nlong\nwööööööö\nööööörd."),
            'cut-multi-line-with-previous-new-lines' =>
                array('utf-8', "Ä very\nlong wöööööööööööörd.", 8, "\n", false,
                      "Ä very\nlong\nwöööööööööööörd."),
            'long-break' =>
                array('utf-8', "Ä very<br>long wöö<br>öööööööö<br>öörd.", 8, '<br>', false,
                      "Ä very<br>long wöö<br>öööööööö<br>öörd."),

            // Alternative cut tests
            'cut-beginning-single-space' =>
                array('utf-8', ' äüöäöü', 3, ' ', true, ' äüö äöü'),
            'cut-ending-single-space' =>
                array('utf-8', 'äüöäöü ', 3, ' ', true, 'äüö äöü '),
            'cut-ending-single-space-with-non-space-divider' =>
                array('utf-8', 'äöüäöü ', 3, '-', true, 'äöü-äöü-'),
            'cut-ending-two-spaces' =>
                array('utf-8', 'äüöäöü  ', 3, ' ', true, 'äüö äöü  '),
            'no-cut-ending-single-space' =>
                array('utf-8', '12345 ', 5, '-', false, '12345-'),
            'no-cut-ending-two-spaces' =>
                array('utf-8', '12345  ', 5, '-', false, '12345- '),
            'cut-ending-three-spaces' =>
                array('utf-8', 'äüöäöü  ', 3, ' ', true, 'äüö äöü  '),
            'cut-ending-two-breaks' =>
                array('utf-8', 'äüöäöü--', 3, '-', true, 'äüö-äöü--'),
            'cut-tab' =>
                array('utf-8', "äbü\töcß", 3, ' ', true, "äbü \töc ß"),
            'cut-new-line-with-space' =>
                array('utf-8', "äbü\nößt", 3, ' ', true, "äbü \nöß t"),
            'cut-new-line-with-new-line' =>
                array('utf-8', "äbü\nößte", 3, "\n", true, "äbü\nößt\ne"),

            // Break cut tests
            'cut-break-before' =>
                array('ascii', 'foobar-foofoofoo', 8, '-', true, 'foobar-foofoofo-o'),
            'cut-break-with' =>
                array('ascii', 'foobar-foobar', 6, '-', true, 'foobar-foobar'),
            'cut-break-within' =>
                array('ascii', 'foobar-foobar', 7, '-', true, 'foobar-foobar'),
            'cut-break-within-end' =>
                array('ascii', 'foobar-', 7, '-', true, 'foobar-'),
            'cut-break-after' =>
                array('ascii', 'foobar-foobar', 5, '-', true, 'fooba-r-fooba-r'),

            // Standard no-cut tests
            'no-cut-single-line' =>
                array('utf-8', 'äbüöcß', 2, ' ', false, 'äbüöcß'),
            'no-cut-multi-line' =>
                array('utf-8', 'äbüöc ß äbüöcß', 2, "\n", false, "äbüöc\nß\näbüöcß"),
            'no-cut-multi-word' =>
                array('utf-8', 'äöü äöü äöü', 5, "\n", false, "äöü\näöü\näöü"),

            // Break no-cut tests
            'no-cut-break-before' =>
                array('ascii', 'foobar-foofoofoo', 8, '-', false, 'foobar-foofoofoo'),
            'no-cut-break-with' =>
                array('ascii', 'foobar-foobar', 6, '-', false, 'foobar-foobar'),
            'no-cut-break-within' =>
                array('ascii', 'foobar-foobar', 7, '-', false, 'foobar-foobar'),
            'no-cut-break-within-end' =>
                array('ascii', 'foobar-', 7, '-', false, 'foobar-'),
            'no-cut-break-after' =>
                array('ascii', 'foobar-foobar', 5, '-', false, 'foobar-foobar'),
        );
    }

    /**
     * @dataProvider wordWrapProvider
     * @param string  $encoding
     * @param string  $str
     * @param integer $width
     * @param string  $break
     * @param boolean $cut
     * @param mixed   $expected
     */
    public function testWordWrap($encoding, $string, $width, $break, $cut, $expected)
    {
        $wrapper = $this->getWrapper($encoding);
        if (!$wrapper) {
            $this->markTestSkipped("Encoding {$encoding} not supported");
        }

        $result = $wrapper->wordWrap($string, $width, $break, $cut);
        $this->assertSame($expected, $result);
    }

    public function testWordWrapInvalidArgument()
    {
        $wrapper = $this->getWrapper();
        if (!$wrapper) {
            $this->fail("Can't instantiate wrapper");
        }

        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            "Cannot force cut when width is zero"
        );
        $wrapper->wordWrap('a', 0, "\n", true);
    }

    public function strPadProvider()
    {
        return array(
            // single-byte
            'left-padding_single-byte' =>
                array('ascii', 'aaa', 5, 'o', STR_PAD_LEFT, 'ooaaa'),
            'center-padding_single-byte' =>
                array('ascii', 'aaa', 6, 'o', STR_PAD_BOTH, 'oaaaoo'),
            'right-padding_single-byte' =>
                array('ascii', 'aaa', 5, 'o', STR_PAD_RIGHT, 'aaaoo'),

            // multi-byte
            'left-padding_multi-byte' =>
                array('utf-8', 'äää', 5, 'ö', STR_PAD_LEFT, 'ööäää'),
            'center-padding_multi-byte' =>
                array('utf-8', 'äää', 6, 'ö', STR_PAD_BOTH, 'öäääöö'),
            'right-padding_multi-byte' =>
                array('utf-8', 'äää', 5, 'ö', STR_PAD_RIGHT, 'äääöö'),

            // ZF-12186
            'input-longer-than-pad-length' =>
                array('utf-8', 'äääöö', 2, 'ö', STR_PAD_RIGHT, 'äääöö'),
            'input-same-as-pad-length' =>
                array('utf-8', 'äääöö', 5, 'ö', STR_PAD_RIGHT, 'äääöö'),
            'negative-pad-length' =>
                array('utf-8', 'äääöö', -2, 'ö', STR_PAD_RIGHT, 'äääöö'),
        );
    }

    /**
     * @dataProvider strPadProvider
     * @param  string  $encoding
     * @param  string  $input
     * @param  integer $padLength
     * @param  string  $padString
     * @param  integer $padType
     * @param mixed   $expected
     *
     * @group ZF-12186
     */
    public function testStrPad($encoding, $input, $padLength, $padString, $padType, $expected)
    {
        $wrapper = $this->getWrapper($encoding);
        if (!$wrapper) {
            $this->markTestSkipped("Encoding {$encoding} not supported");
        }

        $result = $wrapper->strPad($input, $padLength, $padString, $padType);
        $this->assertSame($expected, $result);
    }
}
