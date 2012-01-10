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

/**
 * @namespace
 */
namespace ZendTest\Validator;
use Zend\Validator,
    Zend\Config,
    ReflectionClass;

/**
 * Test helper
 */

/**
 * @see Zend_Validator_CreditCard
 */

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $validator      = new Validator\CreditCard();
        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new Validator\CreditCard();
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that get and setType works as expected
     *
     * @return void
     */
    public function testGetSetType()
    {
        $validator = new Validator\CreditCard();
        $this->assertEquals(11, count($validator->getType()));

        $validator->setType(Validator\CreditCard::MAESTRO);
        $this->assertEquals(array(Validator\CreditCard::MAESTRO), $validator->getType());

        $validator->setType(
            array(
                Validator\CreditCard::AMERICAN_EXPRESS,
                Validator\CreditCard::MAESTRO
            )
        );
        $this->assertEquals(
            array(
                Validator\CreditCard::AMERICAN_EXPRESS,
                Validator\CreditCard::MAESTRO
            ),
            $validator->getType()
        );

        $validator->addType(
            Validator\CreditCard::MASTERCARD
        );
        $this->assertEquals(
            array(
                Validator\CreditCard::AMERICAN_EXPRESS,
                Validator\CreditCard::MAESTRO,
                Validator\CreditCard::MASTERCARD
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
        $validator      = new Validator\CreditCard(Validator\CreditCard::VISA);
        $this->assertEquals($expected, $validator->isValid($input));
    }

    /**
     * Test non string input
     *
     * @return void
     */
    public function testIsValidWithNonString()
    {
        $validator = new Validator\CreditCard(Validator\CreditCard::VISA);
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
        $validator = new Validator\CreditCard();
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
        $validator = new Validator\CreditCard(
            array(
                'type' => Validator\CreditCard::VISA,
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
        $validator = new Validator\CreditCard();
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

        $validator = new Validator\CreditCard($config);
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

        $validator = new Validator\CreditCard($config);
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
        $validator = new Validator\CreditCard('Visa', array('ZendTest\Validator\CreditCardTest', 'staticCallback'));
        $this->assertEquals(array('Visa'), $validator->getType());
        $this->assertEquals(array('ZendTest\Validator\CreditCardTest', 'staticCallback'), $validator->getService());
    }

    /**
     * @group ZF-9477
     */
    public function testMultiInstitute() {
        $validator      = new Validator\CreditCard(array('type' => Validator\CreditCard::MASTERCARD));
        $this->assertFalse($validator->isValid('4111111111111111'));
        $message = $validator->getMessages();
        $this->assertContains('not from an allowed institute', current($message));
    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\CreditCard();
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageTemplates')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }
    
    public function testEqualsMessageVariables()
    {
        $validator = new Validator\CreditCard();
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageVariables')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }

    public static function staticCallback($value)
    {
        return false;
    }
}
