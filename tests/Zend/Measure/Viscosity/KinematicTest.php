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
namespace ZendTest\Measure\Viscosity;
use Zend\Measure\Viscosity;
use Zend\Measure;

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class KinematicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Mass initialisation
     * expected instance
     */
    public function testMassInit()
    {
        $value = new Viscosity\Kinematic('100',Viscosity\Kinematic::STANDARD,'de');
        $this->assertTrue($value instanceof Viscosity\Kinematic,'Zend\Measure\Viscosity\Kinematic Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testViscosity_KinematicUnknownType()
    {
        try {
            $value = new Viscosity\Kinematic('100','Viscosity_Kinematic::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testViscosity_KinematicUnknownValue()
    {
        try {
            $value = new Viscosity\Kinematic('novalue',Viscosity\Kinematic::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testViscosity_KinematicUnknownLocale()
    {
        try {
            $value = new Viscosity\Kinematic('100',Viscosity\Kinematic::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testViscosity_KinematicNoLocale()
    {
        $value = new Viscosity\Kinematic('100',Viscosity\Kinematic::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Viscosity\Kinematic value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testViscosity_KinematicValuePositive()
    {
        $value = new Viscosity\Kinematic('100',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Viscosity\Kinematic value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testViscosity_KinematicValueNegative()
    {
        $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Viscosity\Kinematic value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testViscosity_KinematicValueDecimal()
    {
        $value = new Viscosity\Kinematic('-100,200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Viscosity\Kinematic value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testViscosity_KinematicValueDecimalSeperated()
    {
        $value = new Viscosity\Kinematic('-100.100,200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Viscosity\Kinematic Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testViscosity_KinematicValueString()
    {
        $value = new Viscosity\Kinematic('-100.100,200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Viscosity\Kinematic Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testViscosity_KinematicEquality()
    {
        $value = new Viscosity\Kinematic('-100.100,200',Viscosity\Kinematic::STANDARD,'de');
        $newvalue = new Viscosity\Kinematic('-100.100,200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Viscosity\Kinematic Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testViscosity_KinematicNoEquality()
    {
        $value = new Viscosity\Kinematic('-100.100,200',Viscosity\Kinematic::STANDARD,'de');
        $newvalue = new Viscosity\Kinematic('-100,200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Viscosity\Kinematic Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testViscosity_KinematicSetPositive()
    {
        $value = new Viscosity\Kinematic('100',Viscosity\Kinematic::STANDARD,'de');
        $value->setValue('200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Viscosity\Kinematic value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testViscosity_KinematicSetNegative()
    {
        $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::STANDARD,'de');
        $value->setValue('-200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Viscosity\Kinematic value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testViscosity_KinematicSetDecimal()
    {
        $value = new Viscosity\Kinematic('-100,200',Viscosity\Kinematic::STANDARD,'de');
        $value->setValue('-200,200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Viscosity\Kinematic value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testViscosity_KinematicSetDecimalSeperated()
    {
        $value = new Viscosity\Kinematic('-100.100,200',Viscosity\Kinematic::STANDARD,'de');
        $value->setValue('-200.200,200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Viscosity\Kinematic Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testViscosity_KinematicSetString()
    {
        $value = new Viscosity\Kinematic('-100.100,200',Viscosity\Kinematic::STANDARD,'de');
        $value->setValue('-200.200,200',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Viscosity\Kinematic Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testViscosity_KinematicSetUnknownType()
    {
        try {
            $value = new Viscosity\Kinematic('100',Viscosity\Kinematic::STANDARD,'de');
            $value->setValue('-200.200,200','Viscosity_Kinematic::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testViscosity_KinematicSetUnknownValue()
    {
        try {
            $value = new Viscosity\Kinematic('100',Viscosity\Kinematic::STANDARD,'de');
            $value->setValue('novalue',Viscosity\Kinematic::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testViscosity_KinematicSetUnknownLocale()
    {
        try {
            $value = new Viscosity\Kinematic('100',Viscosity\Kinematic::STANDARD,'de');
            $value->setValue('200',Viscosity\Kinematic::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testViscosity_KinematicSetWithNoLocale()
    {
        $value = new Viscosity\Kinematic('100', Viscosity\Kinematic::STANDARD, 'de');
        $value->setValue('200', Viscosity\Kinematic::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Viscosity\Kinematic value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testViscosity_KinematicSetType()
    {
        $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::STANDARD,'de');
        $value->setType(Viscosity\Kinematic::LENTOR);
        $this->assertEquals(Viscosity\Kinematic::LENTOR, $value->getType(), 'Zend\Measure\Viscosity\Kinematic type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testViscosity_KinematicSetComputedType1()
    {
        $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::STANDARD,'de');
        $value->setType(Viscosity\Kinematic::LITER_PER_CENTIMETER_DAY);
        $this->assertEquals(Viscosity\Kinematic::LITER_PER_CENTIMETER_DAY, $value->getType(), 'Zend\Measure\Viscosity\Kinematic type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testViscosity_KinematicSetComputedType2()
    {
        $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::LITER_PER_CENTIMETER_DAY,'de');
        $value->setType(Viscosity\Kinematic::STANDARD);
        $this->assertEquals(Viscosity\Kinematic::STANDARD, $value->getType(), 'Zend\Measure\Viscosity\Kinematic type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testViscosity_KinematicSetTypeFailed()
    {
        try {
            $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::STANDARD,'de');
            $value->setType('Viscosity_Kinematic::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testViscosity_KinematicToString()
    {
        $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals('-100 m²/s', $value->toString(), 'Value -100 m²/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testViscosity_Kinematic_ToString()
    {
        $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::STANDARD,'de');
        $this->assertEquals('-100 m²/s', $value->__toString(), 'Value -100 m²/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testViscosity_KinematicConversionList()
    {
        $value = new Viscosity\Kinematic('-100',Viscosity\Kinematic::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
