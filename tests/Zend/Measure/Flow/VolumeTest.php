<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Flow_Volume
 */
require_once 'Zend/Measure/Flow/Volume.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_Flow_VolumeTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Volume initialisation
     * expected instance
     */
    public function testFlow_VolumeInit()
    {
        $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Flow_Volume,'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_VolumeUnknownType()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100','Flow_Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_VolumeUnknownValue()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('novalue',Zend_Measure_Flow_Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testFlow_VolumeUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testFlow_VolumeNoLocale()
    {
        $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Flow_Volume value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testFlow_VolumeValuePositive()
    {
        $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testFlow_VolumeValueNegative()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testFlow_VolumeValueDecimal()
    {
        $value = new Zend_Measure_Flow_Volume('-100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testFlow_VolumeValueDecimalSeperated()
    {
        $value = new Zend_Measure_Flow_Volume('-100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testFlow_VolumeValueString()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testFlow_VolumeEquality()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $newvalue = new Zend_Measure_Flow_Volume('otherstring -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Flow_Volume Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testFlow_VolumeNoEquality()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $newvalue = new Zend_Measure_Flow_Volume('otherstring -100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Flow_Volume Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testFlow_VolumeSetPositive()
    {
        $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testFlow_VolumeSetNegative()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testFlow_VolumeSetDecimal()
    {
        $value = new Zend_Measure_Flow_Volume('-100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testFlow_VolumeSetDecimalSeperated()
    {
        $value = new Zend_Measure_Flow_Volume('-100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testFlow_VolumeSetString()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_VolumeSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Flow_Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_VolumeSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Flow_Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_VolumeSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Flow_Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_VolumeSetWithNoLocale()
    {
        $value = new Zend_Measure_Flow_Volume('100', Zend_Measure_Flow_Volume::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Flow_Volume::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testFlow_VolumeSetType()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setType(Zend_Measure_Flow_Volume::CUSEC);
        $this->assertEquals(Zend_Measure_Flow_Volume::CUSEC, $value->getType(), 'Zend_Measure_Flow_Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_VolumeSetComputedType1()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setType(Zend_Measure_Flow_Volume::BARREL_PER_DAY);
        $this->assertEquals(Zend_Measure_Flow_Volume::BARREL_PER_DAY, $value->getType(), 'Zend_Measure_Flow_Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_VolumeSetComputedType2()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::BARREL_PER_DAY,'de');
        $value->setType(Zend_Measure_Flow_Volume::STANDARD);
        $this->assertEquals(Zend_Measure_Flow_Volume::STANDARD, $value->getType(), 'Zend_Measure_Flow_Volume type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testFlow_VolumeSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
            $value->setType('Flow_Volume::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testFlow_VolumeToString()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals('-100 m³/s', $value->toString(), 'Value -100 m³/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testFlow_Volume_ToString()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals('-100 m³/s', $value->__toString(), 'Value -100 m³/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testFlow_VolumeConversionList()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
