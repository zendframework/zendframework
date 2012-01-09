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
class CurrentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Current initialisation
     * expected instance
     */
    public function testCurrentInit()
    {
        $value = new Measure\Current('100',Measure\Current::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Current,'Zend\Measure\Current Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCurrentUnknownType()
    {
        try {
            $value = new Measure\Current('100','Current::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Current('novalue',Measure\Current::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Current('100',Measure\Current::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testCurrentNoLocale()
    {
        $value = new Measure\Current('100',Measure\Current::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Current value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testCurrentValuePositive()
    {
        $value = new Measure\Current('100',Measure\Current::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Current value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testCurrentValueNegative()
    {
        $value = new Measure\Current('-100',Measure\Current::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Current value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testCurrentValueDecimal()
    {
        $value = new Measure\Current('-100,200',Measure\Current::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Current value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testCurrentValueDecimalSeperated()
    {
        $value = new Measure\Current('-100.100,200',Measure\Current::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Current Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testCurrentValueString()
    {
        $value = new Measure\Current('-100.100,200',Measure\Current::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Current Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testCurrentEquality()
    {
        $value = new Measure\Current('-100.100,200',Measure\Current::STANDARD,'de');
        $newvalue = new Measure\Current('-100.100,200',Measure\Current::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Current Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testCurrentNoEquality()
    {
        $value = new Measure\Current('-100.100,200',Measure\Current::STANDARD,'de');
        $newvalue = new Measure\Current('-100,200',Measure\Current::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Current Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testCurrentSetPositive()
    {
        $value = new Measure\Current('100',Measure\Current::STANDARD,'de');
        $value->setValue('200',Measure\Current::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Current value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testCurrentSetNegative()
    {
        $value = new Measure\Current('-100',Measure\Current::STANDARD,'de');
        $value->setValue('-200',Measure\Current::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Current value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testCurrentSetDecimal()
    {
        $value = new Measure\Current('-100,200',Measure\Current::STANDARD,'de');
        $value->setValue('-200,200',Measure\Current::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Current value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testCurrentSetDecimalSeperated()
    {
        $value = new Measure\Current('-100.100,200',Measure\Current::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Current::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Current Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testCurrentSetString()
    {
        $value = new Measure\Current('-100.100,200',Measure\Current::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Current::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Current Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCurrentSetUnknownType()
    {
        try {
            $value = new Measure\Current('100',Measure\Current::STANDARD,'de');
            $value->setValue('-200.200,200','Current::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Current('100',Measure\Current::STANDARD,'de');
            $value->setValue('novalue',Measure\Current::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Current('100',Measure\Current::STANDARD,'de');
            $value->setValue('200',Measure\Current::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCurrentSetWithNoLocale()
    {
        $value = new Measure\Current('100', Measure\Current::STANDARD, 'de');
        $value->setValue('200', Measure\Current::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Current value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testCurrentSetType()
    {
        $value = new Measure\Current('-100',Measure\Current::STANDARD,'de');
        $value->setType(Measure\Current::NANOAMPERE);
        $this->assertEquals(Measure\Current::NANOAMPERE, $value->getType(), 'Zend\Measure\Current type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testCurrentSetTypeFailed()
    {
        try {
            $value = new Measure\Current('-100',Measure\Current::STANDARD,'de');
            $value->setType('Current::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testCurrentToString()
    {
        $value = new Measure\Current('-100',Measure\Current::STANDARD,'de');
        $this->assertEquals('-100 A', $value->toString(), 'Value -100 A expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testCurrent_ToString()
    {
        $value = new Measure\Current('-100',Measure\Current::STANDARD,'de');
        $this->assertEquals('-100 A', $value->__toString(), 'Value -100 A expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testCurrentConversionList()
    {
        $value = new Measure\Current('-100',Measure\Current::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
