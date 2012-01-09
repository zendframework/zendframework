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
namespace ZendTest\Measure\Flow;
use Zend\Measure\Flow;
use Zend\Measure;

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class VolumeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Volume initialisation
     * expected instance
     */
    public function testFlow_VolumeInit()
    {
        $value = new Flow\Volume('100',Flow\Volume::STANDARD,'de');
        $this->assertTrue($value instanceof Flow\Volume,'Zend\Measure\Flow\Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_VolumeUnknownType()
    {
        try {
            $value = new Flow\Volume('100','Flow_Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_VolumeUnknownValue()
    {
        try {
            $value = new Flow\Volume('novalue',Flow\Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testFlow_VolumeUnknownLocale()
    {
        try {
            $value = new Flow\Volume('100',Flow\Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testFlow_VolumeNoLocale()
    {
        $value = new Flow\Volume('100',Flow\Volume::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Flow\Volume value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testFlow_VolumeValuePositive()
    {
        $value = new Flow\Volume('100',Flow\Volume::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Flow\Volume value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testFlow_VolumeValueNegative()
    {
        $value = new Flow\Volume('-100',Flow\Volume::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Flow\Volume value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testFlow_VolumeValueDecimal()
    {
        $value = new Flow\Volume('-100,200',Flow\Volume::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Flow\Volume value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testFlow_VolumeValueDecimalSeperated()
    {
        $value = new Flow\Volume('-100.100,200',Flow\Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Flow\Volume Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testFlow_VolumeValueString()
    {
        $value = new Flow\Volume('-100.100,200',Flow\Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Flow\Volume Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testFlow_VolumeEquality()
    {
        $value = new Flow\Volume('-100.100,200',Flow\Volume::STANDARD,'de');
        $newvalue = new Flow\Volume('-100.100,200',Flow\Volume::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Flow\Volume Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testFlow_VolumeNoEquality()
    {
        $value = new Flow\Volume('-100.100,200',Flow\Volume::STANDARD,'de');
        $newvalue = new Flow\Volume('-100,200',Flow\Volume::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Flow\Volume Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testFlow_VolumeSetPositive()
    {
        $value = new Flow\Volume('100',Flow\Volume::STANDARD,'de');
        $value->setValue('200',Flow\Volume::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Flow\Volume value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testFlow_VolumeSetNegative()
    {
        $value = new Flow\Volume('-100',Flow\Volume::STANDARD,'de');
        $value->setValue('-200',Flow\Volume::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Flow\Volume value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testFlow_VolumeSetDecimal()
    {
        $value = new Flow\Volume('-100,200',Flow\Volume::STANDARD,'de');
        $value->setValue('-200,200',Flow\Volume::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Flow\Volume value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testFlow_VolumeSetDecimalSeperated()
    {
        $value = new Flow\Volume('-100.100,200',Flow\Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Flow\Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Flow\Volume Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testFlow_VolumeSetString()
    {
        $value = new Flow\Volume('-100.100,200',Flow\Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Flow\Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Flow\Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_VolumeSetUnknownType()
    {
        try {
            $value = new Flow\Volume('100',Flow\Volume::STANDARD,'de');
            $value->setValue('-200.200,200','Flow_Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_VolumeSetUnknownValue()
    {
        try {
            $value = new Flow\Volume('100',Flow\Volume::STANDARD,'de');
            $value->setValue('novalue',Flow\Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_VolumeSetUnknownLocale()
    {
        try {
            $value = new Flow\Volume('100',Flow\Volume::STANDARD,'de');
            $value->setValue('200',Flow\Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_VolumeSetWithNoLocale()
    {
        $value = new Flow\Volume('100', Flow\Volume::STANDARD, 'de');
        $value->setValue('200', Flow\Volume::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Flow\Volume value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testFlow_VolumeSetType()
    {
        $value = new Flow\Volume('-100',Flow\Volume::STANDARD,'de');
        $value->setType(Flow\Volume::CUSEC);
        $this->assertEquals(Flow\Volume::CUSEC, $value->getType(), 'Zend\Measure\Flow\Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_VolumeSetComputedType1()
    {
        $value = new Flow\Volume('-100',Flow\Volume::STANDARD,'de');
        $value->setType(Flow\Volume::BARREL_PER_DAY);
        $this->assertEquals(Flow\Volume::BARREL_PER_DAY, $value->getType(), 'Zend\Measure\Flow\Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_VolumeSetComputedType2()
    {
        $value = new Flow\Volume('-100',Flow\Volume::BARREL_PER_DAY,'de');
        $value->setType(Flow\Volume::STANDARD);
        $this->assertEquals(Flow\Volume::STANDARD, $value->getType(), 'Zend\Measure\Flow\Volume type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testFlow_VolumeSetTypeFailed()
    {
        try {
            $value = new Flow\Volume('-100',Flow\Volume::STANDARD,'de');
            $value->setType('Flow_Volume::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testFlow_VolumeToString()
    {
        $value = new Flow\Volume('-100',Flow\Volume::STANDARD,'de');
        $this->assertEquals('-100 m³/s', $value->toString(), 'Value -100 m³/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testFlow_Volume_ToString()
    {
        $value = new Flow\Volume('-100',Flow\Volume::STANDARD,'de');
        $this->assertEquals('-100 m³/s', $value->__toString(), 'Value -100 m³/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testFlow_VolumeConversionList()
    {
        $value = new Flow\Volume('-100',Flow\Volume::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
