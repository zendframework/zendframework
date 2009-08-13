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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Measure_Volume
 */
require_once 'Zend/Measure/Volume.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class Zend_Measure_VolumeTest extends PHPUnit_Framework_TestCase
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
        $value = new Zend_Measure_Volume('100',Zend_Measure_Volume::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Volume,'Zend_Measure_Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testVolumeUnknownType()
    {
        try {
            $value = new Zend_Measure_Volume('100','Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Volume('novalue',Zend_Measure_Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Volume('100',Zend_Measure_Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testVolumeNoLocale()
    {
        $value = new Zend_Measure_Volume('100',Zend_Measure_Volume::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Volume value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testVolumeValuePositive()
    {
        $value = new Zend_Measure_Volume('100',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Volume value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testVolumeValueNegative()
    {
        $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Volume value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testVolumeValueDecimal()
    {
        $value = new Zend_Measure_Volume('-100,200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Volume value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testVolumeValueDecimalSeperated()
    {
        $value = new Zend_Measure_Volume('-100.100,200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Volume Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testVolumeValueString()
    {
        $value = new Zend_Measure_Volume('-100.100,200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Volume Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testVolumeEquality()
    {
        $value = new Zend_Measure_Volume('-100.100,200',Zend_Measure_Volume::STANDARD,'de');
        $newvalue = new Zend_Measure_Volume('-100.100,200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Volume Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testVolumeNoEquality()
    {
        $value = new Zend_Measure_Volume('-100.100,200',Zend_Measure_Volume::STANDARD,'de');
        $newvalue = new Zend_Measure_Volume('-100,200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Volume Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testVolumeSetPositive()
    {
        $value = new Zend_Measure_Volume('100',Zend_Measure_Volume::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Volume value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testVolumeSetNegative()
    {
        $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Volume value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testVolumeSetDecimal()
    {
        $value = new Zend_Measure_Volume('-100,200',Zend_Measure_Volume::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Volume value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testVolumeSetDecimalSeperated()
    {
        $value = new Zend_Measure_Volume('-100.100,200',Zend_Measure_Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Volume Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testVolumeSetString()
    {
        $value = new Zend_Measure_Volume('-100.100,200',Zend_Measure_Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testVolumeSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Volume('100',Zend_Measure_Volume::STANDARD,'de');
            $value->setValue('-200.200,200','Volume::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Volume('100',Zend_Measure_Volume::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Volume::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
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
            $value = new Zend_Measure_Volume('100',Zend_Measure_Volume::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Volume::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testVolumeSetWithNoLocale()
    {
        $value = new Zend_Measure_Volume('100', Zend_Measure_Volume::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Volume::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Volume value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testVolumeSetType()
    {
        $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::STANDARD,'de');
        $value->setType(Zend_Measure_Volume::CORD);
        $this->assertEquals(Zend_Measure_Volume::CORD, $value->getType(), 'Zend_Measure_Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testVolumeSetComputedType1()
    {
        $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::STANDARD,'de');
        $value->setType(Zend_Measure_Volume::CUBIC_YARD);
        $this->assertEquals(Zend_Measure_Volume::CUBIC_YARD, $value->getType(), 'Zend_Measure_Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testVolumeSetComputedType2()
    {
        $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::CUBIC_YARD,'de');
        $value->setType(Zend_Measure_Volume::STANDARD);
        $this->assertEquals(Zend_Measure_Volume::STANDARD, $value->getType(), 'Zend_Measure_Volume type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testVolumeSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::STANDARD,'de');
            $value->setType('Volume::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testVolumeToString()
    {
        $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals('-100 m³', $value->toString(), 'Value -100 m³ expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testVolume_ToString()
    {
        $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::STANDARD,'de');
        $this->assertEquals('-100 m³', $value->__toString(), 'Value -100 m³ expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testVolumeConversionList()
    {
        $value = new Zend_Measure_Volume('-100',Zend_Measure_Volume::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
