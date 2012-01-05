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
namespace ZendTest\Measure\Cooking;
use Zend\Measure\Cooking;
use Zend\Measure;

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class WeightTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Mass initialisation
     * expected instance
     */
    public function testCooking_WeightInit()
    {
        $value = new Cooking\Weight('100',Cooking\Weight::STANDARD,'de');
        $this->assertTrue($value instanceof Cooking\Weight,'Zend\Measure\Cooking\Weight Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCooking_WeightUnknownType()
    {
        try {
            $value = new Cooking\Weight('100','Cooking_Weight::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCooking_WeightUnknownValue()
    {
        try {
            $value = new Cooking\Weight('novalue',Cooking\Weight::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testCooking_WeightUnknownLocale()
    {
        try {
            $value = new Cooking\Weight('100',Cooking\Weight::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testCooking_WeightNoLocale()
    {
        $value = new Cooking\Weight('100',Cooking\Weight::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Cooking\Weight value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testCooking_WeightValuePositive()
    {
        $value = new Cooking\Weight('100',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Cooking\Weight value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testCooking_WeightValueNegative()
    {
        $value = new Cooking\Weight('-100',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Cooking\Weight value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testCooking_WeightValueDecimal()
    {
        $value = new Cooking\Weight('-100,200',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Cooking\Weight value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testCooking_WeightValueDecimalSeperated()
    {
        $value = new Cooking\Weight('-100.100,200',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Cooking\Weight Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testCooking_WeightValueString()
    {
        $value = new Cooking\Weight('-100.100,200',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Cooking\Weight Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testCooking_WeightEquality()
    {
        $value = new Cooking\Weight('-100.100,200',Cooking\Weight::STANDARD,'de');
        $newvalue = new Cooking\Weight('-100.100,200',Cooking\Weight::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Cooking\Weight Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testCooking_WeightNoEquality()
    {
        $value = new Cooking\Weight('-100.100,200',Cooking\Weight::STANDARD,'de');
        $newvalue = new Cooking\Weight('-100,200',Cooking\Weight::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Cooking\Weight Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testCooking_WeightSetPositive()
    {
        $value = new Cooking\Weight('100',Cooking\Weight::STANDARD,'de');
        $value->setValue('200',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Cooking\Weight value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testCooking_WeightSetNegative()
    {
        $value = new Cooking\Weight('-100',Cooking\Weight::STANDARD,'de');
        $value->setValue('-200',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Cooking\Weight value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testCooking_WeightSetDecimal()
    {
        $value = new Cooking\Weight('-100,200',Cooking\Weight::STANDARD,'de');
        $value->setValue('-200,200',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Cooking\Weight value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testCooking_WeightSetDecimalSeperated()
    {
        $value = new Cooking\Weight('-100.100,200',Cooking\Weight::STANDARD,'de');
        $value->setValue('-200.200,200',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Cooking\Weight Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testCooking_WeightSetString()
    {
        $value = new Cooking\Weight('-100.100,200',Cooking\Weight::STANDARD,'de');
        $value->setValue('-200.200,200',Cooking\Weight::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Cooking\Weight Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testCooking_WeightSetUnknownType()
    {
        try {
            $value = new Cooking\Weight('100',Cooking\Weight::STANDARD,'de');
            $value->setValue('-200.200,200','Cooking_Weight::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testCooking_WeightSetUnknownValue()
    {
        try {
            $value = new Cooking\Weight('100',Cooking\Weight::STANDARD,'de');
            $value->setValue('novalue',Cooking\Weight::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCooking_WeightSetUnknownLocale()
    {
        try {
            $value = new Cooking\Weight('100',Cooking\Weight::STANDARD,'de');
            $value->setValue('200',Cooking\Weight::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testCooking_WeightSetWithNoLocale()
    {
        $value = new Cooking\Weight('100', Cooking\Weight::STANDARD, 'de');
        $value->setValue('200', Cooking\Weight::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Cooking\Weight value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testCooking_WeightSetType()
    {
        $value = new Cooking\Weight('-100',Cooking\Weight::STANDARD,'de');
        $value->setType(Cooking\Weight::CUP);
        $this->assertEquals(Cooking\Weight::CUP, $value->getType(), 'Zend\Measure\Cooking\Weight type expected');    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testCooking_WeightSetComputedType1()
    {
        $value = new Cooking\Weight('-100',Cooking\Weight::STANDARD,'de');
        $value->setType(Cooking\Weight::CUP);
        $this->assertEquals(Cooking\Weight::CUP, $value->getType(), 'Zend\Measure\Cooking\Weight type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testCooking_WeightSetComputedType2()
    {
        $value = new Cooking\Weight('-100',Cooking\Weight::CUP,'de');
        $value->setType(Cooking\Weight::STANDARD);
        $this->assertEquals(Cooking\Weight::STANDARD, $value->getType(), 'Zend\Measure\Cooking\Weight type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testCooking_WeightSetTypeFailed()
    {
        try {
            $value = new Cooking\Weight('-100',Cooking\Weight::STANDARD,'de');
            $value->setType('Cooking_Weight::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testCooking_WeightToString()
    {
        $value = new Cooking\Weight('-100',Cooking\Weight::STANDARD,'de');
        $this->assertEquals('-100 g', $value->toString(), 'Value -100 g expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testCooking_Weight_ToString()
    {
        $value = new Cooking\Weight('-100',Cooking\Weight::STANDARD,'de');
        $this->assertEquals('-100 g', $value->__toString(), 'Value -100 g expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testCooking_WeightConversionList()
    {
        $value = new Cooking\Weight('-100',Cooking\Weight::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
