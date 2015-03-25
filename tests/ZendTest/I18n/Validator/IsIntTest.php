<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Validator;

use Zend\I18n\Validator\IsInt as IsIntValidator;
use Locale;

/**
 * @group      Zend_Validator
 */
class IsIntTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Int
     */
    protected $validator;

    /**
     * @var string
     */
    protected $locale;

    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->locale    = Locale::getDefault();
        $this->validator = new IsIntValidator();
    }

    public function tearDown()
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->locale);
        }
    }

    public function intDataProvider()
    {
        return array(
            array(1.00,         true),
            array(0.00,         true),
            array(0.01,         false),
            array(-0.1,         false),
            array(-1,           true),
            array('10',         true),
            array(1,            true),
            array('not an int', false),
            array(true,         false),
            array(false,        false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider intDataProvider()
     * @return void
     */
    public function testBasic($intVal, $expected)
    {
        $this->validator->setLocale('en');
        $this->assertEquals($expected, $this->validator->isValid($intVal));
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
        $this->assertEquals(false, $this->validator->isValid('10 000'));
        $this->assertEquals(true, $this->validator->isValid('10.000'));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7489
     */
    public function testUsingApplicationLocale()
    {
        Locale::setDefault('de');
        $valid = new IsIntValidator();
        $this->assertTrue($valid->isValid('10.000'));
    }

    /**
     * @ZF-7703
     */
    public function testLocaleDetectsNoEnglishLocaleOnOtherSetLocale()
    {
        Locale::setDefault('de');
        $valid = new IsIntValidator();
        $this->assertTrue($valid->isValid(1200));
        $this->assertFalse($valid->isValid('1,200'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals(
            $validator->getOption('messageTemplates'),
            'messageTemplates',
            $validator
        );
    }
}
