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
class LightnessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Lightness initialisation
     * expected instance
     */
    public function testLightnessInit()
    {
        $value = new Measure\Lightness('100',Measure\Lightness::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Lightness,'Zend\Measure\Lightness Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testLightnessUnknownType()
    {
        try {
            $value = new Measure\Lightness('100','Lightness::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testLightnessUnknownValue()
    {
        try {
            $value = new Measure\Lightness('novalue',Measure\Lightness::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testLightnessUnknownLocale()
    {
        try {
            $value = new Measure\Lightness('100',Measure\Lightness::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testLightnessNoLocale()
    {
        $value = new Measure\Lightness('100',Measure\Lightness::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Lightness value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testLightnessValuePositive()
    {
        $value = new Measure\Lightness('100',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Lightness value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testLightnessValueNegative()
    {
        $value = new Measure\Lightness('-100',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Lightness value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testLightnessValueDecimal()
    {
        $value = new Measure\Lightness('-100,200',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Lightness value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testLightnessValueDecimalSeperated()
    {
        $value = new Measure\Lightness('-100.100,200',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Lightness Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testLightnessValueString()
    {
        $value = new Measure\Lightness('-100.100,200',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Lightness Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testLightnessEquality()
    {
        $value = new Measure\Lightness('-100.100,200',Measure\Lightness::STANDARD,'de');
        $newvalue = new Measure\Lightness('-100.100,200',Measure\Lightness::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Lightness Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testLightnessNoEquality()
    {
        $value = new Measure\Lightness('-100.100,200',Measure\Lightness::STANDARD,'de');
        $newvalue = new Measure\Lightness('-100,200',Measure\Lightness::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Lightness Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testLightnessSetPositive()
    {
        $value = new Measure\Lightness('100',Measure\Lightness::STANDARD,'de');
        $value->setValue('200',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Lightness value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testLightnessSetNegative()
    {
        $value = new Measure\Lightness('-100',Measure\Lightness::STANDARD,'de');
        $value->setValue('-200',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Lightness value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testLightnessSetDecimal()
    {
        $value = new Measure\Lightness('-100,200',Measure\Lightness::STANDARD,'de');
        $value->setValue('-200,200',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Lightness value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testLightnessSetDecimalSeperated()
    {
        $value = new Measure\Lightness('-100.100,200',Measure\Lightness::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Lightness Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testLightnessSetString()
    {
        $value = new Measure\Lightness('-100.100,200',Measure\Lightness::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Lightness::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Lightness Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testLightnessSetUnknownType()
    {
        try {
            $value = new Measure\Lightness('100',Measure\Lightness::STANDARD,'de');
            $value->setValue('-200.200,200','Lightness::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testLightnessSetUnknownValue()
    {
        try {
            $value = new Measure\Lightness('100',Measure\Lightness::STANDARD,'de');
            $value->setValue('novalue',Measure\Lightness::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testLightnessSetUnknownLocale()
    {
        try {
            $value = new Measure\Lightness('100',Measure\Lightness::STANDARD,'de');
            $value->setValue('200',Measure\Lightness::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testLightnessSetWithNoLocale()
    {
        $value = new Measure\Lightness('100', Measure\Lightness::STANDARD, 'de');
        $value->setValue('200', Measure\Lightness::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Lightness value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testLightnessSetType()
    {
        $value = new Measure\Lightness('-100',Measure\Lightness::STANDARD,'de');
        $value->setType(Measure\Lightness::STILB);
        $this->assertEquals(Measure\Lightness::STILB, $value->getType(), 'Zend\Measure\Lightness type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testLightnessSetTypeFailed()
    {
        try {
            $value = new Measure\Lightness('-100',Measure\Lightness::STANDARD,'de');
            $value->setType('Lightness::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testLightnessToString()
    {
        $value = new Measure\Lightness('-100',Measure\Lightness::STANDARD,'de');
        $this->assertEquals('-100 cd/m²', $value->toString(), 'Value -100 cd/m² expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testLightness_ToString()
    {
        $value = new Measure\Lightness('-100',Measure\Lightness::STANDARD,'de');
        $this->assertEquals('-100 cd/m²', $value->__toString(), 'Value -100 cd/m² expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testLightnessConversionList()
    {
        $value = new Measure\Lightness('-100',Measure\Lightness::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
