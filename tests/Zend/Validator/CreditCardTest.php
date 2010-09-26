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
 * @namespace
 */
namespace ZendTest\Validator;
use Zend\Validator;
use Zend\Config;

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
class CreditCardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator      = new Validator\CreditCard();
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

    /**
     * Test specific provider
     *
     * @return void
     */
    public function testProvider()
    {
        $validator      = new Validator\CreditCard(Validator\CreditCard::VISA);
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
        $validator = new Validator\CreditCard(Validator\CreditCard::VISA);
        $this->assertFalse($validator->isValid(array('something')));
    }

    /**
     * Test service class with invalid validation
     *
     * @return void
     */
    public function testServiceClass()
    {
        $validator = new Validator\CreditCard();
        $this->assertEquals(null, $validator->getService());
        $validator->setService(array('\ZendTest\Validator\CreditCardTest', 'staticCallback'));
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
        $validator = new Validator\CreditCard(
            array(
                'type' => Validator\CreditCard::VISA,
                'service' => array('\ZendTest\Validator\CreditCardTest', 'staticCallback')
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
        $validator = new Validator\CreditCard();
        $this->assertEquals(null, $validator->getService());
        try {
            $validator->setService(array('\ZendTest\Validator\CreditCardTest', 'nocallback'));
            $this->fail('Exception expected');
        } catch(\Zend\Exception $e) {
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
        $config = new Config\Config(array('type' => 'Visa', 'service' => array('\ZendTest\Validator\CreditCardTest', 'staticCallback')));

        $validator = new Validator\CreditCard($config);
        $this->assertEquals(array('Visa'), $validator->getType());
        $this->assertEquals(array('\ZendTest\Validator\CreditCardTest', 'staticCallback'), $validator->getService());
    }

    /**
     * Test optional constructor parameters
     *
     * @return void
     */
    public function testOptionalConstructorParameter()
    {
        $validator = new Validator\CreditCard('Visa', array('\ZendTest\Validator\CreditCardTest', 'staticCallback'));
        $this->assertEquals(array('Visa'), $validator->getType());
        $this->assertEquals(array('\ZendTest\Validator\CreditCardTest', 'staticCallback'), $validator->getService());
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

    public static function staticCallback($value)
    {
        return false;
    }
}
