<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Cooking_Weight
 */
require_once 'Zend/Measure/Cooking/Weight.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_Cooking_WeightTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Mass initialisation
     * expected instance
     */
    public function testCooking_WeightInit()
    {
        $value = new Zend_Measure_Cooking_Weight('100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Cooking_Weight,'Zend_Measure_Cooking_Weight Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCooking_WeightUnknownType()
    {
        try {
            $value = new Zend_Measure_Cooking_Weight('100','Cooking_Weight::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCooking_WeightUnknownValue()
    {
        try {
            $value = new Zend_Measure_Cooking_Weight('novalue',Zend_Measure_Cooking_Weight::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testCooking_WeightUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Cooking_Weight('100',Zend_Measure_Cooking_Weight::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testCooking_WeightNoLocale()
    {
        $value = new Zend_Measure_Cooking_Weight('100',Zend_Measure_Cooking_Weight::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Cooking_Weight value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testCooking_WeightValuePositive()
    {
        $value = new Zend_Measure_Cooking_Weight('100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Cooking_Weight value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testCooking_WeightValueNegative()
    {
        $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Cooking_Weight value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testCooking_WeightValueDecimal()
    {
        $value = new Zend_Measure_Cooking_Weight('-100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Cooking_Weight value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testCooking_WeightValueDecimalSeperated()
    {
        $value = new Zend_Measure_Cooking_Weight('-100.100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Cooking_Weight Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testCooking_WeightValueString()
    {
        $value = new Zend_Measure_Cooking_Weight('string -100.100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Cooking_Weight Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testCooking_WeightEquality()
    {
        $value = new Zend_Measure_Cooking_Weight('string -100.100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $newvalue = new Zend_Measure_Cooking_Weight('otherstring -100.100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Cooking_Weight Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testCooking_WeightNoEquality()
    {
        $value = new Zend_Measure_Cooking_Weight('string -100.100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $newvalue = new Zend_Measure_Cooking_Weight('otherstring -100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Cooking_Weight Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testCooking_WeightSetPositive()
    {
        $value = new Zend_Measure_Cooking_Weight('100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Cooking_Weight value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testCooking_WeightSetNegative()
    {
        $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Cooking_Weight value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testCooking_WeightSetDecimal()
    {
        $value = new Zend_Measure_Cooking_Weight('-100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Cooking_Weight value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testCooking_WeightSetDecimalSeperated()
    {
        $value = new Zend_Measure_Cooking_Weight('-100.100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Cooking_Weight Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testCooking_WeightSetString()
    {
        $value = new Zend_Measure_Cooking_Weight('string -100.100,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Cooking_Weight Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCooking_WeightSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Cooking_Weight('100',Zend_Measure_Cooking_Weight::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Cooking_Weight::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCooking_WeightSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Cooking_Weight('100',Zend_Measure_Cooking_Weight::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Cooking_Weight::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCooking_WeightSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Cooking_Weight('100',Zend_Measure_Cooking_Weight::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Cooking_Weight::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCooking_WeightSetWithNoLocale()
    {
        $value = new Zend_Measure_Cooking_Weight('100', Zend_Measure_Cooking_Weight::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Cooking_Weight::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Cooking_Weight value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testCooking_WeightSetType()
    {
        $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $value->setType(Zend_Measure_Cooking_Weight::CUP);
        $this->assertEquals(Zend_Measure_Cooking_Weight::CUP, $value->getType(), 'Zend_Measure_Cooking_Weight type expected');    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testCooking_WeightSetComputedType1()
    {
        $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $value->setType(Zend_Measure_Cooking_Weight::CUP);
        $this->assertEquals(Zend_Measure_Cooking_Weight::CUP, $value->getType(), 'Zend_Measure_Cooking_Weight type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testCooking_WeightSetComputedType2()
    {
        $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::CUP,'de');
        $value->setType(Zend_Measure_Cooking_Weight::STANDARD);
        $this->assertEquals(Zend_Measure_Cooking_Weight::STANDARD, $value->getType(), 'Zend_Measure_Cooking_Weight type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testCooking_WeightSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::STANDARD,'de');
            $value->setType('Cooking_Weight::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testCooking_WeightToString()
    {
        $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals('-100 g', $value->toString(), 'Value -100 g expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testCooking_Weight_ToString()
    {
        $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $this->assertEquals('-100 g', $value->__toString(), 'Value -100 g expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testCooking_WeightConversionList()
    {
        $value = new Zend_Measure_Cooking_Weight('-100',Zend_Measure_Cooking_Weight::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
