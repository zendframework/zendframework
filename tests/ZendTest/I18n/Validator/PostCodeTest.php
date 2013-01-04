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

use Zend\I18n\Validator\PostCode as PostCodeValidator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
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
        $this->validator = new PostCodeValidator(array('locale' => 'de_AT'));
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
}
