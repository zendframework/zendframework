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
 * @version    $Id:$
 */

/**
 * Test helper
 */

/**
 * @see Zend_Validate_CreditCard
 */

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_CreditCardTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator      = new Zend_Validate_CreditCard();
        $valuesExpected = array(
            '4111111111111111' => true,
            '5404000000000001' => true,
            '374200000000004'  => true,
            '4444555566667777' => false,
            'ABCDEF'           => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input), 'Test failed at ' . $input);
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new Zend_Validate_CreditCard();
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that get and setType works as expected
     *
     * @return void
     */
    public function testGetSetType()
    {
        $validator = new Zend_Validate_CreditCard();
        $this->assertEquals(11, count($validator->getType()));

        $validator->setType(Zend_Validate_CreditCard::MAESTRO);
        $this->assertEquals(array(Zend_Validate_CreditCard::MAESTRO), $validator->getType());

        $validator->setType(
            array(
                Zend_Validate_CreditCard::AMERICAN_EXPRESS,
                Zend_Validate_CreditCard::MAESTRO
            )
        );
        $this->assertEquals(
            array(
                Zend_Validate_CreditCard::AMERICAN_EXPRESS,
                Zend_Validate_CreditCard::MAESTRO
            ),
            $validator->getType()
        );

        $validator->addType(
            Zend_Validate_CreditCard::MASTERCARD
        );
        $this->assertEquals(
            array(
                Zend_Validate_CreditCard::AMERICAN_EXPRESS,
                Zend_Validate_CreditCard::MAESTRO,
                Zend_Validate_CreditCard::MASTERCARD
            ),
            $validator->getType()
        );
    }

    /**
     * Test specific provider
     *
     * @return void
     */
    public function testProvider()
    {
        $validator      = new Zend_Validate_CreditCard(Zend_Validate_CreditCard::VISA);
        $valuesExpected = array(
            '4111111111111111' => true,
            '5404000000000001' => false,
            '374200000000004'  => false,
            '4444555566667777' => false,
            'ABCDEF'           => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input));
        }
    }

    /**
     * Test non string input
     *
     * @return void
     */
    public function testIsValidWithNonString()
    {
        $validator = new Zend_Validate_CreditCard(Zend_Validate_CreditCard::VISA);
        $this->assertFalse($validator->isValid(array('something')));
    }

    /**
     * Test service class with invalid validation
     *
     * @return void
     */
    public function testServiceClass()
    {
        $validator = new Zend_Validate_CreditCard();
        $this->assertEquals(null, $validator->getService());
        $validator->setService(array('Zend_Validate_CreditCardTest', 'staticCallback'));
        $valuesExpected = array(
            '4111111111111111' => false,
            '5404000000000001' => false,
            '374200000000004'  => false,
            '4444555566667777' => false,
            'ABCDEF'           => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input));
        }
    }

    /**
     * Test non string input
     *
     * @return void
     */
    public function testConstructionWithOptions()
    {
        $validator = new Zend_Validate_CreditCard(
            array(
                'type' => Zend_Validate_CreditCard::VISA,
                'service' => array('Zend_Validate_CreditCardTest', 'staticCallback')
            )
        );

        $valuesExpected = array(
            '4111111111111111' => false,
            '5404000000000001' => false,
            '374200000000004'  => false,
            '4444555566667777' => false,
            'ABCDEF'           => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input));
        }
    }

    /**
     * Test a invalid service class
     *
     * @return void
     */
    public function testInvalidServiceClass()
    {
        $validator = new Zend_Validate_CreditCard();
        $this->assertEquals(null, $validator->getService());
        try {
            $validator->setService(array('Zend_Validate_CreditCardTest', 'nocallback'));
            $this->fail('Exception expected');
        } catch(Zend_Exception $e) {
            $this->assertContains('Invalid callback given', $e->getMessage());
        }
    }

    /**
     * Test a config object
     *
     * @return void
     */
    public function testConfigObject()
    {
        $options = array('type' => 'Visa');
        $config = new Zend_Config($options, false);

        $validator = new Zend_Validate_CreditCard($config);
        $this->assertEquals(array('Visa'), $validator->getType());
    }

    /**
     * Test optional parameters with config object
     *
     * @return void
     */
    public function testOptionalConstructorParameterByConfigObject()
    {
        $config = new Zend_Config(array('type' => 'Visa', 'service' => array('Zend_Validate_CreditCardTest', 'staticCallback')));

        $validator = new Zend_Validate_CreditCard($config);
        $this->assertEquals(array('Visa'), $validator->getType());
        $this->assertEquals(array('Zend_Validate_CreditCardTest', 'staticCallback'), $validator->getService());
    }

    /**
     * Test optional constructor parameters
     *
     * @return void
     */
    public function testOptionalConstructorParameter()
    {
        $validator = new Zend_Validate_CreditCard('Visa', array('Zend_Validate_CreditCardTest', 'staticCallback'));
        $this->assertEquals(array('Visa'), $validator->getType());
        $this->assertEquals(array('Zend_Validate_CreditCardTest', 'staticCallback'), $validator->getService());
    }

    /**
     * @group ZF-9477
     */
    public function testMultiInstitute() {
        $validator      = new Zend_Validate_CreditCard(array('type' => Zend_Validate_CreditCard::MASTERCARD));
        $this->assertFalse($validator->isValid('4111111111111111'));
        $message = $validator->getMessages();
        $this->assertContains('not from an allowed institute', current($message));
    }

    public static function staticCallback($value)
    {
        return false;
    }
}
