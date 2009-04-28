<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Flow_Mole
 */
require_once 'Zend/Measure/Flow/Mole.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_Flow_MoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for mole initialisation
     * expected instance
     */
    public function testMoleInit()
    {
        $value = new Zend_Measure_Flow_Mole('100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Flow_Mole,'Zend_Measure_Flow_Mole Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_MoleUnknownType()
    {
        try {
            $value = new Zend_Measure_Flow_Mole('100','Flow_Mole::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_MoleUnknownValue()
    {
        try {
            $value = new Zend_Measure_Flow_Mole('novalue',Zend_Measure_Flow_Mole::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testFlow_MoleUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Flow_Mole('100',Zend_Measure_Flow_Mole::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testFlow_MoleNoLocale()
    {
        $value = new Zend_Measure_Flow_Mole('100',Zend_Measure_Flow_Mole::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Flow_Mole value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testFlow_MoleValuePositive()
    {
        $value = new Zend_Measure_Flow_Mole('100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Flow_Mole value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testFlow_MoleValueNegative()
    {
        $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Flow_Mole value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testFlow_MoleValueDecimal()
    {
        $value = new Zend_Measure_Flow_Mole('-100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Flow_Mole value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testFlow_MoleValueDecimalSeperated()
    {
        $value = new Zend_Measure_Flow_Mole('-100.100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Flow_Mole Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testFlow_MoleValueString()
    {
        $value = new Zend_Measure_Flow_Mole('string -100.100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Flow_Mole Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testFlow_MoleEquality()
    {
        $value = new Zend_Measure_Flow_Mole('string -100.100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $newvalue = new Zend_Measure_Flow_Mole('otherstring -100.100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Flow_Mole Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testFlow_MoleNoEquality()
    {
        $value = new Zend_Measure_Flow_Mole('string -100.100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $newvalue = new Zend_Measure_Flow_Mole('otherstring -100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Flow_Mole Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testFlow_MoleSetPositive()
    {
        $value = new Zend_Measure_Flow_Mole('100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Flow_Mole value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testFlow_MoleSetNegative()
    {
        $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Flow_Mole value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testFlow_MoleSetDecimal()
    {
        $value = new Zend_Measure_Flow_Mole('-100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Flow_Mole value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testFlow_MoleSetDecimalSeperated()
    {
        $value = new Zend_Measure_Flow_Mole('-100.100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Flow_Mole Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testFlow_MoleSetString()
    {
        $value = new Zend_Measure_Flow_Mole('string -100.100,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Flow_Mole Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_MoleSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Flow_Mole('100',Zend_Measure_Flow_Mole::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Flow_Mole::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_MoleSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Flow_Mole('100',Zend_Measure_Flow_Mole::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Flow_Mole::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_MoleSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Flow_Mole('100',Zend_Measure_Flow_Mole::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Flow_Mole::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_MoleSetWithNoLocale()
    {
        $value = new Zend_Measure_Flow_Mole('100', Zend_Measure_Flow_Mole::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Flow_Mole::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Flow_Mole value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testFlow_MoleSetType()
    {
        $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $value->setType(Zend_Measure_Flow_Mole::MILLIMOLE_PER_DAY);
        $this->assertEquals(Zend_Measure_Flow_Mole::MILLIMOLE_PER_DAY, $value->getType(), 'Zend_Measure_Flow_Mole type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_MoleSetComputedType1()
    {
        $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $value->setType(Zend_Measure_Flow_Mole::MILLIMOLE_PER_DAY);
        $this->assertEquals(Zend_Measure_Flow_Mole::MILLIMOLE_PER_DAY, $value->getType(), 'Zend_Measure_Flow_Mole type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_MoleSetComputedType2()
    {
        $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::MILLIMOLE_PER_DAY,'de');
        $value->setType(Zend_Measure_Flow_Mole::STANDARD);
        $this->assertEquals(Zend_Measure_Flow_Mole::STANDARD, $value->getType(), 'Zend_Measure_Flow_Mole type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testFlow_MoleSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::STANDARD,'de');
            $value->setType('Flow_Mole::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testFlow_MoleToString()
    {
        $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals('-100 mol/s', $value->toString(), 'Value -100 mol/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testFlow_Mole_ToString()
    {
        $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $this->assertEquals('-100 mol/s', $value->__toString(), 'Value -100 mol/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testFlow_MoleConversionList()
    {
        $value = new Zend_Measure_Flow_Mole('-100',Zend_Measure_Flow_Mole::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
