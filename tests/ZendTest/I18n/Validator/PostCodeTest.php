<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Validator;

use Zend\I18n\Validator\PostCode as PostCodeValidator;

/**
 * @group      Zend_Validator
 */
class PostCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  PostCode
     */
    protected $validator;

    /**
     * Creates a new Zend\PostCode object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->validator = new PostCodeValidator(array('locale' => 'de_AT'));
    }

    /**
     * @dataProvider UKPostCodesDataProvider
     * @group #7250
     * @group #7264
     */
    public function testUKBasic($postCode, $expected)
    {
        $uk_validator = new PostCodeValidator(array('locale' => 'en_GB'));
        $this->assertSame($expected, $uk_validator->isValid($postCode));
    }

    public function UKPostCodesDataProvider()
    {
        return array(
            array('CA3 5JQ', true),
            array('GL15 2GB', true),
            array('GL152GB', true),
            array('ECA32 6JQ', false),
            array('se5 0eg', false),
            array('SE5 0EG', true),
            array('ECA3 5JQ', false),
            array('WC2H 7LTa', false),
            array('WC2H 7LTA', false),
        );
    }

    public function postCodesDataProvider()
    {
        return array(
            array('2292',    true),
            array('1000',    true),
            array('0000',    true),
            array('12345',   false),
            array(1234,      true),
            array(9821,      true),
            array('21A4',    false),
            array('ABCD',    false),
            array(true,      false),
            array('AT-2292', false),
            array(1.56,      false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider postCodesDataProvider
     * @return void
     */
    public function testBasic($postCode, $expected)
    {
        $this->assertEquals($expected, $this->validator->isValid($postCode));
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
     * Ensures that a region is available
     */
    public function testSettingLocalesWithoutRegion()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Locale must contain a region');
        $this->validator->setLocale('de')->isValid('1000');
    }

    /**
     * Ensures that the region contains postal codes
     */
    public function testSettingLocalesWithoutPostalCodes()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'A postcode-format string has to be given for validation');
        $this->validator->setLocale('gez_ER')->isValid('1000');
    }

    /**
     * Ensures locales can be retrieved
     */
    public function testGettingLocale()
    {
        $this->assertEquals('de_AT', $this->validator->getLocale());
    }

    /**
     * Ensures format can be set and retrieved
     */
    public function testSetGetFormat()
    {
        $this->validator->setFormat('\d{1}');
        $this->assertEquals('\d{1}', $this->validator->getFormat());
    }

    public function testSetGetFormatThrowsExceptionOnNullFormat()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'A postcode-format string has to be given');
        $this->validator->setLocale(null)->setFormat(null)->isValid('1000');
    }

    public function testSetGetFormatThrowsExceptionOnEmptyFormat()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'A postcode-format string has to be given');
        $this->validator->setLocale(null)->setFormat('')->isValid('1000');
    }

    /**
     * @group ZF-9212
     */
    public function testErrorMessageText()
    {
        $this->assertFalse($this->validator->isValid('hello'));
        $message = $this->validator->getMessages();
        $this->assertContains('not appear to be a postal code', $message['postcodeNoMatch']);
    }

     /**
     * Test service class with invalid validation
     *
     * @group ZF2-44
     */
    public function testServiceClass()
    {
        $params = (object)array(
            'serviceTrue'   => null,
            'serviceFalse'  => null,
        );

        $serviceTrue  = function ($value) use ($params) {
            $params->serviceTrue = $value;
            return true;
        };

        $serviceFalse = function ($value) use ($params) {
            $params->serviceFalse = $value;
            return false;
        };

        $this->assertEquals(null, $this->validator->getService());


        $this->validator->setService($serviceTrue);
        $this->assertEquals($this->validator->getService(), $serviceTrue);
        $this->assertTrue($this->validator->isValid('2292'));
        $this->assertEquals($params->serviceTrue, '2292');


        $this->validator->setService($serviceFalse);
        $this->assertEquals($this->validator->getService(), $serviceFalse);
        $this->assertFalse($this->validator->isValid('hello'));
        $this->assertEquals($params->serviceFalse, 'hello');

        $message = $this->validator->getMessages();
        $this->assertContains('not appear to be a postal code', $message['postcodeService']);
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    /**
     * Post codes are provided by French government official post code database
     * https://www.data.gouv.fr/fr/datasets/base-officielle-des-codes-postaux/
     */
    public function testFrPostCodes()
    {
        $validator = $this->validator;
        $validator->setLocale('fr_FR');

        $this->assertTrue($validator->isValid('13100')); // AIX EN PROVENCE
        $this->assertTrue($validator->isValid('97439')); // STE ROSE
        $this->assertTrue($validator->isValid('98790')); // MAHETIKA
        $this->assertFalse($validator->isValid('00000')); // Post codes starting with 00 don't exist
        $this->assertFalse($validator->isValid('96000')); // Post codes starting with 96 don't exist
        $this->assertFalse($validator->isValid('99000')); // Post codes starting with 99 don't exist
    }

    /**
     * Post codes are provided by Norway Mail database
     * http://www.bring.no/hele-bring/produkter-og-tjenester/brev-og-postreklame/andre-tjenester/postnummertabeller
     */
    public function testNoPostCodes()
    {
        $validator = $this->validator;
        $validator->setLocale('en_NO');

        $this->assertTrue($validator->isValid('0301')); // OSLO
        $this->assertTrue($validator->isValid('9910')); // BJØRNEVATN
        $this->assertFalse($validator->isValid('0000')); // Postal code 0000
    }
}
