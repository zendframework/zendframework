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
class AngleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Angle initialisation
     * expected instance
     */
    public function testAngleInit()
    {
        $value = new Measure\Angle('100',Measure\Angle::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Angle,'Zend\Measure\Angle Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAngleUnknownType()
    {
        try {
            $value = new Measure\Angle('100','Angle::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Angle('novalue',Measure\Angle::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Angle('100',Measure\Angle::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testAngleNoLocale()
    {
        $value = new Measure\Angle('100',Measure\Angle::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Angle value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testAngleValuePositive()
    {
        $value = new Measure\Angle('100',Measure\Angle::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Angle value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testAngleValueNegative()
    {
        $value = new Measure\Angle('-100',Measure\Angle::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Angle value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testAngleValueDecimal()
    {
        $value = new Measure\Angle('-100,200',Measure\Angle::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Angle value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testAngleValueDecimalSeperated()
    {
        $value = new Measure\Angle('-100.100,200',Measure\Angle::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Angle Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testAngleValueString()
    {
        $value = new Measure\Angle('-100.100,200',Measure\Angle::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Angle Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testAngleEquality()
    {
        $value = new Measure\Angle('-100.100,200',Measure\Angle::STANDARD,'de');
        $newvalue = new Measure\Angle('-100.100,200',Measure\Angle::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Angle Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testAngleNoEquality()
    {
        $value = new Measure\Angle('-100.100,200',Measure\Angle::STANDARD,'de');
        $newvalue = new Measure\Angle('-100,200',Measure\Angle::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Angle Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testAngleSetPositive()
    {
        $value = new Measure\Angle('100',Measure\Angle::STANDARD,'de');
        $value->setValue('200',Measure\Angle::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Angle value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testAngleSetNegative()
    {
        $value = new Measure\Angle('-100',Measure\Angle::STANDARD,'de');
        $value->setValue('-200',Measure\Angle::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Angle value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testAngleSetDecimal()
    {
        $value = new Measure\Angle('-100,200',Measure\Angle::STANDARD,'de');
        $value->setValue('-200,200',Measure\Angle::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Angle value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testAngleSetDecimalSeperated()
    {
        $value = new Measure\Angle('-100.100,200',Measure\Angle::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Angle::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Angle Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testAngleSetString()
    {
        $value = new Measure\Angle('-100.100,200',Measure\Angle::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Angle::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Angle Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAngleSetUnknownType()
    {
        try {
            $value = new Measure\Angle('100',Measure\Angle::STANDARD,'de');
            $value->setValue('-200.200,200','Angle::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Angle('100',Measure\Angle::STANDARD,'de');
            $value->setValue('novalue',Measure\Angle::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Angle('100',Measure\Angle::STANDARD,'de');
            $value->setValue('200',Measure\Angle::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAngleSetWithNoLocale()
    {
        $value = new Measure\Angle('100', Measure\Angle::STANDARD, 'de');
        $value->setValue('200', Measure\Angle::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Angle value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testAngleSetType()
    {
        $value = new Measure\Angle('-100',Measure\Angle::STANDARD,'de');
        $value->setType(Measure\Angle::GRAD);
        $this->assertEquals(Measure\Angle::GRAD, $value->getType(), 'Zend\Measure\Angle type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testAngleSetComputedType1()
    {
        $value = new Measure\Angle('-100',Measure\Angle::RADIAN,'de');
        $value->setType(Measure\Angle::MINUTE);
        $this->assertEquals(Measure\Angle::MINUTE, $value->getType(), 'Zend\Measure\Angle type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testAngleSetComputedType2()
    {
        $value = new Measure\Angle('-100',Measure\Angle::MINUTE,'de');
        $value->setType(Measure\Angle::RADIAN);
        $this->assertEquals(Measure\Angle::RADIAN, $value->getType(), 'Zend\Measure\Angle type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testAngleSetTypeFailed()
    {
        try {
            $value = new Measure\Angle('-100',Measure\Angle::STANDARD,'de');
            $value->setType('Angle::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testAngleToString()
    {
        $value = new Measure\Angle('-100',Measure\Angle::STANDARD,'de');
        $this->assertEquals('-100 rad', $value->toString(), 'Value -100 rad expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testAngle_ToString()
    {
        $value = new Measure\Angle('-100',Measure\Angle::STANDARD,'de');
        $this->assertEquals('-100 rad', $value->__toString(), 'Value -100 rad expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testAngleConversionList()
    {
        $value = new Measure\Angle('-100',Measure\Angle::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
