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
class ForceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Force initialisation
     * expected instance
     */
    public function testForceInit()
    {
        $value = new Measure\Force('100',Measure\Force::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Force,'Zend\Measure\Force Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testForceUnknownType()
    {
        try {
            $value = new Measure\Force('100','Force::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testForceUnknownValue()
    {
        try {
            $value = new Measure\Force('novalue',Measure\Force::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testForceUnknownLocale()
    {
        try {
            $value = new Measure\Force('100',Measure\Force::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testForceNoLocale()
    {
        $value = new Measure\Force('100',Measure\Force::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Force value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testForceValuePositive()
    {
        $value = new Measure\Force('100',Measure\Force::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Force value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testForceValueNegative()
    {
        $value = new Measure\Force('-100',Measure\Force::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Force value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testForceValueDecimal()
    {
        $value = new Measure\Force('-100,200',Measure\Force::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Force value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testForceValueDecimalSeperated()
    {
        $value = new Measure\Force('-100.100,200',Measure\Force::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Force Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testForceValueString()
    {
        $value = new Measure\Force('-100.100,200',Measure\Force::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Force Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testForceEquality()
    {
        $value = new Measure\Force('-100.100,200',Measure\Force::STANDARD,'de');
        $newvalue = new Measure\Force('-100.100,200',Measure\Force::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Force Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testForceNoEquality()
    {
        $value = new Measure\Force('-100.100,200',Measure\Force::STANDARD,'de');
        $newvalue = new Measure\Force('-100,200',Measure\Force::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Force Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testForceSetPositive()
    {
        $value = new Measure\Force('100',Measure\Force::STANDARD,'de');
        $value->setValue('200',Measure\Force::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Force value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testForceSetNegative()
    {
        $value = new Measure\Force('-100',Measure\Force::STANDARD,'de');
        $value->setValue('-200',Measure\Force::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Force value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testForceSetDecimal()
    {
        $value = new Measure\Force('-100,200',Measure\Force::STANDARD,'de');
        $value->setValue('-200,200',Measure\Force::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Force value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testForceSetDecimalSeperated()
    {
        $value = new Measure\Force('-100.100,200',Measure\Force::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Force::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Force Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testForceSetString()
    {
        $value = new Measure\Force('-100.100,200',Measure\Force::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Force::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Force Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testForceSetUnknownType()
    {
        try {
            $value = new Measure\Force('100',Measure\Force::STANDARD,'de');
            $value->setValue('-200.200,200','Force::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testForceSetUnknownValue()
    {
        try {
            $value = new Measure\Force('100',Measure\Force::STANDARD,'de');
            $value->setValue('novalue',Measure\Force::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testForceSetUnknownLocale()
    {
        try {
            $value = new Measure\Force('100',Measure\Force::STANDARD,'de');
            $value->setValue('200',Measure\Force::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testForceSetWithNoLocale()
    {
        $value = new Measure\Force('100', Measure\Force::STANDARD, 'de');
        $value->setValue('200', Measure\Force::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Force value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testForceSetType()
    {
        $value = new Measure\Force('-100',Measure\Force::STANDARD,'de');
        $value->setType(Measure\Force::NANONEWTON);
        $this->assertEquals(Measure\Force::NANONEWTON, $value->getType(), 'Zend\Measure\Force type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testForceSetTypeFailed()
    {
        try {
            $value = new Measure\Force('-100',Measure\Force::STANDARD,'de');
            $value->setType('Force::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testForceToString()
    {
        $value = new Measure\Force('-100',Measure\Force::STANDARD,'de');
        $this->assertEquals('-100 N', $value->toString(), 'Value -100 N expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testForce_ToString()
    {
        $value = new Measure\Force('-100',Measure\Force::STANDARD,'de');
        $this->assertEquals('-100 N', $value->__toString(), 'Value -100 N expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testForceConversionList()
    {
        $value = new Measure\Force('-100',Measure\Force::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
