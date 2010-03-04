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
 * Zend_Measure_Time
 */
require_once 'Zend/Measure/Time.php';

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
class Zend_Measure_TimeTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Time initialisation
     * expected instance
     */
    public function testTimeInit()
    {
        $value = new Zend_Measure_Time('100',Zend_Measure_Time::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Time,'Zend_Measure_Time Object not returned');
    }

    /**
     * test for exception unknown type
     * expected exception
     */
    public function testTimeUnknownType()
    {
        try {
            $value = new Zend_Measure_Time('100','Time::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }

    /**
     * test for exception unknown value
     * expected exception
     */
    public function testTimeUnknownValue()
    {
        try {
            $value = new Zend_Measure_Time('novalue',Zend_Measure_Time::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }

    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testTimeUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Time('100',Zend_Measure_Time::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }

    /**
     * test for standard locale
     * expected integer
     */
    public function testTimeNoLocale()
    {
        $value = new Zend_Measure_Time('100',Zend_Measure_Time::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Time value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testTimeValuePositive()
    {
        $value = new Zend_Measure_Time('100',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Time value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testTimeValueNegative()
    {
        $value = new Zend_Measure_Time('-100',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Time value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testTimeValueDecimal()
    {
        $value = new Zend_Measure_Time('-100,200',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Time value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testTimeValueDecimalSeperated()
    {
        $value = new Zend_Measure_Time('-100.100,200',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Time Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testTimeValueString()
    {
        $value = new Zend_Measure_Time('-100.100,200',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Time Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testTimeEquality()
    {
        $value = new Zend_Measure_Time('-100.100,200',Zend_Measure_Time::STANDARD,'de');
        $newvalue = new Zend_Measure_Time('-100.100,200',Zend_Measure_Time::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Time Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testTimeNoEquality()
    {
        $value = new Zend_Measure_Time('-100.100,200',Zend_Measure_Time::STANDARD,'de');
        $newvalue = new Zend_Measure_Time('-100,200',Zend_Measure_Time::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Time Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testTimeSetPositive()
    {
        $value = new Zend_Measure_Time('100',Zend_Measure_Time::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Time value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testTimeSetNegative()
    {
        $value = new Zend_Measure_Time('-100',Zend_Measure_Time::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Time value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testTimeSetDecimal()
    {
        $value = new Zend_Measure_Time('-100,200',Zend_Measure_Time::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Time value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testTimeSetDecimalSeperated()
    {
        $value = new Zend_Measure_Time('-100.100,200',Zend_Measure_Time::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Time Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testTimeSetString()
    {
        $value = new Zend_Measure_Time('-100.100,200',Zend_Measure_Time::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Time Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testTimeSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Time('100',Zend_Measure_Time::STANDARD,'de');
            $value->setValue('-200.200,200','Time::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testTimeSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Time('100',Zend_Measure_Time::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Time::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Exception $e) {
            return; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testTimeSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Time('100',Zend_Measure_Time::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Time::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testTimeSetWithNoLocale()
    {
        $value = new Zend_Measure_Time('100', Zend_Measure_Time::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Time::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Time value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testTimeSetType()
    {
        $value = new Zend_Measure_Time('-100',Zend_Measure_Time::STANDARD,'de');
        $value->setType(Zend_Measure_Time::MINUTE);
        $this->assertEquals(Zend_Measure_Time::MINUTE, $value->getType(), 'Zend_Measure_Time type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testTimeSetComputedType1()
    {
        $value = new Zend_Measure_Time('-100',Zend_Measure_Time::STANDARD,'de');
        $value->setType(Zend_Measure_Time::YEAR);
        $this->assertEquals(Zend_Measure_Time::YEAR, $value->getType(), 'Zend_Measure_Time type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testTimeSetComputedType2()
    {
        $value = new Zend_Measure_Time('-100',Zend_Measure_Time::YEAR,'de');
        $value->setType(Zend_Measure_Time::LEAPYEAR);
        $this->assertEquals(Zend_Measure_Time::LEAPYEAR, $value->getType(), 'Zend_Measure_Time type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testTimeSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Time('-100',Zend_Measure_Time::STANDARD,'de');
            $value->setType('Time::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testTimeToString()
    {
        $value = new Zend_Measure_Time('-100',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals('-100 s', $value->toString(), 'Value -100 s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testTime_ToString()
    {
        $value = new Zend_Measure_Time('-100',Zend_Measure_Time::STANDARD,'de');
        $this->assertEquals('-100 s', $value->__toString(), 'Value -100 s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testTimeConversionList()
    {
        $value = new Zend_Measure_Time('-100',Zend_Measure_Time::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }

    /**
     * @group ZF-9078
     */
    public function testSetTypeOnPhpMathWithStrippedValue()
    {
        $locale = new Zend_Locale('en_US');
        $time = new Zend_Measure_Time(0, Zend_Measure_Time::SECOND);
        $time->setLocale($locale);
        $time->setType(Zend_Measure_Time::SECOND);
        $seconds = $time->getValue();
        $this->assertEquals(0, $seconds);
        $this->assertEquals(Zend_Measure_Time::SECOND, $time->getType());
    }
}
