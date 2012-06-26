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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\Float as FloatValidator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class FloatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FloatValidator
     */
    protected $validator;

    /** @var string */
    protected $locale;
    public function setUp()
    {
        $this->locale    = setlocale(LC_ALL, 0); //backup locale
        $this->validator = new FloatValidator();
    }

    public function tearDown()
    {
        //restore locale
        if (is_string($this->locale) && strpos($this->locale, ';')) {
            $locales = array();
            foreach (explode(';', $this->locale) as $l) {
                $tmp = explode('=', $l);
                $locales[$tmp[0]] = $tmp[1];
            }
            setlocale(LC_ALL, $locales);
            return;
        }
        setlocale(LC_ALL, $this->locale);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicProvider
     * @return void
     */
    public function testBasic($value, $expected)
    {
        $this->assertEquals($expected, $this->validator->isValid($value),
                            'Failed expecting ' . $value . ' being ' . ($expected ? 'true' : 'false'));
    }

    public function basicProvider()
    {
        return array(
            array(1.00, true),
            array(0.01, true),
            array(-0.1, true),
            array('10.1', true),
            array(1, true),
            array('not a float', false),
        );
    }
    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures that set/getLocale() works
     */
    public function testSettingLocales()
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
        $this->assertEquals(true, $this->validator->isValid('10,5'));
    }

    /**
     * @group ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7987
     */
    public function testNoZendLocaleButPhpLocale()
    {
        setlocale(LC_ALL, 'de');
        $valid = new FloatValidator();
        $this->assertTrue($valid->isValid(123,456));
        $this->assertTrue($valid->isValid('123,456'));
    }

    /**
     * @ZF-7987
     */
    public function testLocaleDeFloatType()
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
        $this->assertEquals(true, $this->validator->isValid(10.5));
    }

    /**
     * @ZF-7987
     */
    public function testPhpLocaleDeFloatType()
    {
        setlocale(LC_ALL, 'de');
        $valid = new FloatValidator();
        $this->assertTrue($valid->isValid(10.5));
    }

    /**
     * @ZF-7987
     */
    public function testPhpLocaleFrFloatType()
    {
        setlocale(LC_ALL, 'fr');
        $valid = new FloatValidator();
        $this->assertTrue($valid->isValid(10.5));
    }

    /**
     * @ZF-8919
     */
    public function testPhpLocaleDeStringType()
    {
        setlocale(LC_ALL, 'de_AT');
        setlocale(LC_NUMERIC, 'de_AT');
        $valid = new FloatValidator('de_AT');
        $this->assertTrue($valid->isValid('1,3'));
        $this->assertTrue($valid->isValid('1000,3'));
        $this->assertTrue($valid->isValid('1.000,3'));
        $this->assertFalse($valid->isValid('1.3'));
        $this->assertFalse($valid->isValid('1000.3'));
        $this->assertFalse($valid->isValid('1,000.3'));
    }

    /**
     * @ZF-8919
     */
    public function testPhpLocaleFrStringType()
    {
        $valid = new FloatValidator('fr_FR');
        $this->assertTrue($valid->isValid('1,3'));
        $this->assertTrue($valid->isValid('1000,3'));
        $this->assertTrue($valid->isValid('1 000,3'));
        $this->assertFalse($valid->isValid('1.3'));
        $this->assertFalse($valid->isValid('1000.3'));
        $this->assertFalse($valid->isValid('1,000.3'));
    }

    /**
     * @ZF-8919
     */
    public function testPhpLocaleEnStringType()
    {
        $valid = new FloatValidator('en_US');
        $this->assertTrue($valid->isValid('1.3'));
        $this->assertTrue($valid->isValid('1000.3'));
        $this->assertTrue($valid->isValid('1,000.3'));
        $this->assertFalse($valid->isValid('1,3'));
        $this->assertFalse($valid->isValid('1000,3'));
        $this->assertFalse($valid->isValid('1.000,3'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
