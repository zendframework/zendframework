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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Measure_Speed
 */
require_once 'Zend/Measure/Speed.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_SpeedTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Speed initialisation
     * expected instance
     */
    public function testSpeedInit()
    {
        $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Speed,'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testSpeedUnknownType()
    {
        try {
            $value = new Zend_Measure_Speed('100','Speed::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testSpeedUnknownValue()
    {
        try {
            $value = new Zend_Measure_Speed('novalue',Zend_Measure_Speed::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testSpeedUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testSpeedNoLocale()
    {
        $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Speed value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testSpeedValuePositive()
    {
        $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Speed value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testSpeedValueNegative()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Speed value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testSpeedValueDecimal()
    {
        $value = new Zend_Measure_Speed('-100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Speed value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testSpeedValueDecimalSeperated()
    {
        $value = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testSpeedValueString()
    {
        $value = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testSpeedEquality()
    {
        $value = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $newvalue = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Speed Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testSpeedNoEquality()
    {
        $value = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $newvalue = new Zend_Measure_Speed('-100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Speed Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testSpeedSetPositive()
    {
        $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Speed value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testSpeedSetNegative()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Speed value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testSpeedSetDecimal()
    {
        $value = new Zend_Measure_Speed('-100,200',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Speed value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testSpeedSetDecimalSeperated()
    {
        $value = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testSpeedSetString()
    {
        $value = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testSpeedSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
            $value->setValue('-200.200,200','Speed::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testSpeedSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Speed::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testSpeedSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Speed::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testSpeedSetWithNoLocale()
    {
        $value = new Zend_Measure_Speed('100', Zend_Measure_Speed::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Speed::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Speed value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testSpeedSetType()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $value->setType(Zend_Measure_Speed::METER_PER_HOUR);
        $this->assertEquals(Zend_Measure_Speed::METER_PER_HOUR, $value->getType(), 'Zend_Measure_Speed type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testSpeedSetComputedType1()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $value->setType(Zend_Measure_Speed::METER_PER_HOUR);
        $this->assertEquals(Zend_Measure_Speed::METER_PER_HOUR, $value->getType(), 'Zend_Measure_Speed type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testSpeedSetComputedType2()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::METER_PER_HOUR,'de');
        $value->setType(Zend_Measure_Speed::STANDARD);
        $this->assertEquals(Zend_Measure_Speed::STANDARD, $value->getType(), 'Zend_Measure_Speed type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testSpeedSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
            $value->setType('Speed::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testSpeedToString()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals('-100 m/s', $value->toString(), 'Value -100 m/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testSpeed_ToString()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals('-100 m/s', $value->__toString(), 'Value -100 m/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testSpeedConversionList()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
