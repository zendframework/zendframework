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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Measure_Pressure
 */
require_once 'Zend/Measure/Pressure.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class Zend_Measure_PressureTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Pressure initialisation
     * expected instance
     */
    public function testPressureInit()
    {
        $value = new Zend_Measure_Pressure('100',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Pressure,'Zend_Measure_Pressure Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testPressureUnknownType()
    {
        try {
            $value = new Zend_Measure_Pressure('100','Pressure::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testPressureUnknownValue()
    {
        try {
            $value = new Zend_Measure_Pressure('novalue',Zend_Measure_Pressure::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testPressureUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Pressure('100',Zend_Measure_Pressure::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testPressureNoLocale()
    {
        $value = new Zend_Measure_Pressure('100',Zend_Measure_Pressure::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Pressure value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testPressureValuePositive()
    {
        $value = new Zend_Measure_Pressure('100',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Pressure value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testPressureValueNegative()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Pressure value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testPressureValueDecimal()
    {
        $value = new Zend_Measure_Pressure('-100,200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Pressure value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testPressureValueDecimalSeperated()
    {
        $value = new Zend_Measure_Pressure('-100.100,200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Pressure Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testPressureValueString()
    {
        $value = new Zend_Measure_Pressure('-100.100,200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Pressure Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testPressureEquality()
    {
        $value = new Zend_Measure_Pressure('-100.100,200',Zend_Measure_Pressure::STANDARD,'de');
        $newvalue = new Zend_Measure_Pressure('-100.100,200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Pressure Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testPressureNoEquality()
    {
        $value = new Zend_Measure_Pressure('-100.100,200',Zend_Measure_Pressure::STANDARD,'de');
        $newvalue = new Zend_Measure_Pressure('-100,200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Pressure Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testPressureSetPositive()
    {
        $value = new Zend_Measure_Pressure('100',Zend_Measure_Pressure::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Pressure value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testPressureSetNegative()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Pressure value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testPressureSetDecimal()
    {
        $value = new Zend_Measure_Pressure('-100,200',Zend_Measure_Pressure::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Pressure value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testPressureSetDecimalSeperated()
    {
        $value = new Zend_Measure_Pressure('-100.100,200',Zend_Measure_Pressure::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Pressure Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testPressureSetString()
    {
        $value = new Zend_Measure_Pressure('-100.100,200',Zend_Measure_Pressure::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Pressure Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testPressureSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Pressure('100',Zend_Measure_Pressure::STANDARD,'de');
            $value->setValue('-200.200,200','Pressure::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testPressureSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Pressure('100',Zend_Measure_Pressure::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Pressure::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testPressureSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Pressure('100',Zend_Measure_Pressure::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Pressure::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testPressureSetWithNoLocale()
    {
        $value = new Zend_Measure_Pressure('100', Zend_Measure_Pressure::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Pressure::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Pressure value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testPressureSetType()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::STANDARD,'de');
        $value->setType(Zend_Measure_Pressure::TON_PER_SQUARE_FOOT);
        $this->assertEquals(Zend_Measure_Pressure::TON_PER_SQUARE_FOOT, $value->getType(), 'Zend_Measure_Pressure type expected');
    }


    /**
     * test setting type2
     * expected new type
     */
    public function testPressureSetType2()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::TON_PER_SQUARE_FOOT,'de');
        $value->setType(Zend_Measure_Pressure::STANDARD);
        $this->assertEquals(Zend_Measure_Pressure::STANDARD, $value->getType(), 'Zend_Measure_Pressure type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testPressureSetComputedType1()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::TON_PER_SQUARE_FOOT,'de');
        $value->setType(Zend_Measure_Pressure::TON_PER_SQUARE_INCH);
        $this->assertEquals(Zend_Measure_Pressure::TON_PER_SQUARE_INCH, $value->getType(), 'Zend_Measure_Pressure type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testPressureSetComputedType2()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::TON_PER_SQUARE_INCH,'de');
        $value->setType(Zend_Measure_Pressure::TON_PER_SQUARE_FOOT);
        $this->assertEquals(Zend_Measure_Pressure::TON_PER_SQUARE_FOOT, $value->getType(), 'Zend_Measure_Pressure type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testPressureSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::STANDARD,'de');
            $value->setType('Pressure::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testPressureToString()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals('-100 N/m²', $value->toString(), 'Value -100 N/m² expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testPressure_ToString()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::STANDARD,'de');
        $this->assertEquals('-100 N/m²', $value->__toString(), 'Value -100 N/m² expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testPressureConversionList()
    {
        $value = new Zend_Measure_Pressure('-100',Zend_Measure_Pressure::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
