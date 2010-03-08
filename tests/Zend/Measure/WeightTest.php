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
 * Zend_Measure_Weight
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class Zend_Measure_WeightTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Weight initialisation
     * expected instance
     */
    public function testWeightInit()
    {
        $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Weight,'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testWeightUnknownType()
    {
        try {
            $value = new Zend_Measure_Weight('100','Weight::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testWeightUnknownValue()
    {
        try {
            $value = new Zend_Measure_Weight('novalue',Zend_Measure_Weight::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testWeightUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testWeightNoLocale()
    {
        $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Weight value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testWeightValuePositive()
    {
        $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Weight value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testWeightValueNegative()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Weight value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testWeightValueDecimal()
    {
        $value = new Zend_Measure_Weight('-100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Weight value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testWeightValueDecimalSeperated()
    {
        $value = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testWeightValueString()
    {
        $value = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testWeightEquality()
    {
        $value = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $newvalue = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Weight Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testWeightNoEquality()
    {
        $value = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $newvalue = new Zend_Measure_Weight('-100,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Weight Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testWeightSetPositive()
    {
        $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Weight value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testWeightSetNegative()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Weight value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testWeightSetDecimal()
    {
        $value = new Zend_Measure_Weight('-100,200',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Weight value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testWeightSetDecimalSeperated()
    {
        $value = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testWeightSetString()
    {
        $value = new Zend_Measure_Weight('-100.100,200',Zend_Measure_Weight::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Weight Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testWeightSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
            $value->setValue('-200.200,200','Weight::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testWeightSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Weight::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testWeightSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Weight('100',Zend_Measure_Weight::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Weight::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testMeasureSetWithNoLocale()
    {
        $value = new Zend_Measure_Weight('100', Zend_Measure_Weight::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Weight::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Weight value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testWeightSetType()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $value->setType(Zend_Measure_Weight::GRAM);
        $this->assertEquals(Zend_Measure_Weight::GRAM, $value->getType(), 'Zend_Measure_Weight type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testWeightSetComputedType1()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::DRAM,'de');
        $value->setType(Zend_Measure_Weight::OUNCE);
        $this->assertEquals(Zend_Measure_Weight::OUNCE, $value->getType(), 'Zend_Measure_Weight type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testWeightSetComputedType2()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::OUNCE,'de');
        $value->setType(Zend_Measure_Weight::DRAM);
        $this->assertEquals(Zend_Measure_Weight::DRAM, $value->getType(), 'Zend_Measure_Weight type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testWeightSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
            $value->setType('Weight::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testWeightToString()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals('-100 kg', $value->toString(), 'Value -100 kg expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testWeight_ToString()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $this->assertEquals('-100 kg', $value->__toString(), 'Value -100 kg expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testWeightConversionList()
    {
        $value = new Zend_Measure_Weight('-100',Zend_Measure_Weight::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
