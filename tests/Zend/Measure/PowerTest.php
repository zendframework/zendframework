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
 * Zend_Measure_Power
 */
require_once 'Zend/Measure/Power.php';

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
class Zend_Measure_PowerTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Power initialisation
     * expected instance
     */
    public function testPowerInit()
    {
        $value = new Zend_Measure_Power('100',Zend_Measure_Power::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Power,'Zend_Measure_Power Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testPowerUnknownType()
    {
        try {
            $value = new Zend_Measure_Power('100','Power::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testPowerUnknownValue()
    {
        try {
            $value = new Zend_Measure_Power('novalue',Zend_Measure_Power::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testPowerUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Power('100',Zend_Measure_Power::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testPowerNoLocale()
    {
        $value = new Zend_Measure_Power('100',Zend_Measure_Power::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Power value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testPowerValuePositive()
    {
        $value = new Zend_Measure_Power('100',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Power value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testPowerValueNegative()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Power value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testPowerValueDecimal()
    {
        $value = new Zend_Measure_Power('-100,200',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Power value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testPowerValueDecimalSeperated()
    {
        $value = new Zend_Measure_Power('-100.100,200',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Power Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testPowerValueString()
    {
        $value = new Zend_Measure_Power('-100.100,200',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Power Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testPowerEquality()
    {
        $value = new Zend_Measure_Power('-100.100,200',Zend_Measure_Power::STANDARD,'de');
        $newvalue = new Zend_Measure_Power('-100.100,200',Zend_Measure_Power::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Power Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testPowerNoEquality()
    {
        $value = new Zend_Measure_Power('-100.100,200',Zend_Measure_Power::STANDARD,'de');
        $newvalue = new Zend_Measure_Power('-100,200',Zend_Measure_Power::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Power Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testPowerSetPositive()
    {
        $value = new Zend_Measure_Power('100',Zend_Measure_Power::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Power value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testPowerSetNegative()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Power value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testPowerSetDecimal()
    {
        $value = new Zend_Measure_Power('-100,200',Zend_Measure_Power::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Power value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testPowerSetDecimalSeperated()
    {
        $value = new Zend_Measure_Power('-100.100,200',Zend_Measure_Power::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Power Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testPowerSetString()
    {
        $value = new Zend_Measure_Power('-100.100,200',Zend_Measure_Power::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Power Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testPowerSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Power('100',Zend_Measure_Power::STANDARD,'de');
            $value->setValue('-200.200,200','Power::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testPowerSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Power('100',Zend_Measure_Power::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Power::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testPowerSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Power('100',Zend_Measure_Power::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Power::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testPowerSetWithNoLocale()
    {
        $value = new Zend_Measure_Power('100', Zend_Measure_Power::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Power::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Power value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testPowerSetType()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::STANDARD,'de');
        $value->setType(Zend_Measure_Power::CALORIE_PER_HOUR);
        $this->assertEquals(Zend_Measure_Power::CALORIE_PER_HOUR, $value->getType(), 'Zend_Measure_Power type expected');
    }


    /**
     * test setting type2
     * expected new type
     */
    public function testPowerSetType2()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::CALORIE_PER_HOUR,'de');
        $value->setType(Zend_Measure_Power::STANDARD);
        $this->assertEquals(Zend_Measure_Power::STANDARD, $value->getType(), 'Zend_Measure_Power type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testPowerSetComputedType1()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::CALORIE_PER_HOUR,'de');
        $value->setType(Zend_Measure_Power::JOULE_PER_HOUR);
        $this->assertEquals(Zend_Measure_Power::JOULE_PER_HOUR, $value->getType(), 'Zend_Measure_Power type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testPowerSetComputedType2()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::JOULE_PER_HOUR,'de');
        $value->setType(Zend_Measure_Power::CALORIE_PER_HOUR);
        $this->assertEquals(Zend_Measure_Power::CALORIE_PER_HOUR, $value->getType(), 'Zend_Measure_Power type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testPowerSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Power('-100',Zend_Measure_Power::STANDARD,'de');
            $value->setType('Power::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testPowerToString()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals('-100 W', $value->toString(), 'Value -100 W expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testPower_ToString()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::STANDARD,'de');
        $this->assertEquals('-100 W', $value->__toString(), 'Value -100 W expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testPowerConversionList()
    {
        $value = new Zend_Measure_Power('-100',Zend_Measure_Power::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
