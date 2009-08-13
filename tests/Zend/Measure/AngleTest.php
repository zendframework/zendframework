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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Measure_Angle
 */
require_once 'Zend/Measure/Angle.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class Zend_Measure_AngleTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Angle initialisation
     * expected instance
     */
    public function testAngleInit()
    {
        $value = new Zend_Measure_Angle('100',Zend_Measure_Angle::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Angle,'Zend_Measure_Angle Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAngleUnknownType()
    {
        try {
            $value = new Zend_Measure_Angle('100','Angle::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testAngleUnknownValue()
    {
        try {
            $value = new Zend_Measure_Angle('novalue',Zend_Measure_Angle::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testAngleUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Angle('100',Zend_Measure_Angle::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testAngleNoLocale()
    {
        $value = new Zend_Measure_Angle('100',Zend_Measure_Angle::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Angle value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testAngleValuePositive()
    {
        $value = new Zend_Measure_Angle('100',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Angle value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testAngleValueNegative()
    {
        $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Angle value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testAngleValueDecimal()
    {
        $value = new Zend_Measure_Angle('-100,200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Angle value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testAngleValueDecimalSeperated()
    {
        $value = new Zend_Measure_Angle('-100.100,200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Angle Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testAngleValueString()
    {
        $value = new Zend_Measure_Angle('-100.100,200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Angle Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testAngleEquality()
    {
        $value = new Zend_Measure_Angle('-100.100,200',Zend_Measure_Angle::STANDARD,'de');
        $newvalue = new Zend_Measure_Angle('-100.100,200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Angle Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testAngleNoEquality()
    {
        $value = new Zend_Measure_Angle('-100.100,200',Zend_Measure_Angle::STANDARD,'de');
        $newvalue = new Zend_Measure_Angle('-100,200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Angle Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testAngleSetPositive()
    {
        $value = new Zend_Measure_Angle('100',Zend_Measure_Angle::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Angle value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testAngleSetNegative()
    {
        $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Angle value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testAngleSetDecimal()
    {
        $value = new Zend_Measure_Angle('-100,200',Zend_Measure_Angle::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Angle value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testAngleSetDecimalSeperated()
    {
        $value = new Zend_Measure_Angle('-100.100,200',Zend_Measure_Angle::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Angle Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testAngleSetString()
    {
        $value = new Zend_Measure_Angle('-100.100,200',Zend_Measure_Angle::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Angle Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAngleSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Angle('100',Zend_Measure_Angle::STANDARD,'de');
            $value->setValue('-200.200,200','Angle::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testAngleSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Angle('100',Zend_Measure_Angle::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Angle::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAngleSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Angle('100',Zend_Measure_Angle::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Angle::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAngleSetWithNoLocale()
    {
        $value = new Zend_Measure_Angle('100', Zend_Measure_Angle::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Angle::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Angle value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testAngleSetType()
    {
        $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::STANDARD,'de');
        $value->setType(Zend_Measure_Angle::GRAD);
        $this->assertEquals(Zend_Measure_Angle::GRAD, $value->getType(), 'Zend_Measure_Angle type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testAngleSetComputedType1()
    {
        $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::RADIAN,'de');
        $value->setType(Zend_Measure_Angle::MINUTE);
        $this->assertEquals(Zend_Measure_Angle::MINUTE, $value->getType(), 'Zend_Measure_Angle type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testAngleSetComputedType2()
    {
        $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::MINUTE,'de');
        $value->setType(Zend_Measure_Angle::RADIAN);
        $this->assertEquals(Zend_Measure_Angle::RADIAN, $value->getType(), 'Zend_Measure_Angle type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testAngleSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::STANDARD,'de');
            $value->setType('Angle::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testAngleToString()
    {
        $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals('-100 rad', $value->toString(), 'Value -100 rad expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testAngle_ToString()
    {
        $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::STANDARD,'de');
        $this->assertEquals('-100 rad', $value->__toString(), 'Value -100 rad expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testAngleConversionList()
    {
        $value = new Zend_Measure_Angle('-100',Zend_Measure_Angle::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
