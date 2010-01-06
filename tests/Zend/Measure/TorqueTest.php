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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Measure_Torque
 */
require_once 'Zend/Measure/Torque.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class Zend_Measure_TorqueTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Torque initialisation
     * expected instance
     */
    public function testTorqueInit()
    {
        $value = new Zend_Measure_Torque('100',Zend_Measure_Torque::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Torque,'Zend_Measure_Torque Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testTorqueUnknownType()
    {
        try {
            $value = new Zend_Measure_Torque('100','Torque::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Torque('novalue',Zend_Measure_Torque::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Torque('100',Zend_Measure_Torque::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testTorqueNoLocale()
    {
        $value = new Zend_Measure_Torque('100',Zend_Measure_Torque::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Torque value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testTorqueValuePositive()
    {
        $value = new Zend_Measure_Torque('100',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Torque value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testTorqueValueNegative()
    {
        $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Torque value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testTorqueValueDecimal()
    {
        $value = new Zend_Measure_Torque('-100,200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Torque value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testTorqueValueDecimalSeperated()
    {
        $value = new Zend_Measure_Torque('-100.100,200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Torque Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testTorqueValueString()
    {
        $value = new Zend_Measure_Torque('-100.100,200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Torque Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testTorqueEquality()
    {
        $value = new Zend_Measure_Torque('-100.100,200',Zend_Measure_Torque::STANDARD,'de');
        $newvalue = new Zend_Measure_Torque('-100.100,200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Torque Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testTorqueNoEquality()
    {
        $value = new Zend_Measure_Torque('-100.100,200',Zend_Measure_Torque::STANDARD,'de');
        $newvalue = new Zend_Measure_Torque('-100,200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Torque Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testTorqueSetPositive()
    {
        $value = new Zend_Measure_Torque('100',Zend_Measure_Torque::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Torque value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testTorqueSetNegative()
    {
        $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Torque value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testTorqueSetDecimal()
    {
        $value = new Zend_Measure_Torque('-100,200',Zend_Measure_Torque::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Torque value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testTorqueSetDecimalSeperated()
    {
        $value = new Zend_Measure_Torque('-100.100,200',Zend_Measure_Torque::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Torque Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testTorqueSetString()
    {
        $value = new Zend_Measure_Torque('-100.100,200',Zend_Measure_Torque::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Torque Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testTorqueSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Torque('100',Zend_Measure_Torque::STANDARD,'de');
            $value->setValue('-200.200,200','Torque::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Torque('100',Zend_Measure_Torque::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Torque::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Torque('100',Zend_Measure_Torque::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Torque::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testTorqueSetWithNoLocale()
    {
        $value = new Zend_Measure_Torque('100', Zend_Measure_Torque::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Torque::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Torque value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testTorqueSetType()
    {
        $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::STANDARD,'de');
        $value->setType(Zend_Measure_Torque::NEWTON_CENTIMETER);
        $this->assertEquals(Zend_Measure_Torque::NEWTON_CENTIMETER, $value->getType(), 'Zend_Measure_Torque type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testTorqueSetComputedType1()
    {
        $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::STANDARD,'de');
        $value->setType(Zend_Measure_Torque::POUND_INCH);
        $this->assertEquals(Zend_Measure_Torque::POUND_INCH, $value->getType(), 'Zend_Measure_Torque type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testTorqueSetComputedType2()
    {
        $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::POUND_INCH,'de');
        $value->setType(Zend_Measure_Torque::STANDARD);
        $this->assertEquals(Zend_Measure_Torque::STANDARD, $value->getType(), 'Zend_Measure_Torque type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testTorqueSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::STANDARD,'de');
            $value->setType('Torque::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testTorqueToString()
    {
        $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals('-100 Nm', $value->toString(), 'Value -100 Nm expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testTorque_ToString()
    {
        $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::STANDARD,'de');
        $this->assertEquals('-100 Nm', $value->__toString(), 'Value -100 Nm expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testTorqueConversionList()
    {
        $value = new Zend_Measure_Torque('-100',Zend_Measure_Torque::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
