<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Flow_Mass
 */
require_once 'Zend/Measure/Flow/Mass.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_Flow_MassTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Mass initialisation
     * expected instance
     */
    public function testMassInit()
    {
        $value = new Zend_Measure_Flow_Mass('100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Flow_Mass,'Zend_Measure_Flow_Mass Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_MassUnknownType()
    {
        try {
            $value = new Zend_Measure_Flow_Mass('100','Flow_Mass::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_MassUnknownValue()
    {
        try {
            $value = new Zend_Measure_Flow_Mass('novalue',Zend_Measure_Flow_Mass::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testFlow_MassUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Flow_Mass('100',Zend_Measure_Flow_Mass::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testFlow_MassNoLocale()
    {
        $value = new Zend_Measure_Flow_Mass('100',Zend_Measure_Flow_Mass::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Flow_Mass value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testFlow_MassValuePositive()
    {
        $value = new Zend_Measure_Flow_Mass('100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Flow_Mass value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testFlow_MassValueNegative()
    {
        $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Flow_Mass value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testFlow_MassValueDecimal()
    {
        $value = new Zend_Measure_Flow_Mass('-100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Flow_Mass value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testFlow_MassValueDecimalSeperated()
    {
        $value = new Zend_Measure_Flow_Mass('-100.100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Flow_Mass Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testFlow_MassValueString()
    {
        $value = new Zend_Measure_Flow_Mass('string -100.100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Flow_Mass Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testFlow_MassEquality()
    {
        $value = new Zend_Measure_Flow_Mass('string -100.100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $newvalue = new Zend_Measure_Flow_Mass('otherstring -100.100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Flow_Mass Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testFlow_MassNoEquality()
    {
        $value = new Zend_Measure_Flow_Mass('string -100.100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $newvalue = new Zend_Measure_Flow_Mass('otherstring -100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Flow_Mass Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testFlow_MassSetPositive()
    {
        $value = new Zend_Measure_Flow_Mass('100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Flow_Mass value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testFlow_MassSetNegative()
    {
        $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Flow_Mass value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testFlow_MassSetDecimal()
    {
        $value = new Zend_Measure_Flow_Mass('-100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Flow_Mass value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testFlow_MassSetDecimalSeperated()
    {
        $value = new Zend_Measure_Flow_Mass('-100.100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Flow_Mass Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testFlow_MassSetString()
    {
        $value = new Zend_Measure_Flow_Mass('string -100.100,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Flow_Mass Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_MassSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Flow_Mass('100',Zend_Measure_Flow_Mass::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Flow_Mass::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_MassSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Flow_Mass('100',Zend_Measure_Flow_Mass::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Flow_Mass::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_MassSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Flow_Mass('100',Zend_Measure_Flow_Mass::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Flow_Mass::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_MassSetWithNoLocale()
    {
        $value = new Zend_Measure_Flow_Mass('100', Zend_Measure_Flow_Mass::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Flow_Mass::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Flow_Mass value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testFlow_MassSetType()
    {
        $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $value->setType(Zend_Measure_Flow_Mass::GRAM_PER_DAY);
        $this->assertEquals(Zend_Measure_Flow_Mass::GRAM_PER_DAY, $value->getType(), 'Zend_Measure_Flow_Mass type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_MassSetComputedType1()
    {
        $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $value->setType(Zend_Measure_Flow_Mass::GRAM_PER_DAY);
        $this->assertEquals(Zend_Measure_Flow_Mass::GRAM_PER_DAY, $value->getType(), 'Zend_Measure_Flow_Mass type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_MassSetComputedType2()
    {
        $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::GRAM_PER_DAY,'de');
        $value->setType(Zend_Measure_Flow_Mass::STANDARD);
        $this->assertEquals(Zend_Measure_Flow_Mass::STANDARD, $value->getType(), 'Zend_Measure_Flow_Mass type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testFlow_MassSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::STANDARD,'de');
            $value->setType('Flow_Mass::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testFlow_MassToString()
    {
        $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals('-100 kg/s', $value->toString(), 'Value -100 kg/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testFlow_Mass_ToString()
    {
        $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $this->assertEquals('-100 kg/s', $value->__toString(), 'Value -100 kg/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testFlow_MassConversionList()
    {
        $value = new Zend_Measure_Flow_Mass('-100',Zend_Measure_Flow_Mass::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
