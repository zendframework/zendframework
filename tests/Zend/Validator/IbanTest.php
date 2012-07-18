<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Validator;

use Zend\Validator\Iban as IbanValidator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class IbanTest extends \PHPUnit_Framework_TestCase
{
    public function ibanDataProvider()
    {
        return array(
            array('AD1200012030200359100100', true),
            array('AT611904300234573201',     true),
            array('AT61 1904 3002 3457 3201', false),
            array('AD1200012030200354100100', false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider ibanDataProvider
     * @return void
     */
    public function testBasic($iban, $expected)
    {
        $validator = new IbanValidator();
        $this->assertEquals($expected, $validator->isValid($iban));
    }

    public function testSettingAndGettingCountryCode()
    {
        $validator = new IbanValidator();

        $validator->setCountryCode('DE');
        $this->assertEquals('DE', $validator->getCountryCode());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'ISO 3166-1');
        $validator->setCountryCode('foo');
    }

    public function testInstanceWithCountryCode()
    {
        $validator = new IbanValidator(array('country_code' => 'AT'));
        $this->assertEquals('AT', $validator->getCountryCode());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'ISO 3166-1');
        $validator = new IbanValidator(array('country_code' => 'BAR'));
    }

    public function testIbanNotSupportedCountryCode()
    {
        $validator = new IbanValidator();
        $this->assertFalse($validator->isValid('US611904300234573201'));
    }

    /**
     * @group ZF-10556
     */
    public function testIbanDetectionWithoutCountryCode()
    {
        $validator = new IbanValidator();
        $this->assertTrue($validator->isValid('AT611904300234573201'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new IbanValidator();
        $this->assertAttributeEquals(
            $validator->getOption('messageTemplates'),
            'messageTemplates',
            $validator
        );
    }
}
