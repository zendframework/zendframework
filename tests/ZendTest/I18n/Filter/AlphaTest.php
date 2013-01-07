<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\Filter;

use Zend\I18n\Filter\Alpha as AlphaFilter;
use Locale;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class AlphaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * AlphaFilter object
     *
     * @var AlphaFilter
     */
    protected $filter;

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     *
     * @var mixed
     **/
    protected static $unicodeEnabled;

    /**
     * Locale in browser.
     *
     * @var string
     */
    protected $locale;

    /**
     * The Alphabet means english alphabet.
     *
     * @var bool
     */
    protected static $meansEnglishAlphabet;

    /**
     * Creates a new AlnumFilter object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->filter = new AlphaFilter();

        $this->locale               = Locale::getDefault();
        $language                   = Locale::getPrimaryLanguage($this->locale);
        self::$meansEnglishAlphabet = in_array($language, array('ja'));
        self::$unicodeEnabled       = (@preg_match('/\pL/u', 'a')) ? true : false;
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        if (!self::$unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $valuesExpected = array(
                'abc123'        => 'abc',
                'abc 123'       => 'abc',
                'abcxyz'        => 'abcxyz',
                ''              => ''
            );
        } elseif (self::$meansEnglishAlphabet) {
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
                'aＡBｂc'       => 'aBc',
                'z Ｙ　x'       => 'zx',
                'Ｗ1v３Ｕ4t'    => 'vt',
                '，sй.rλ:qν＿p' => 'srqp',
                'onml'          => 'onml'
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

        foreach ($valuesExpected as $input => $expected) {
            $actual = $this->filter->filter($input);
            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testAllowWhiteSpace()
    {
        $this->filter->setAllowWhiteSpace(true);

        if (!self::$unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z match
            $valuesExpected = array(
                'abc123'   => 'abc',
                'abc 123'  => 'abc ',
                'abcxyz'   => 'abcxyz',
                ''         => '',
                "\n"       => "\n",
                " \t "     => " \t "
            );
        } if (self::$meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            $valuesExpected = array(
                'a B'    => 'a B',
                'zＹ　x' => 'zx'
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

        foreach ($valuesExpected as $input => $expected) {
            $actual = $this->filter->filter($input);
            $this->assertEquals($expected, $actual);
        }
    }
}
