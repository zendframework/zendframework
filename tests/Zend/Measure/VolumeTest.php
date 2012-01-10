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
class VolumeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }


    /**
     * test for Volume initialisation
     * expected instance
     */
    public function testVolumeInit()
    {
        $value = new Measure\Volume('100',Measure\Volume::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Volume,'Zend\Measure\Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testVolumeUnknownType()
    {
        try {
            $value = new Measure\Volume('100','Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testVolumeUnknownValue()
    {
        try {
            $value = new Measure\Volume('novalue',Measure\Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testVolumeUnknownLocale()
    {
        try {
            $value = new Measure\Volume('100',Measure\Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testVolumeNoLocale()
    {
        $value = new Measure\Volume('100',Measure\Volume::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Volume value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testVolumeValuePositive()
    {
        $value = new Measure\Volume('100',Measure\Volume::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Volume value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testVolumeValueNegative()
    {
        $value = new Measure\Volume('-100',Measure\Volume::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Volume value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testVolumeValueDecimal()
    {
        $value = new Measure\Volume('-100,200',Measure\Volume::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Volume value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testVolumeValueDecimalSeperated()
    {
        $value = new Measure\Volume('-100.100,200',Measure\Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Volume Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testVolumeValueString()
    {
        $value = new Measure\Volume('-100.100,200',Measure\Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Volume Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testVolumeEquality()
    {
        $value = new Measure\Volume('-100.100,200',Measure\Volume::STANDARD,'de');
        $newvalue = new Measure\Volume('-100.100,200',Measure\Volume::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Volume Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testVolumeNoEquality()
    {
        $value = new Measure\Volume('-100.100,200',Measure\Volume::STANDARD,'de');
        $newvalue = new Measure\Volume('-100,200',Measure\Volume::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Volume Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testVolumeSetPositive()
    {
        $value = new Measure\Volume('100',Measure\Volume::STANDARD,'de');
        $value->setValue('200',Measure\Volume::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Volume value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testVolumeSetNegative()
    {
        $value = new Measure\Volume('-100',Measure\Volume::STANDARD,'de');
        $value->setValue('-200',Measure\Volume::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Volume value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testVolumeSetDecimal()
    {
        $value = new Measure\Volume('-100,200',Measure\Volume::STANDARD,'de');
        $value->setValue('-200,200',Measure\Volume::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Volume value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testVolumeSetDecimalSeperated()
    {
        $value = new Measure\Volume('-100.100,200',Measure\Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Volume Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testVolumeSetString()
    {
        $value = new Measure\Volume('-100.100,200',Measure\Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testVolumeSetUnknownType()
    {
        try {
            $value = new Measure\Volume('100',Measure\Volume::STANDARD,'de');
            $value->setValue('-200.200,200','Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testVolumeSetUnknownValue()
    {
        try {
            $value = new Measure\Volume('100',Measure\Volume::STANDARD,'de');
            $value->setValue('novalue',Measure\Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testVolumeSetUnknownLocale()
    {
        try {
            $value = new Measure\Volume('100',Measure\Volume::STANDARD,'de');
            $value->setValue('200',Measure\Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testVolumeSetWithNoLocale()
    {
        $value = new Measure\Volume('100', Measure\Volume::STANDARD, 'de');
        $value->setValue('200', Measure\Volume::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Volume value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testVolumeSetType()
    {
        $value = new Measure\Volume('-100',Measure\Volume::STANDARD,'de');
        $value->setType(Measure\Volume::CORD);
        $this->assertEquals(Measure\Volume::CORD, $value->getType(), 'Zend\Measure\Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testVolumeSetComputedType1()
    {
        $value = new Measure\Volume('-100',Measure\Volume::STANDARD,'de');
        $value->setType(Measure\Volume::CUBIC_YARD);
        $this->assertEquals(Measure\Volume::CUBIC_YARD, $value->getType(), 'Zend\Measure\Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testVolumeSetComputedType2()
    {
        $value = new Measure\Volume('-100',Measure\Volume::CUBIC_YARD,'de');
        $value->setType(Measure\Volume::STANDARD);
        $this->assertEquals(Measure\Volume::STANDARD, $value->getType(), 'Zend\Measure\Volume type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testVolumeSetTypeFailed()
    {
        try {
            $value = new Measure\Volume('-100',Measure\Volume::STANDARD,'de');
            $value->setType('Volume::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testVolumeToString()
    {
        $value = new Measure\Volume('-100',Measure\Volume::STANDARD,'de');
        $this->assertEquals('-100 m³', $value->toString(), 'Value -100 m³ expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testVolume_ToString()
    {
        $value = new Measure\Volume('-100',Measure\Volume::STANDARD,'de');
        $this->assertEquals('-100 m³', $value->__toString(), 'Value -100 m³ expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testVolumeConversionList()
    {
        $value = new Measure\Volume('-100',Measure\Volume::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
