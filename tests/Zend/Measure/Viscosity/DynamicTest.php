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
class DynamicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Mass initialisation
     * expected instance
     */
    public function testMassInit()
    {
        $value = new Viscosity\Dynamic('100',Viscosity\Dynamic::STANDARD,'de');
        $this->assertTrue($value instanceof Viscosity\Dynamic,'Zend\Measure\Viscosity\Dynamic Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testViscosity_DynamicUnknownType()
    {
        try {
            $value = new Viscosity\Dynamic('100','Viscosity_Dynamic::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testViscosity_DynamicUnknownValue()
    {
        try {
            $value = new Viscosity\Dynamic('novalue',Viscosity\Dynamic::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testViscosity_DynamicUnknownLocale()
    {
        try {
            $value = new Viscosity\Dynamic('100',Viscosity\Dynamic::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testViscosity_DynamicNoLocale()
    {
        $value = new Viscosity\Dynamic('100',Viscosity\Dynamic::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Viscosity\Dynamic value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testViscosity_DynamicValuePositive()
    {
        $value = new Viscosity\Dynamic('100',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Viscosity\Dynamic value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testViscosity_DynamicValueNegative()
    {
        $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Viscosity\Dynamic value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testViscosity_DynamicValueDecimal()
    {
        $value = new Viscosity\Dynamic('-100,200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Viscosity\Dynamic value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testViscosity_DynamicValueDecimalSeperated()
    {
        $value = new Viscosity\Dynamic('-100.100,200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Viscosity\Dynamic Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testViscosity_DynamicValueString()
    {
        $value = new Viscosity\Dynamic('-100.100,200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Viscosity\Dynamic Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testViscosity_DynamicEquality()
    {
        $value = new Viscosity\Dynamic('-100.100,200',Viscosity\Dynamic::STANDARD,'de');
        $newvalue = new Viscosity\Dynamic('-100.100,200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Viscosity\Dynamic Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testViscosity_DynamicNoEquality()
    {
        $value = new Viscosity\Dynamic('-100.100,200',Viscosity\Dynamic::STANDARD,'de');
        $newvalue = new Viscosity\Dynamic('-100,200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Viscosity\Dynamic Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testViscosity_DynamicSetPositive()
    {
        $value = new Viscosity\Dynamic('100',Viscosity\Dynamic::STANDARD,'de');
        $value->setValue('200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Viscosity\Dynamic value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testViscosity_DynamicSetNegative()
    {
        $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::STANDARD,'de');
        $value->setValue('-200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Viscosity\Dynamic value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testViscosity_DynamicSetDecimal()
    {
        $value = new Viscosity\Dynamic('-100,200',Viscosity\Dynamic::STANDARD,'de');
        $value->setValue('-200,200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Viscosity\Dynamic value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testViscosity_DynamicSetDecimalSeperated()
    {
        $value = new Viscosity\Dynamic('-100.100,200',Viscosity\Dynamic::STANDARD,'de');
        $value->setValue('-200.200,200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Viscosity\Dynamic Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testViscosity_DynamicSetString()
    {
        $value = new Viscosity\Dynamic('-100.100,200',Viscosity\Dynamic::STANDARD,'de');
        $value->setValue('-200.200,200',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Viscosity\Dynamic Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testViscosity_DynamicSetUnknownType()
    {
        try {
            $value = new Viscosity\Dynamic('100',Viscosity\Dynamic::STANDARD,'de');
            $value->setValue('-200.200,200','Viscosity_Dynamic::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testViscosity_DynamicSetUnknownValue()
    {
        try {
            $value = new Viscosity\Dynamic('100',Viscosity\Dynamic::STANDARD,'de');
            $value->setValue('novalue',Viscosity\Dynamic::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testViscosity_DynamicSetUnknownLocale()
    {
        try {
            $value = new Viscosity\Dynamic('100',Viscosity\Dynamic::STANDARD,'de');
            $value->setValue('200',Viscosity\Dynamic::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testViscosity_DynamicSetWithNoLocale()
    {
        $value = new Viscosity\Dynamic('100', Viscosity\Dynamic::STANDARD, 'de');
        $value->setValue('200', Viscosity\Dynamic::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Viscosity\Dynamic value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testViscosity_DynamicSetType()
    {
        $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::STANDARD,'de');
        $value->setType(Viscosity\Dynamic::POISE);
        $this->assertEquals(Viscosity\Dynamic::POISE, $value->getType(), 'Zend\Measure\Viscosity\Dynamic type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testViscosity_DynamicSetComputedType1()
    {
        $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::STANDARD,'de');
        $value->setType(Viscosity\Dynamic::KILOGRAM_PER_METER_HOUR);
        $this->assertEquals(Viscosity\Dynamic::KILOGRAM_PER_METER_HOUR, $value->getType(), 'Zend\Measure\Viscosity\Dynamic type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testViscosity_DynamicSetComputedType2()
    {
        $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::KILOGRAM_PER_METER_HOUR,'de');
        $value->setType(Viscosity\Dynamic::STANDARD);
        $this->assertEquals(Viscosity\Dynamic::STANDARD, $value->getType(), 'Zend\Measure\Viscosity\Dynamic type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testViscosity_DynamicSetTypeFailed()
    {
        try {
            $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::STANDARD,'de');
            $value->setType('Viscosity_Dynamic::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testViscosity_DynamicToString()
    {
        $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals('-100 kg/ms', $value->toString(), 'Value -100 kg/ms expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testViscosity_Dynamic_ToString()
    {
        $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::STANDARD,'de');
        $this->assertEquals('-100 kg/ms', $value->__toString(), 'Value -100 kg/ms expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testViscosity_DynamicConversionList()
    {
        $value = new Viscosity\Dynamic('-100',Viscosity\Dynamic::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
