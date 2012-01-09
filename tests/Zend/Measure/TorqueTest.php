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
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Measure;
use Zend\Measure;

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class TorqueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Torque initialisation
     * expected instance
     */
    public function testTorqueInit()
    {
        $value = new Measure\Torque('100',Measure\Torque::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Torque,'Zend\Measure\Torque Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testTorqueUnknownType()
    {
        try {
            $value = new Measure\Torque('100','Torque::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testTorqueUnknownValue()
    {
        try {
            $value = new Measure\Torque('novalue',Measure\Torque::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testTorqueUnknownLocale()
    {
        try {
            $value = new Measure\Torque('100',Measure\Torque::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testTorqueNoLocale()
    {
        $value = new Measure\Torque('100',Measure\Torque::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Torque value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testTorqueValuePositive()
    {
        $value = new Measure\Torque('100',Measure\Torque::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Torque value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testTorqueValueNegative()
    {
        $value = new Measure\Torque('-100',Measure\Torque::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Torque value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testTorqueValueDecimal()
    {
        $value = new Measure\Torque('-100,200',Measure\Torque::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Torque value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testTorqueValueDecimalSeperated()
    {
        $value = new Measure\Torque('-100.100,200',Measure\Torque::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Torque Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testTorqueValueString()
    {
        $value = new Measure\Torque('-100.100,200',Measure\Torque::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Torque Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testTorqueEquality()
    {
        $value = new Measure\Torque('-100.100,200',Measure\Torque::STANDARD,'de');
        $newvalue = new Measure\Torque('-100.100,200',Measure\Torque::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Torque Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testTorqueNoEquality()
    {
        $value = new Measure\Torque('-100.100,200',Measure\Torque::STANDARD,'de');
        $newvalue = new Measure\Torque('-100,200',Measure\Torque::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Torque Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testTorqueSetPositive()
    {
        $value = new Measure\Torque('100',Measure\Torque::STANDARD,'de');
        $value->setValue('200',Measure\Torque::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Torque value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testTorqueSetNegative()
    {
        $value = new Measure\Torque('-100',Measure\Torque::STANDARD,'de');
        $value->setValue('-200',Measure\Torque::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Torque value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testTorqueSetDecimal()
    {
        $value = new Measure\Torque('-100,200',Measure\Torque::STANDARD,'de');
        $value->setValue('-200,200',Measure\Torque::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Torque value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testTorqueSetDecimalSeperated()
    {
        $value = new Measure\Torque('-100.100,200',Measure\Torque::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Torque::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Torque Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testTorqueSetString()
    {
        $value = new Measure\Torque('-100.100,200',Measure\Torque::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Torque::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Torque Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testTorqueSetUnknownType()
    {
        try {
            $value = new Measure\Torque('100',Measure\Torque::STANDARD,'de');
            $value->setValue('-200.200,200','Torque::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testTorqueSetUnknownValue()
    {
        try {
            $value = new Measure\Torque('100',Measure\Torque::STANDARD,'de');
            $value->setValue('novalue',Measure\Torque::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testTorqueSetUnknownLocale()
    {
        try {
            $value = new Measure\Torque('100',Measure\Torque::STANDARD,'de');
            $value->setValue('200',Measure\Torque::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testTorqueSetWithNoLocale()
    {
        $value = new Measure\Torque('100', Measure\Torque::STANDARD, 'de');
        $value->setValue('200', Measure\Torque::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Torque value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testTorqueSetType()
    {
        $value = new Measure\Torque('-100',Measure\Torque::STANDARD,'de');
        $value->setType(Measure\Torque::NEWTON_CENTIMETER);
        $this->assertEquals(Measure\Torque::NEWTON_CENTIMETER, $value->getType(), 'Zend\Measure\Torque type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testTorqueSetComputedType1()
    {
        $value = new Measure\Torque('-100',Measure\Torque::STANDARD,'de');
        $value->setType(Measure\Torque::POUND_INCH);
        $this->assertEquals(Measure\Torque::POUND_INCH, $value->getType(), 'Zend\Measure\Torque type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testTorqueSetComputedType2()
    {
        $value = new Measure\Torque('-100',Measure\Torque::POUND_INCH,'de');
        $value->setType(Measure\Torque::STANDARD);
        $this->assertEquals(Measure\Torque::STANDARD, $value->getType(), 'Zend\Measure\Torque type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testTorqueSetTypeFailed()
    {
        try {
            $value = new Measure\Torque('-100',Measure\Torque::STANDARD,'de');
            $value->setType('Torque::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testTorqueToString()
    {
        $value = new Measure\Torque('-100',Measure\Torque::STANDARD,'de');
        $this->assertEquals('-100 Nm', $value->toString(), 'Value -100 Nm expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testTorque_ToString()
    {
        $value = new Measure\Torque('-100',Measure\Torque::STANDARD,'de');
        $this->assertEquals('-100 Nm', $value->__toString(), 'Value -100 Nm expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testTorqueConversionList()
    {
        $value = new Measure\Torque('-100',Measure\Torque::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
