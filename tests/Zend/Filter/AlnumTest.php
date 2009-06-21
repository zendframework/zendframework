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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_Alnum
 */
require_once 'Zend/Filter/Alnum.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_AlnumTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_Alnum object
     *
     * @var Zend_Filter_Alnum
     */
    protected $_filter;

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     *
     * @var mixed
     **/
    protected static $_unicodeEnabled;

    /**
     * Locale in browser.
     *
     * @var Zend_Locale object
     */
    protected $_locale;

    /**
     * The Alphabet means english alphabet.
     *
     * @var boolean
     */
    protected static $_meansEnglishAlphabet;

    /**
     * Creates a new Zend_Filter_Alnum object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_Alnum();
        if (null === self::$_unicodeEnabled) {
            self::$_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        }
        if (null === self::$_meansEnglishAlphabet) {
            $this->_locale = new Zend_Locale('auto');
            self::$_meansEnglishAlphabet = in_array($this->_locale->getLanguage(),
                                                    array('ja')
                                                    );
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $valuesExpected = array(
                'abc123'  => 'abc123',
                'abc 123' => 'abc123',
                'abcxyz'  => 'abcxyz',
                'AZ@#4.3' => 'AZ43',
                ''        => ''
                );
        } if (self::$_meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            /**
             * The first element contains multibyte alphabets and digits.
             *  But , Zend_Filter_Alnum is expected to return only singlebyte alphabets and digits.
             *
             * The second contains multibyte or singebyte space.
             * The third  contains various multibyte or singebyte characters.
             */
            $valuesExpected = array(
                'aＡBｂ3４5６'  => 'aB35',
                'z７ Ｙ8　x９'  => 'z8x',
                '，s1.2r３#:q,' => 's12rq',
            );
        } else {
            //The Alphabet means each language's alphabet.
            $valuesExpected = array(
                'abc123'  => 'abc123',
                'abc 123' => 'abc123',
                'abcxyz'  => 'abcxyz',
                'če2t3ně'         => 'če2t3ně',
                'grz5e4gżółka'    => 'grz5e4gżółka',
                'Be3l5gië'        => 'Be3l5gië',
                ''        => ''
                );
        }
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testAllowWhiteSpace()
    {
        $this->_filter->setAllowWhiteSpace(true);

        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $valuesExpected = array(
                'abc123'  => 'abc123',
                'abc 123' => 'abc 123',
                'abcxyz'  => 'abcxyz',
                'AZ@#4.3' => 'AZ43',
                ''        => '',
                "\n"      => "\n",
                " \t "    => " \t "
                );
        } if (self::$_meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            $valuesExpected = array(
                'a B ４5'  => 'a B 5',
                'z3　x'  => 'z3x'
                );
        } else {
            //The Alphabet means each language's alphabet.
            $valuesExpected = array(
                'abc123'  => 'abc123',
                'abc 123' => 'abc 123',
                'abcxyz'  => 'abcxyz',
                'če2 t3ně'         => 'če2 t3ně',
                'gr z5e4gżółka'    => 'gr z5e4gżółka',
                'Be3l5 gië'        => 'Be3l5 gië',
                ''        => '',
            );
        }
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }
}