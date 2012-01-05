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
class SpeedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Speed initialisation
     * expected instance
     */
    public function testSpeedInit()
    {
        $value = new Measure\Speed('100',Measure\Speed::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Speed,'Zend\Measure\Speed Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testSpeedUnknownType()
    {
        try {
            $value = new Measure\Speed('100','Speed::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testSpeedUnknownValue()
    {
        try {
            $value = new Measure\Speed('novalue',Measure\Speed::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testSpeedUnknownLocale()
    {
        try {
            $value = new Measure\Speed('100',Measure\Speed::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testSpeedNoLocale()
    {
        $value = new Measure\Speed('100',Measure\Speed::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Speed value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testSpeedValuePositive()
    {
        $value = new Measure\Speed('100',Measure\Speed::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Speed value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testSpeedValueNegative()
    {
        $value = new Measure\Speed('-100',Measure\Speed::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Speed value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testSpeedValueDecimal()
    {
        $value = new Measure\Speed('-100,200',Measure\Speed::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Speed value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testSpeedValueDecimalSeperated()
    {
        $value = new Measure\Speed('-100.100,200',Measure\Speed::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Speed Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testSpeedValueString()
    {
        $value = new Measure\Speed('-100.100,200',Measure\Speed::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Speed Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testSpeedEquality()
    {
        $value = new Measure\Speed('-100.100,200',Measure\Speed::STANDARD,'de');
        $newvalue = new Measure\Speed('-100.100,200',Measure\Speed::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Speed Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testSpeedNoEquality()
    {
        $value = new Measure\Speed('-100.100,200',Measure\Speed::STANDARD,'de');
        $newvalue = new Measure\Speed('-100,200',Measure\Speed::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Speed Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testSpeedSetPositive()
    {
        $value = new Measure\Speed('100',Measure\Speed::STANDARD,'de');
        $value->setValue('200',Measure\Speed::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Speed value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testSpeedSetNegative()
    {
        $value = new Measure\Speed('-100',Measure\Speed::STANDARD,'de');
        $value->setValue('-200',Measure\Speed::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Speed value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testSpeedSetDecimal()
    {
        $value = new Measure\Speed('-100,200',Measure\Speed::STANDARD,'de');
        $value->setValue('-200,200',Measure\Speed::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Speed value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testSpeedSetDecimalSeperated()
    {
        $value = new Measure\Speed('-100.100,200',Measure\Speed::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Speed::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Speed Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testSpeedSetString()
    {
        $value = new Measure\Speed('-100.100,200',Measure\Speed::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Speed::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Speed Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testSpeedSetUnknownType()
    {
        try {
            $value = new Measure\Speed('100',Measure\Speed::STANDARD,'de');
            $value->setValue('-200.200,200','Speed::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testSpeedSetUnknownValue()
    {
        try {
            $value = new Measure\Speed('100',Measure\Speed::STANDARD,'de');
            $value->setValue('novalue',Measure\Speed::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testSpeedSetUnknownLocale()
    {
        try {
            $value = new Measure\Speed('100',Measure\Speed::STANDARD,'de');
            $value->setValue('200',Measure\Speed::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testSpeedSetWithNoLocale()
    {
        $value = new Measure\Speed('100', Measure\Speed::STANDARD, 'de');
        $value->setValue('200', Measure\Speed::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Speed value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testSpeedSetType()
    {
        $value = new Measure\Speed('-100',Measure\Speed::STANDARD,'de');
        $value->setType(Measure\Speed::METER_PER_HOUR);
        $this->assertEquals(Measure\Speed::METER_PER_HOUR, $value->getType(), 'Zend\Measure\Speed type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testSpeedSetComputedType1()
    {
        $value = new Measure\Speed('-100',Measure\Speed::STANDARD,'de');
        $value->setType(Measure\Speed::METER_PER_HOUR);
        $this->assertEquals(Measure\Speed::METER_PER_HOUR, $value->getType(), 'Zend\Measure\Speed type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testSpeedSetComputedType2()
    {
        $value = new Measure\Speed('-100',Measure\Speed::METER_PER_HOUR,'de');
        $value->setType(Measure\Speed::STANDARD);
        $this->assertEquals(Measure\Speed::STANDARD, $value->getType(), 'Zend\Measure\Speed type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testSpeedSetTypeFailed()
    {
        try {
            $value = new Measure\Speed('-100',Measure\Speed::STANDARD,'de');
            $value->setType('Speed::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testSpeedToString()
    {
        $value = new Measure\Speed('-100',Measure\Speed::STANDARD,'de');
        $this->assertEquals('-100 m/s', $value->toString(), 'Value -100 m/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testSpeed_ToString()
    {
        $value = new Measure\Speed('-100',Measure\Speed::STANDARD,'de');
        $this->assertEquals('-100 m/s', $value->__toString(), 'Value -100 m/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testSpeedConversionList()
    {
        $value = new Measure\Speed('-100',Measure\Speed::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
