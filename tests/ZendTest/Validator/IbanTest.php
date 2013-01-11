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
            array('AT61 1904 3002 3457 3201', true),
            array('AD1200012030200354100100', false),

            array('AL47212110090000000235698741', true),
            array('AD1200012030200359100100', true),
            array('AT611904300234573201', true),
            array('AZ21NABZ00000000137010001944', true),
            array('BH67BMAG00001299123456', true),
            array('BE68539007547034', true),
            array('BA391290079401028494', true),
            array('BG80BNBG96611020345678', true),
            array('CR0515202001026284066', true),
            array('HR1210010051863000160', true),
            array('CY17002001280000001200527600', true),
            array('CZ6508000000192000145399', true),
            array('DK5000400440116243', true),
            array('DO28BAGR00000001212453611324', true),
            array('EE382200221020145685', true),
            array('FO6264600001631634', true),
            array('FI2112345600000785', true),
            array('FR1420041010050500013M02606', true),
            array('GE29NB0000000101904917', true),
            array('DE89370400440532013000', true),
            array('GI75NWBK000000007099453', true),
            array('GR1601101250000000012300695', true),
            array('GL8964710001000206', true),
            array('GT82TRAJ01020000001210029690', true),
            array('HU42117730161111101800000000', true),
            array('IS140159260076545510730339', true),
            array('IE29AIBK93115212345678', true),
            array('IL620108000000099999999', true),
            array('IT60X0542811101000000123456', true),
            array('KZ86125KZT5004100100', true),
            array('KW81CBKU0000000000001234560101', true),
            array('LV80BANK0000435195001', true),
            array('LB62099900000001001901229114', true),
            array('LI21088100002324013AA', true),
            array('LT121000011101001000', true),
            array('LU280019400644750000', true),
            array('MK07250120000058984', true),
            array('MT84MALT011000012345MTLCAST001S', true),
            array('MR1300020001010000123456753', true),
            array('MU17BOMM0101101030300200000MUR', true),
            array('MD24AG000225100013104168', true),
            array('MC5811222000010123456789030', true),
            array('ME25505000012345678951', true),
            array('NL91ABNA0417164300', true),
            array('NO9386011117947', true),
            array('PK36SCBL0000001123456702', true),
            array('PL61109010140000071219812874', true),
            array('PT50000201231234567890154', true),
            array('RO49AAAA1B31007593840000', true),
            array('SM86U0322509800000000270100', true),
            array('SA0380000000608010167519', true),
            array('RS35260005601001611379', true),
            array('SK3112000000198742637541', true),
            array('SI56191000000123438', true),
            array('ES9121000418450200051332', true),
            array('SE4550000000058398257466', true),
            array('CH9300762011623852957', true),
            array('TN5910006035183598478831', true),
            array('TR330006100519786457841326', true),
            array('AE070331234567890123456', true),
            array('GB29NWBK60161331926819', true),
            array('VG96VPVG0000012345678901', true),

            array('DO17552081023122561803924090', true),
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
        $this->assertEquals($expected, $validator->isValid($iban), implode("\n", array_merge($validator->getMessages())));
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

    public function testSepaNotSupportedCountryCode()
    {
        $validator = new IbanValidator();
        $this->assertTrue($validator->isValid('DO17552081023122561803924090'));
        $validator->setAllowNonSepa(false);
        $this->assertFalse($validator->isValid('DO17552081023122561803924090'));
        $validator->setAllowNonSepa(true);
        $this->assertTrue($validator->isValid('DO17552081023122561803924090'));
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
