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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\Alpha as AlphaFilter,
    Zend\Locale\Locale;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class AlphaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * AlphaFilter object
     *
     * @var AlphaFilter
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
     * Creates a new AlphaFilter object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        if (null === self::$_unicodeEnabled) {
            self::$_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        }

        $this->_locale = new Locale('auto');
        if (null === self::$_meansEnglishAlphabet) {
            self::$_meansEnglishAlphabet = in_array($this->_locale->getLanguage(),
                                                    array('ja')
                                                    );
        }
        $this->_filter = new AlphaFilter(array(
            'unicodeEnabled' => self::$_unicodeEnabled,
            'locale'         => $this->_locale,
        ));
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
                'abc123'        => 'abc',
                'abc 123'       => 'abc',
                'abcxyz'        => 'abcxyz',
                ''              => ''
                );
        } else if (self::$_meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            /**
             * The first element contains multibyte alphabets.
             *  But , AlphaFilter is expected to return only singlebyte alphabets.
             * The second contains multibyte or singlebyte space.
             * The third  contains multibyte or singlebyte digits.
             * The forth  contains various multibyte or singlebyte characters.
             * The last contains only singlebyte alphabets.
             */
            $valuesExpected = array(
                'aＡBｂc'  => 'aBc',
                'z Ｙ　x'  => 'zx',
                'Ｗ1v３Ｕ4t' => 'vt',
                '，sй.rλ:qν＿p' => 'srqp',
                'onml' => 'onml'
                );
        } else {
            //The Alphabet means each language's alphabet.
            $valuesExpected = array(
                'abc123'        => 'abc',
                'abc 123'       => 'abc',
                'abcxyz'        => 'abcxyz',
                'četně'         => 'četně',
                'لعربية'        => 'لعربية',
                'grzegżółka'    => 'grzegżółka',
                'België'        => 'België',
                ''              => ''
                );
        }

        $filter = $this->_filter;
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testAllowWhiteSpace()
    {
        $this->_filter->setAllowWhiteSpace(true);
        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $valuesExpected = array(
                'abc123'        => 'abc',
                'abc 123'       => 'abc ',
                'abcxyz'        => 'abcxyz',
                ''              => '',
                "\n"            => "\n",
                " \t "          => " \t "
                );
        } if (self::$_meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            $valuesExpected = array(
                'a B'  => 'a B',
                'zＹ　x'  => 'zx'
                );
        } else {
            //The Alphabet means each language's alphabet.
            $valuesExpected = array(
                'abc123'        => 'abc',
                'abc 123'       => 'abc ',
                'abcxyz'        => 'abcxyz',
                'četně'         => 'četně',
                'لعربية'        => 'لعربية',
                'grzegżółka'    => 'grzegżółka',
                'België'        => 'België',
                ''              => '',
                "\n"            => "\n",
                " \t "          => " \t "
                );
        }

        $filter = $this->_filter;
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }

    /**
     * @group ZF-11631
     */
    public function testRegistryLocale()
    {
        $locale = new Locale('ja');
        \Zend\Registry::set('Zend_Locale', $locale);

        if (!self::$_unicodeEnabled) {
            $this->markTestSkipped('Unicode not enabled');
        }

        $valuesExpected = array(
                'aＡBｂc'  => 'aBc',
                'z Ｙ　x'  => 'zx',
                'Ｗ1v３Ｕ4t' => 'vt',
                '，sй.rλ:qν＿p' => 'srqp',
                'onml' => 'onml'
        );

        $filter = new AlphaFilter();
        $this->assertEquals('ja', (string) $filter->getLocale());

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }
}
