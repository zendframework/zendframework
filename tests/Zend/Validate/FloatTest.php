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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_Float
 */
require_once 'Zend/Validate/Float.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_FloatTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate_Float object
     *
     * @var Zend_Validate_Float
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Float object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_locale = setlocale(LC_ALL, 0); //backup locale

        require_once 'Zend/Registry.php';
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            Zend_Registry::getInstance()->offsetUnset('Zend_Locale');
        }

        $this->_validator = new Zend_Validate_Float();
    }

    public function tearDown()
    {
        //restore locale
        if (is_string($this->_locale) && strpos($this->_locale, ';')) {
            $locales = array();
            foreach (explode(';', $this->_locale) as $l) {
                $tmp = explode('=', $l);
                $locales[$tmp[0]] = $tmp[1];
            }
            setlocale(LC_ALL, $locales);
            return;
        }
        setlocale(LC_ALL, $this->_locale);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(1.00, true),
            array(0.01, true),
            array(-0.1, true),
            array('10.1', true),
            array(1, true),
            array('not a float', false),
            );
        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]));
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensures that set/getLocale() works
     */
    public function testSettingLocales()
    {
        $this->_validator->setLocale('de');
        $this->assertEquals('de', $this->_validator->getLocale());
        $this->assertEquals(true, $this->_validator->isValid('10,5'));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7489
     */
    public function testUsingApplicationLocale()
    {
        Zend_Registry::set('Zend_Locale', new Zend_Locale('de'));
        $valid = new Zend_Validate_Float();
        $this->assertTrue($valid->isValid('123,456'));
    }

    /**
     * @ZF-7987
     */
    public function testNoZendLocaleButPhpLocale()
    {
        setlocale(LC_ALL, 'de');
        $valid = new Zend_Validate_Float();
        $this->assertTrue($valid->isValid(123,456));
    }

    /**
     * @ZF-7987
     */
    public function testLocaleDeFloatType()
    {
        $this->_validator->setLocale('de');
        $this->assertEquals('de', $this->_validator->getLocale());
        $this->assertEquals(true, $this->_validator->isValid(10.5));
    }

    /**
     * @ZF-7987
     */
    public function testPhpLocaleDeFloatType()
    {
        setlocale(LC_ALL, 'de');
        $valid = new Zend_Validate_Float();
        $this->assertTrue($valid->isValid(10.5));
    }
}
