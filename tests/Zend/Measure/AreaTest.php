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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Measure_Area
 */
require_once 'Zend/Measure/Area.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_AreaTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for area initialisation
     * expected instance
     */
    public function testAreaInit()
    {
        $value = new Zend_Measure_Area('100',Zend_Measure_Area::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Area,'Zend_Measure_Area Object not returned');
    }

    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAreaUnknownType()
    {
        try {
            $value = new Zend_Measure_Area('100','Area::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown value
     * expected exception
     */
    public function testAreaUnknownValue()
    {
        try {
            $value = new Zend_Measure_Area('novalue',Zend_Measure_Area::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testAreaUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Area('100',Zend_Measure_Area::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }

    /**
     * test for standard locale
     * expected integer
     */
    public function testAreaNoLocale()
    {
        $value = new Zend_Measure_Area('100',Zend_Measure_Area::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Area value expected');
    }

    /**
     * test for positive value
     * expected integer
     */
    public function testAreaValuePositive()
    {
        $value = new Zend_Measure_Area('100',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Area value expected to be a positive integer');
    }

    /**
     * test for negative value
     * expected integer
     */
    public function testAreaValueNegative()
    {
        $value = new Zend_Measure_Area('-100',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Area value expected to be a negative integer');
    }

    /**
     * test for decimal value
     * expected float
     */
    public function testAreaValueDecimal()
    {
        $value = new Zend_Measure_Area('-100,200',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Area value expected to be a decimal value');
    }

    /**
     * test for decimal seperated value
     * expected float
     */
    public function testAreaValueDecimalSeperated()
    {
        $value = new Zend_Measure_Area('-100.100,200',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Area Object not returned');
    }

    /**
     * test for string with integrated value
     * expected float
     */
    public function testAreaValueString()
    {
        $value = new Zend_Measure_Area('-100.100,200',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Area Object not returned');
    }

    /**
     * test for equality
     * expected true
     */
    public function testAreaEquality()
    {
        $value = new Zend_Measure_Area('-100.100,200',Zend_Measure_Area::STANDARD,'de');
        $newvalue = new Zend_Measure_Area('-100.100,200',Zend_Measure_Area::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Area Object should be equal');
    }

    /**
     * test for no equality
     * expected false
     */
    public function testAreaNoEquality()
    {
        $value = new Zend_Measure_Area('-100.100,200',Zend_Measure_Area::STANDARD,'de');
        $newvalue = new Zend_Measure_Area('-100,200',Zend_Measure_Area::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Area Object should be not equal');
    }

    /**
     * test for set positive value
     * expected integer
     */
    public function testAreaSetPositive()
    {
        $value = new Zend_Measure_Area('100',Zend_Measure_Area::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Area value expected to be a positive integer');
    }

    /**
     * test for set negative value
     * expected integer
     */
    public function testAreaSetNegative()
    {
        $value = new Zend_Measure_Area('-100',Zend_Measure_Area::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Area value expected to be a negative integer');
    }

    /**
     * test for set decimal value
     * expected float
     */
    public function testAreaSetDecimal()
    {
        $value = new Zend_Measure_Area('-100,200',Zend_Measure_Area::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Area value expected to be a decimal value');
    }

    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testAreaSetDecimalSeperated()
    {
        $value = new Zend_Measure_Area('-100.100,200',Zend_Measure_Area::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Area Object not returned');
    }

    /**
     * test for set string with integrated value
     * expected float
     */
    public function testAreaSetString()
    {
        $value = new Zend_Measure_Area('-100.100,200',Zend_Measure_Area::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Area Object not returned');
    }

    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAreaSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Area('100',Zend_Measure_Area::STANDARD,'de');
            $value->setValue('-200.200,200','Area::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown value
     * expected exception
     */
    public function testAreaSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Area('100',Zend_Measure_Area::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Area::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAreaSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Area('100',Zend_Measure_Area::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Area::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAreaSetWithNoLocale()
    {
        $value = new Zend_Measure_Area('100', Zend_Measure_Area::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Area::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Area value expected to be a positive integer');
    }

    /**
     * test setting type
     * expected new type
     */
    public function testAreaSetType()
    {
        $value = new Zend_Measure_Area('-100',Zend_Measure_Area::STANDARD,'de');
        $value->setType(Zend_Measure_Area::MORGEN);
        $this->assertEquals(Zend_Measure_Area::MORGEN, $value->getType(), 'Zend_Measure_Area type expected');
    }

    /**
     * test setting computed type
     * expected new type
     */
    public function testAreaSetComputedType1()
    {
        $value = new Zend_Measure_Area('-100',Zend_Measure_Area::SQUARE_MILE,'de');
        $value->setType(Zend_Measure_Area::SQUARE_INCH);
        $this->assertEquals(Zend_Measure_Area::SQUARE_INCH, $value->getType(), 'Zend_Measure_Area type expected');
    }

    /**
     * test setting computed type
     * expected new type
     */
    public function testAreaSetComputedType2()
    {
        $value = new Zend_Measure_Area('-100',Zend_Measure_Area::SQUARE_INCH,'de');
        $value->setType(Zend_Measure_Area::SQUARE_MILE);
        $this->assertEquals(Zend_Measure_Area::SQUARE_MILE, $value->getType(), 'Zend_Measure_Area type expected');
    }

    /**
     * test setting unknown type
     * expected new type
     */
    public function testAreaSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Area('-100',Zend_Measure_Area::STANDARD,'de');
            $value->setType('Area::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }

    /**
     * test toString
     * expected string
     */
    public function testAreaToString()
    {
        $value = new Zend_Measure_Area('-100',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals('-100 m²', $value->toString(), 'Value -100 m² expected');
    }

    /**
     * test __toString
     * expected string
     */
    public function testArea_ToString()
    {
        $value = new Zend_Measure_Area('-100',Zend_Measure_Area::STANDARD,'de');
        $this->assertEquals('-100 m²', $value->__toString(), 'Value -100 m² expected');
    }

    /**
     * test getConversionList
     * expected array
     */
    public function testAreaConversionList()
    {
        $value = new Zend_Measure_Area('-100',Zend_Measure_Area::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
