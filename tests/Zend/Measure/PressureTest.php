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
namespace ZendTest\Measure;
use Zend\Measure;

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class PressureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Pressure initialisation
     * expected instance
     */
    public function testPressureInit()
    {
        $value = new Measure\Pressure('100',Measure\Pressure::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Pressure,'Zend\Measure\Pressure Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testPressureUnknownType()
    {
        try {
            $value = new Measure\Pressure('100','Pressure::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Pressure('novalue',Measure\Pressure::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Pressure('100',Measure\Pressure::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testPressureNoLocale()
    {
        $value = new Measure\Pressure('100',Measure\Pressure::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Pressure value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testPressureValuePositive()
    {
        $value = new Measure\Pressure('100',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Pressure value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testPressureValueNegative()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Pressure value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testPressureValueDecimal()
    {
        $value = new Measure\Pressure('-100,200',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Pressure value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testPressureValueDecimalSeperated()
    {
        $value = new Measure\Pressure('-100.100,200',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Pressure Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testPressureValueString()
    {
        $value = new Measure\Pressure('-100.100,200',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Pressure Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testPressureEquality()
    {
        $value = new Measure\Pressure('-100.100,200',Measure\Pressure::STANDARD,'de');
        $newvalue = new Measure\Pressure('-100.100,200',Measure\Pressure::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Pressure Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testPressureNoEquality()
    {
        $value = new Measure\Pressure('-100.100,200',Measure\Pressure::STANDARD,'de');
        $newvalue = new Measure\Pressure('-100,200',Measure\Pressure::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Pressure Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testPressureSetPositive()
    {
        $value = new Measure\Pressure('100',Measure\Pressure::STANDARD,'de');
        $value->setValue('200',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Pressure value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testPressureSetNegative()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::STANDARD,'de');
        $value->setValue('-200',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Pressure value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testPressureSetDecimal()
    {
        $value = new Measure\Pressure('-100,200',Measure\Pressure::STANDARD,'de');
        $value->setValue('-200,200',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Pressure value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testPressureSetDecimalSeperated()
    {
        $value = new Measure\Pressure('-100.100,200',Measure\Pressure::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Pressure Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testPressureSetString()
    {
        $value = new Measure\Pressure('-100.100,200',Measure\Pressure::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Pressure::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Pressure Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testPressureSetUnknownType()
    {
        try {
            $value = new Measure\Pressure('100',Measure\Pressure::STANDARD,'de');
            $value->setValue('-200.200,200','Pressure::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Pressure('100',Measure\Pressure::STANDARD,'de');
            $value->setValue('novalue',Measure\Pressure::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Pressure('100',Measure\Pressure::STANDARD,'de');
            $value->setValue('200',Measure\Pressure::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testPressureSetWithNoLocale()
    {
        $value = new Measure\Pressure('100', Measure\Pressure::STANDARD, 'de');
        $value->setValue('200', Measure\Pressure::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Pressure value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testPressureSetType()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::STANDARD,'de');
        $value->setType(Measure\Pressure::TON_PER_SQUARE_FOOT);
        $this->assertEquals(Measure\Pressure::TON_PER_SQUARE_FOOT, $value->getType(), 'Zend\Measure\Pressure type expected');
    }


    /**
     * test setting type2
     * expected new type
     */
    public function testPressureSetType2()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::TON_PER_SQUARE_FOOT,'de');
        $value->setType(Measure\Pressure::STANDARD);
        $this->assertEquals(Measure\Pressure::STANDARD, $value->getType(), 'Zend\Measure\Pressure type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testPressureSetComputedType1()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::TON_PER_SQUARE_FOOT,'de');
        $value->setType(Measure\Pressure::TON_PER_SQUARE_INCH);
        $this->assertEquals(Measure\Pressure::TON_PER_SQUARE_INCH, $value->getType(), 'Zend\Measure\Pressure type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testPressureSetComputedType2()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::TON_PER_SQUARE_INCH,'de');
        $value->setType(Measure\Pressure::TON_PER_SQUARE_FOOT);
        $this->assertEquals(Measure\Pressure::TON_PER_SQUARE_FOOT, $value->getType(), 'Zend\Measure\Pressure type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testPressureSetTypeFailed()
    {
        try {
            $value = new Measure\Pressure('-100',Measure\Pressure::STANDARD,'de');
            $value->setType('Pressure::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testPressureToString()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::STANDARD,'de');
        $this->assertEquals('-100 N/m²', $value->toString(), 'Value -100 N/m² expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testPressure_ToString()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::STANDARD,'de');
        $this->assertEquals('-100 N/m²', $value->__toString(), 'Value -100 N/m² expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testPressureConversionList()
    {
        $value = new Measure\Pressure('-100',Measure\Pressure::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
