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
 * Zend_Measure_Current
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
class Zend_Measure_CurrentTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Current initialisation
     * expected instance
     */
    public function testCurrentInit()
    {
        $value = new Zend_Measure_Current('100',Zend_Measure_Current::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Current,'Zend_Measure_Current Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCurrentUnknownType()
    {
        try {
            $value = new Zend_Measure_Current('100','Current::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCurrentUnknownValue()
    {
        try {
            $value = new Zend_Measure_Current('novalue',Zend_Measure_Current::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testCurrentUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Current('100',Zend_Measure_Current::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testCurrentNoLocale()
    {
        $value = new Zend_Measure_Current('100',Zend_Measure_Current::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Current value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testCurrentValuePositive()
    {
        $value = new Zend_Measure_Current('100',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Current value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testCurrentValueNegative()
    {
        $value = new Zend_Measure_Current('-100',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Current value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testCurrentValueDecimal()
    {
        $value = new Zend_Measure_Current('-100,200',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Current value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testCurrentValueDecimalSeperated()
    {
        $value = new Zend_Measure_Current('-100.100,200',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Current Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testCurrentValueString()
    {
        $value = new Zend_Measure_Current('-100.100,200',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Current Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testCurrentEquality()
    {
        $value = new Zend_Measure_Current('-100.100,200',Zend_Measure_Current::STANDARD,'de');
        $newvalue = new Zend_Measure_Current('-100.100,200',Zend_Measure_Current::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Current Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testCurrentNoEquality()
    {
        $value = new Zend_Measure_Current('-100.100,200',Zend_Measure_Current::STANDARD,'de');
        $newvalue = new Zend_Measure_Current('-100,200',Zend_Measure_Current::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Current Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testCurrentSetPositive()
    {
        $value = new Zend_Measure_Current('100',Zend_Measure_Current::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Current value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testCurrentSetNegative()
    {
        $value = new Zend_Measure_Current('-100',Zend_Measure_Current::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Current value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testCurrentSetDecimal()
    {
        $value = new Zend_Measure_Current('-100,200',Zend_Measure_Current::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Current value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testCurrentSetDecimalSeperated()
    {
        $value = new Zend_Measure_Current('-100.100,200',Zend_Measure_Current::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Current Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testCurrentSetString()
    {
        $value = new Zend_Measure_Current('-100.100,200',Zend_Measure_Current::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Current Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCurrentSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Current('100',Zend_Measure_Current::STANDARD,'de');
            $value->setValue('-200.200,200','Current::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCurrentSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Current('100',Zend_Measure_Current::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Current::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCurrentSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Current('100',Zend_Measure_Current::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Current::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCurrentSetWithNoLocale()
    {
        $value = new Zend_Measure_Current('100', Zend_Measure_Current::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Current::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Current value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testCurrentSetType()
    {
        $value = new Zend_Measure_Current('-100',Zend_Measure_Current::STANDARD,'de');
        $value->setType(Zend_Measure_Current::NANOAMPERE);
        $this->assertEquals(Zend_Measure_Current::NANOAMPERE, $value->getType(), 'Zend_Measure_Current type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testCurrentSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Current('-100',Zend_Measure_Current::STANDARD,'de');
            $value->setType('Current::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testCurrentToString()
    {
        $value = new Zend_Measure_Current('-100',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals('-100 A', $value->toString(), 'Value -100 A expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testCurrent_ToString()
    {
        $value = new Zend_Measure_Current('-100',Zend_Measure_Current::STANDARD,'de');
        $this->assertEquals('-100 A', $value->__toString(), 'Value -100 A expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testCurrentConversionList()
    {
        $value = new Zend_Measure_Current('-100',Zend_Measure_Current::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
