<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Validator;

use Zend\I18n\Validator\Date as DateValidator;
use Locale;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class Date extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateValidator
     */
    protected $validator;

    /** @var string */
    protected $locale;

    public function setUp()
    {
        $this->locale    = Locale::getDefault();
        $this->validator = new DateValidator(array('locale' => 'en'));
    }

    public function tearDown()
    {
        Locale::setDefault($this->locale);
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
            array('2/15/2015',   true),
            array('2/15/2015',   true),
            array('2/15/2015',   true),
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
    }

    public function testUsingApplicationLocale()
    {
        $valid = new DateValidator();
        $this->assertEquals(Locale::getDefault(), $valid->getLocale());
    }

    public function testLocaleDeFloatType()
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
        $this->assertEquals(true, $this->validator->isValid('12.Mai.2010'));
    }

    public function deLocaleStringsProvider()
    {
        return array(
            array('12 Mai 2010', true),
            array('12.Mai.2010', true),
            array('12 Mai 10', true),
            array('Mai 12 2010', false),
        );
    }

    /**
     * @dataProvider deLocaleStringsProvider
     */
    public function testPhpLocaleDeStringType($float, $expected)
    {
        Locale::setDefault('de_AT');
        $valid = new DateValidator(array('locale' => 'de_AT'));
        $this->assertEquals($expected, $valid->isValid($float));
    }

    public function frLocaleStringsProvider()
    {
        return array(
            array('18/1/2010', true),
            array('18/01/2010', true),
            array('18 avril 2010', true),
            array('2 janvier 2010', true),
        );
    }

    /**
     * @dataProvider frLocaleStringsProvider
     */
    public function testPhpLocaleFrStringType($float, $expected)
    {
        $valid = new DateValidator(array('locale' => 'fr_FR'));
        $this->assertEquals($expected, $valid->isValid($float));
    }

    public function enLocaleStringsProvider()
    {
        return array(
            array('12/03/2011',     true),
        );
    }

    /**
     * @dataProvider enLocaleStringsProvider
     */
    public function testPhpLocaleEnStringType($float, $expected)
    {
        $valid = new DateValidator(array('locale' => 'en_US'));
        $this->assertEquals($expected, $valid->isValid($float));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
