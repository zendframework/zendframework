<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use Zend\Config;
use Zend\Validator\CreditCard;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class CreditCardTest extends \PHPUnit_Framework_TestCase
{
    public static function basicValues()
    {
        return array(
            array('4111111111111111', true),
            array('5404000000000001', true),
            array('374200000000004', true),
            array('4444555566667777', false),
            array('ABCDEF', false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicValues
     */
    public function testBasic($input, $expected)
    {
        $validator      = new CreditCard();
        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new CreditCard();
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that get and setType works as expected
     *
     * @return void
     */
    public function testGetSetType()
    {
        $validator = new CreditCard();
        $this->assertEquals(11, count($validator->getType()));

        $validator->setType(CreditCard::MAESTRO);
        $this->assertEquals(array(CreditCard::MAESTRO), $validator->getType());

        $validator->setType(
            array(
                CreditCard::AMERICAN_EXPRESS,
                CreditCard::MAESTRO
            )
        );
        $this->assertEquals(
            array(
                CreditCard::AMERICAN_EXPRESS,
                CreditCard::MAESTRO
            ),
            $validator->getType()
        );

        $validator->addType(
            CreditCard::MASTERCARD
        );
        $this->assertEquals(
            array(
                CreditCard::AMERICAN_EXPRESS,
                CreditCard::MAESTRO,
                CreditCard::MASTERCARD
            ),
            $validator->getType()
        );
    }

    public static function visaValues()
    {
        return array(
            array('4111111111111111', true),
            array('5404000000000001', false),
            array('374200000000004', false),
            array('4444555566667777', false),
            array('ABCDEF', false),
        );
    }

    /**
     * Test specific provider
     *
     * @dataProvider visaValues
     */
    public function testProvider($input, $expected)
    {
        $validator      = new CreditCard(CreditCard::VISA);
        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Test non string input
     *
     * @return void
     */
    public function testIsValidWithNonString()
    {
        $validator = new CreditCard(CreditCard::VISA);
        $this->assertFalse($validator->isValid(array('something')));
    }

    public static function serviceValues()
    {
        return array(
            array('4111111111111111', false),
            array('5404000000000001', false),
            array('374200000000004', false),
            array('4444555566667777', false),
            array('ABCDEF', false),
        );
    }

    /**
     * Test service class with invalid validation
     *
     * @dataProvider serviceValues
     */
    public function testServiceClass($input, $expected)
    {
        $validator = new CreditCard();
        $this->assertEquals(null, $validator->getService());
        $validator->setService(array('ZendTest\Validator\CreditCardTest', 'staticCallback'));
        $this->assertEquals($expected, $validator->isValid($input));
    }

    public static function optionsValues()
    {
        return array(
            array('4111111111111111', false),
            array('5404000000000001', false),
            array('374200000000004', false),
            array('4444555566667777', false),
            array('ABCDEF', false),
        );
    }

    /**
     * Test non string input
     *
     * @dataProvider optionsValues
     */
    public function testConstructionWithOptions($input, $expected)
    {
        $validator = new CreditCard(
            array(
                'type' => CreditCard::VISA,
                'service' => array('ZendTest\Validator\CreditCardTest', 'staticCallback')
            )
        );

        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Test a invalid service class
     *
     * @return void
     */
    public function testInvalidServiceClass()
    {
        $validator = new CreditCard();
        $this->assertEquals(null, $validator->getService());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Invalid callback given');
        $validator->setService(array('ZendTest\Validator\CreditCardTest', 'nocallback'));
    }

    /**
     * Test a config object
     *
     * @return void
     */
    public function testConfigObject()
    {
        $options = array('type' => 'Visa');
        $config = new Config\Config($options, false);

        $validator = new CreditCard($config);
        $this->assertEquals(array('Visa'), $validator->getType());
    }

    /**
     * Test optional parameters with config object
     *
     * @return void
     */
    public function testOptionalConstructorParameterByConfigObject()
    {
        $config = new Config\Config(array('type' => 'Visa', 'service' => array('ZendTest\Validator\CreditCardTest', 'staticCallback')));

        $validator = new CreditCard($config);
        $this->assertEquals(array('Visa'), $validator->getType());
        $this->assertEquals(array('ZendTest\Validator\CreditCardTest', 'staticCallback'), $validator->getService());
    }

    /**
     * Test optional constructor parameters
     *
     * @return void
     */
    public function testOptionalConstructorParameter()
    {
        $validator = new CreditCard('Visa', array('ZendTest\Validator\CreditCardTest', 'staticCallback'));
        $this->assertEquals(array('Visa'), $validator->getType());
        $this->assertEquals(array('ZendTest\Validator\CreditCardTest', 'staticCallback'), $validator->getService());
    }

    /**
     * @group ZF-9477
     */
    public function testMultiInstitute()
    {
        $validator      = new CreditCard(array('type' => CreditCard::MASTERCARD));
        $this->assertFalse($validator->isValid('4111111111111111'));
        $message = $validator->getMessages();
        $this->assertContains('not from an allowed institute', current($message));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new CreditCard();
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public static function staticCallback($value)
    {
        return false;
    }
}
