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
namespace ZendTest\Measure\Cooking;
use Zend\Measure\Cooking;
use Zend\Measure;

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class VolumeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Mass initialisation
     * expected instance
     */
    public function testMassInit()
    {
        $value = new Cooking\Volume('100',Cooking\Volume::STANDARD,'de');
        $this->assertTrue($value instanceof Cooking\Volume,'Zend\Measure\Cooking\Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCooking_VolumeUnknownType()
    {
        try {
            $value = new Cooking\Volume('100','Cooking_Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCooking_VolumeUnknownValue()
    {
        try {
            $value = new Cooking\Volume('novalue',Cooking\Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testCooking_VolumeUnknownLocale()
    {
        try {
            $value = new Cooking\Volume('100',Cooking\Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testCooking_VolumeNoLocale()
    {
        $value = new Cooking\Volume('100',Cooking\Volume::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Cooking\Volume value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testCooking_VolumeValuePositive()
    {
        $value = new Cooking\Volume('100',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Cooking\Volume value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testCooking_VolumeValueNegative()
    {
        $value = new Cooking\Volume('-100',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Cooking\Volume value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testCooking_VolumeValueDecimal()
    {
        $value = new Cooking\Volume('-100,200',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Cooking\Volume value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testCooking_VolumeValueDecimalSeperated()
    {
        $value = new Cooking\Volume('-100.100,200',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Cooking\Volume Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testCooking_VolumeValueString()
    {
        $value = new Cooking\Volume('-100.100,200',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Cooking\Volume Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testCooking_VolumeEquality()
    {
        $value = new Cooking\Volume('-100.100,200',Cooking\Volume::STANDARD,'de');
        $newvalue = new Cooking\Volume('-100.100,200',Cooking\Volume::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Cooking\Volume Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testCooking_VolumeNoEquality()
    {
        $value = new Cooking\Volume('-100.100,200',Cooking\Volume::STANDARD,'de');
        $newvalue = new Cooking\Volume('-100,200',Cooking\Volume::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Cooking\Volume Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testCooking_VolumeSetPositive()
    {
        $value = new Cooking\Volume('100',Cooking\Volume::STANDARD,'de');
        $value->setValue('200',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Cooking\Volume value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testCooking_VolumeSetNegative()
    {
        $value = new Cooking\Volume('-100',Cooking\Volume::STANDARD,'de');
        $value->setValue('-200',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Cooking\Volume value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testCooking_VolumeSetDecimal()
    {
        $value = new Cooking\Volume('-100,200',Cooking\Volume::STANDARD,'de');
        $value->setValue('-200,200',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Cooking\Volume value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testCooking_VolumeSetDecimalSeperated()
    {
        $value = new Cooking\Volume('-100.100,200',Cooking\Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Cooking\Volume Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testCooking_VolumeSetString()
    {
        $value = new Cooking\Volume('-100.100,200',Cooking\Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Cooking\Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Cooking\Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCooking_VolumeSetUnknownType()
    {
        try {
            $value = new Cooking\Volume('100',Cooking\Volume::STANDARD,'de');
            $value->setValue('-200.200,200','Cooking_Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCooking_VolumeSetUnknownValue()
    {
        try {
            $value = new Cooking\Volume('100',Cooking\Volume::STANDARD,'de');
            $value->setValue('novalue',Cooking\Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCooking_VolumeSetUnknownLocale()
    {
        try {
            $value = new Cooking\Volume('100',Cooking\Volume::STANDARD,'de');
            $value->setValue('200',Cooking\Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCooking_VolumeSetWithNoLocale()
    {
        $value = new Cooking\Volume('100', Cooking\Volume::STANDARD, 'de');
        $value->setValue('200', Cooking\Volume::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Cooking\Volume value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testCooking_VolumeSetType()
    {
        $value = new Cooking\Volume('-100',Cooking\Volume::STANDARD,'de');
        $value->setType(Cooking\Volume::DRAM);
        $this->assertEquals(Cooking\Volume::DRAM, $value->getType(), 'Zend\Measure\Cooking\Volume type expected');    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testCooking_VolumeSetComputedType1()
    {
        $value = new Cooking\Volume('-100',Cooking\Volume::STANDARD,'de');
        $value->setType(Cooking\Volume::DRAM);
        $this->assertEquals(Cooking\Volume::DRAM, $value->getType(), 'Zend\Measure\Cooking\Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testCooking_VolumeSetComputedType2()
    {
        $value = new Cooking\Volume('-100',Cooking\Volume::DRAM,'de');
        $value->setType(Cooking\Volume::STANDARD);
        $this->assertEquals(Cooking\Volume::STANDARD, $value->getType(), 'Zend\Measure\Cooking\Volume type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testCooking_VolumeSetTypeFailed()
    {
        try {
            $value = new Cooking\Volume('-100',Cooking\Volume::STANDARD,'de');
            $value->setType('Cooking_Volume::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testCooking_VolumeToString()
    {
        $value = new Cooking\Volume('-100',Cooking\Volume::STANDARD,'de');
        $this->assertEquals('-100 m³', $value->toString(), 'Value -100 m³ expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testCooking_Volume_ToString()
    {
        $value = new Cooking\Volume('-100',Cooking\Volume::STANDARD,'de');
        $this->assertEquals('-100 m³', $value->__toString(), 'Value -100 m³ expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testCooking_VolumeConversionList()
    {
        $value = new Cooking\Volume('-100',Cooking\Volume::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
