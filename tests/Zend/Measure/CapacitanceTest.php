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
class CapacitanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Capacitance initialisation
     * expected instance
     */
    public function testCapacitanceInit()
    {
        $value = new Measure\Capacitance('100',Measure\Capacitance::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Capacitance,'Zend\Measure\Capacitance Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCapacitanceUnknownType()
    {
        try {
            $value = new Measure\Capacitance('100','Capacitance::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCapacitanceUnknownValue()
    {
        try {
            $value = new Measure\Capacitance('novalue',Measure\Capacitance::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testCapacitanceUnknownLocale()
    {
        try {
            $value = new Measure\Capacitance('100',Measure\Capacitance::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testCapacitanceNoLocale()
    {
        $value = new Measure\Capacitance('100',Measure\Capacitance::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Capacitance value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testCapacitanceValuePositive()
    {
        $value = new Measure\Capacitance('100',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Capacitance value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testCapacitanceValueNegative()
    {
        $value = new Measure\Capacitance('-100',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Capacitance value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testCapacitanceValueDecimal()
    {
        $value = new Measure\Capacitance('-100,200',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Capacitance value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testCapacitanceValueDecimalSeperated()
    {
        $value = new Measure\Capacitance('-100.100,200',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Capacitance Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testCapacitanceValueString()
    {
        $value = new Measure\Capacitance('-100.100,200',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Capacitance Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testCapacitanceEquality()
    {
        $value = new Measure\Capacitance('-100.100,200',Measure\Capacitance::STANDARD,'de');
        $newvalue = new Measure\Capacitance('-100.100,200',Measure\Capacitance::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Capacitance Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testCapacitanceNoEquality()
    {
        $value = new Measure\Capacitance('-100.100,200',Measure\Capacitance::STANDARD,'de');
        $newvalue = new Measure\Capacitance('-100,200',Measure\Capacitance::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Capacitance Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testCapacitanceSetPositive()
    {
        $value = new Measure\Capacitance('100',Measure\Capacitance::STANDARD,'de');
        $value->setValue('200',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Capacitance value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testCapacitanceSetNegative()
    {
        $value = new Measure\Capacitance('-100',Measure\Capacitance::STANDARD,'de');
        $value->setValue('-200',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Capacitance value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testCapacitanceSetDecimal()
    {
        $value = new Measure\Capacitance('-100,200',Measure\Capacitance::STANDARD,'de');
        $value->setValue('-200,200',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Capacitance value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testCapacitanceSetDecimalSeperated()
    {
        $value = new Measure\Capacitance('-100.100,200',Measure\Capacitance::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Capacitance Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testCapacitanceSetString()
    {
        $value = new Measure\Capacitance('-100.100,200',Measure\Capacitance::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Capacitance Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCapacitanceSetUnknownType()
    {
        try {
            $value = new Measure\Capacitance('100',Measure\Capacitance::STANDARD,'de');
            $value->setValue('-200.200,200','Capacitance::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCapacitanceSetUnknownValue()
    {
        try {
            $value = new Measure\Capacitance('100',Measure\Capacitance::STANDARD,'de');
            $value->setValue('novalue',Measure\Capacitance::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCapacitanceSetUnknownLocale()
    {
        try {
            $value = new Measure\Capacitance('100',Measure\Capacitance::STANDARD,'de');
            $value->setValue('200',Measure\Capacitance::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCapacitanceSetWithNoLocale()
    {
        $value = new Measure\Capacitance('100', Measure\Capacitance::STANDARD, 'de');
        $value->setValue('200', Measure\Capacitance::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Capacitance value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testCapacitanceSetType()
    {
        $value = new Measure\Capacitance('-100',Measure\Capacitance::STANDARD,'de');
        $value->setType(Measure\Capacitance::NANOFARAD);
        $this->assertEquals(Measure\Capacitance::NANOFARAD, $value->getType(), 'Zend\Measure\Capacitance type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testCapacitanceSetTypeFailed()
    {
        try {
            $value = new Measure\Capacitance('-100',Measure\Capacitance::STANDARD,'de');
            $value->setType('Capacitance::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testCapacitanceToString()
    {
        $value = new Measure\Capacitance('-100',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals('-100 F', $value->toString(), 'Value -100 F expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testCapacitance_ToString()
    {
        $value = new Measure\Capacitance('-100',Measure\Capacitance::STANDARD,'de');
        $this->assertEquals('-100 F', $value->__toString(), 'Value -100 F expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testCapacitanceConversionList()
    {
        $value = new Measure\Capacitance('-100',Measure\Capacitance::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
