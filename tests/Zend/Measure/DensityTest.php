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
class DensityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Density initialisation
     * expected instance
     */
    public function testDensityInit()
    {
        $value = new Measure\Density('100',Measure\Density::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Density,'Zend\Measure\Density Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testDensityUnknownType()
    {
        try {
            $value = new Measure\Density('100','Density::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testDensityUnknownValue()
    {
        try {
            $value = new Measure\Density('novalue',Measure\Density::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testDensityUnknownLocale()
    {
        try {
            $value = new Measure\Density('100',Measure\Density::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testDensityNoLocale()
    {
        $value = new Measure\Density('100',Measure\Density::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Density value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testDensityValuePositive()
    {
        $value = new Measure\Density('100',Measure\Density::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Density value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testDensityValueNegative()
    {
        $value = new Measure\Density('-100',Measure\Density::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Density value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testDensityValueDecimal()
    {
        $value = new Measure\Density('-100,200',Measure\Density::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Density value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testDensityValueDecimalSeperated()
    {
        $value = new Measure\Density('-100.100,200',Measure\Density::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Density Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testDensityValueString()
    {
        $value = new Measure\Density('-100.100,200',Measure\Density::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Density Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testDensityEquality()
    {
        $value = new Measure\Density('-100.100,200',Measure\Density::STANDARD,'de');
        $newvalue = new Measure\Density('-100.100,200',Measure\Density::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Density Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testDensityNoEquality()
    {
        $value = new Measure\Density('-100.100,200',Measure\Density::STANDARD,'de');
        $newvalue = new Measure\Density('-100,200',Measure\Density::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Density Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testDensitySetPositive()
    {
        $value = new Measure\Density('100',Measure\Density::STANDARD,'de');
        $value->setValue('200',Measure\Density::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Density value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testDensitySetNegative()
    {
        $value = new Measure\Density('-100',Measure\Density::STANDARD,'de');
        $value->setValue('-200',Measure\Density::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Density value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testDensitySetDecimal()
    {
        $value = new Measure\Density('-100,200',Measure\Density::STANDARD,'de');
        $value->setValue('-200,200',Measure\Density::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Density value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testDensitySetDecimalSeperated()
    {
        $value = new Measure\Density('-100.100,200',Measure\Density::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Density::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Density Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testDensitySetString()
    {
        $value = new Measure\Density('-100.100,200',Measure\Density::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Density::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Density Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testDensitySetUnknownType()
    {
        try {
            $value = new Measure\Density('100',Measure\Density::STANDARD,'de');
            $value->setValue('-200.200,200','Density::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testDensitySetUnknownValue()
    {
        try {
            $value = new Measure\Density('100',Measure\Density::STANDARD,'de');
            $value->setValue('novalue',Measure\Density::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testDensitySetUnknownLocale()
    {
        try {
            $value = new Measure\Density('100',Measure\Density::STANDARD,'de');
            $value->setValue('200',Measure\Density::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testDensitySetWithNoLocale()
    {
        $value = new Measure\Density('100', Measure\Density::STANDARD, 'de');
        $value->setValue('200', Measure\Density::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Density value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testDensitySetType()
    {
        $value = new Measure\Density('-100',Measure\Density::STANDARD,'de');
        $value->setType(Measure\Density::GOLD);
        $this->assertEquals(Measure\Density::GOLD, $value->getType(), 'Zend\Measure\Density type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testDensitySetComputedType1()
    {
        $value = new Measure\Density('-100',Measure\Density::SILVER,'de');
        $value->setType(Measure\Density::TONNE_PER_MILLILITER);
        $this->assertEquals(Measure\Density::TONNE_PER_MILLILITER, $value->getType(), 'Zend\Measure\Density type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testDensitySetComputedType2()
    {
        $value = new Measure\Density('-100',Measure\Density::TONNE_PER_MILLILITER,'de');
        $value->setType(Measure\Density::GOLD);
        $this->assertEquals(Measure\Density::GOLD, $value->getType(), 'Zend\Measure\Density type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testDensitySetTypeFailed()
    {
        try {
            $value = new Measure\Density('-100',Measure\Density::STANDARD,'de');
            $value->setType('Density::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testDensityToString()
    {
        $value = new Measure\Density('-100',Measure\Density::STANDARD,'de');
        $this->assertEquals('-100 kg/m³', $value->toString(), 'Value -100 kg/m³ expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testDensity_ToString()
    {
        $value = new Measure\Density('-100',Measure\Density::STANDARD,'de');
        $this->assertEquals('-100 kg/m³', $value->__toString(), 'Value -100 kg/m³ expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testDensityConversionList()
    {
        $value = new Measure\Density('-100',Measure\Density::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
