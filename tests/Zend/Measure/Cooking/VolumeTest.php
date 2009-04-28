<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Cooking_Volume
 */
require_once 'Zend/Measure/Cooking/Volume.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_Cooking_VolumeTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Mass initialisation
     * expected instance
     */
    public function testMassInit()
    {
        $value = new Zend_Measure_Cooking_Volume('100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Cooking_Volume,'Zend_Measure_Cooking_Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCooking_VolumeUnknownType()
    {
        try {
            $value = new Zend_Measure_Cooking_Volume('100','Cooking_Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Cooking_Volume('novalue',Zend_Measure_Cooking_Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Cooking_Volume('100',Zend_Measure_Cooking_Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testCooking_VolumeNoLocale()
    {
        $value = new Zend_Measure_Cooking_Volume('100',Zend_Measure_Cooking_Volume::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Cooking_Volume value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testCooking_VolumeValuePositive()
    {
        $value = new Zend_Measure_Cooking_Volume('100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Cooking_Volume value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testCooking_VolumeValueNegative()
    {
        $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Cooking_Volume value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testCooking_VolumeValueDecimal()
    {
        $value = new Zend_Measure_Cooking_Volume('-100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Cooking_Volume value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testCooking_VolumeValueDecimalSeperated()
    {
        $value = new Zend_Measure_Cooking_Volume('-100.100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Cooking_Volume Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testCooking_VolumeValueString()
    {
        $value = new Zend_Measure_Cooking_Volume('string -100.100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Cooking_Volume Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testCooking_VolumeEquality()
    {
        $value = new Zend_Measure_Cooking_Volume('string -100.100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $newvalue = new Zend_Measure_Cooking_Volume('otherstring -100.100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Cooking_Volume Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testCooking_VolumeNoEquality()
    {
        $value = new Zend_Measure_Cooking_Volume('string -100.100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $newvalue = new Zend_Measure_Cooking_Volume('otherstring -100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Cooking_Volume Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testCooking_VolumeSetPositive()
    {
        $value = new Zend_Measure_Cooking_Volume('100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Cooking_Volume value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testCooking_VolumeSetNegative()
    {
        $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Cooking_Volume value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testCooking_VolumeSetDecimal()
    {
        $value = new Zend_Measure_Cooking_Volume('-100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Cooking_Volume value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testCooking_VolumeSetDecimalSeperated()
    {
        $value = new Zend_Measure_Cooking_Volume('-100.100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Cooking_Volume Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testCooking_VolumeSetString()
    {
        $value = new Zend_Measure_Cooking_Volume('string -100.100,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Cooking_Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCooking_VolumeSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Cooking_Volume('100',Zend_Measure_Cooking_Volume::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Cooking_Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Cooking_Volume('100',Zend_Measure_Cooking_Volume::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Cooking_Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Exception $e) {
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
            $value = new Zend_Measure_Cooking_Volume('100',Zend_Measure_Cooking_Volume::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Cooking_Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCooking_VolumeSetWithNoLocale()
    {
        $value = new Zend_Measure_Cooking_Volume('100', Zend_Measure_Cooking_Volume::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Cooking_Volume::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Cooking_Volume value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testCooking_VolumeSetType()
    {
        $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $value->setType(Zend_Measure_Cooking_Volume::DRAM);
        $this->assertEquals(Zend_Measure_Cooking_Volume::DRAM, $value->getType(), 'Zend_Measure_Cooking_Volume type expected');    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testCooking_VolumeSetComputedType1()
    {
        $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $value->setType(Zend_Measure_Cooking_Volume::DRAM);
        $this->assertEquals(Zend_Measure_Cooking_Volume::DRAM, $value->getType(), 'Zend_Measure_Cooking_Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testCooking_VolumeSetComputedType2()
    {
        $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::DRAM,'de');
        $value->setType(Zend_Measure_Cooking_Volume::STANDARD);
        $this->assertEquals(Zend_Measure_Cooking_Volume::STANDARD, $value->getType(), 'Zend_Measure_Cooking_Volume type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testCooking_VolumeSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::STANDARD,'de');
            $value->setType('Cooking_Volume::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testCooking_VolumeToString()
    {
        $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals('-100 m³', $value->toString(), 'Value -100 m³ expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testCooking_Volume_ToString()
    {
        $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $this->assertEquals('-100 m³', $value->__toString(), 'Value -100 m³ expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testCooking_VolumeConversionList()
    {
        $value = new Zend_Measure_Cooking_Volume('-100',Zend_Measure_Cooking_Volume::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
