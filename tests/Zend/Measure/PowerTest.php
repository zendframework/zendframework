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
class PowerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Power initialisation
     * expected instance
     */
    public function testPowerInit()
    {
        $value = new Measure\Power('100',Measure\Power::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Power,'Zend\Measure\Power Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testPowerUnknownType()
    {
        try {
            $value = new Measure\Power('100','Power::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Power('novalue',Measure\Power::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Power('100',Measure\Power::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testPowerNoLocale()
    {
        $value = new Measure\Power('100',Measure\Power::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Power value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testPowerValuePositive()
    {
        $value = new Measure\Power('100',Measure\Power::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Power value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testPowerValueNegative()
    {
        $value = new Measure\Power('-100',Measure\Power::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Power value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testPowerValueDecimal()
    {
        $value = new Measure\Power('-100,200',Measure\Power::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Power value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testPowerValueDecimalSeperated()
    {
        $value = new Measure\Power('-100.100,200',Measure\Power::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Power Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testPowerValueString()
    {
        $value = new Measure\Power('-100.100,200',Measure\Power::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Power Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testPowerEquality()
    {
        $value = new Measure\Power('-100.100,200',Measure\Power::STANDARD,'de');
        $newvalue = new Measure\Power('-100.100,200',Measure\Power::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Power Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testPowerNoEquality()
    {
        $value = new Measure\Power('-100.100,200',Measure\Power::STANDARD,'de');
        $newvalue = new Measure\Power('-100,200',Measure\Power::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Power Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testPowerSetPositive()
    {
        $value = new Measure\Power('100',Measure\Power::STANDARD,'de');
        $value->setValue('200',Measure\Power::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Power value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testPowerSetNegative()
    {
        $value = new Measure\Power('-100',Measure\Power::STANDARD,'de');
        $value->setValue('-200',Measure\Power::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Power value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testPowerSetDecimal()
    {
        $value = new Measure\Power('-100,200',Measure\Power::STANDARD,'de');
        $value->setValue('-200,200',Measure\Power::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Power value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testPowerSetDecimalSeperated()
    {
        $value = new Measure\Power('-100.100,200',Measure\Power::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Power::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Power Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testPowerSetString()
    {
        $value = new Measure\Power('-100.100,200',Measure\Power::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Power::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Power Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testPowerSetUnknownType()
    {
        try {
            $value = new Measure\Power('100',Measure\Power::STANDARD,'de');
            $value->setValue('-200.200,200','Power::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Power('100',Measure\Power::STANDARD,'de');
            $value->setValue('novalue',Measure\Power::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
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
            $value = new Measure\Power('100',Measure\Power::STANDARD,'de');
            $value->setValue('200',Measure\Power::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testPowerSetWithNoLocale()
    {
        $value = new Measure\Power('100', Measure\Power::STANDARD, 'de');
        $value->setValue('200', Measure\Power::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Power value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testPowerSetType()
    {
        $value = new Measure\Power('-100',Measure\Power::STANDARD,'de');
        $value->setType(Measure\Power::CALORIE_PER_HOUR);
        $this->assertEquals(Measure\Power::CALORIE_PER_HOUR, $value->getType(), 'Zend\Measure\Power type expected');
    }


    /**
     * test setting type2
     * expected new type
     */
    public function testPowerSetType2()
    {
        $value = new Measure\Power('-100',Measure\Power::CALORIE_PER_HOUR,'de');
        $value->setType(Measure\Power::STANDARD);
        $this->assertEquals(Measure\Power::STANDARD, $value->getType(), 'Zend\Measure\Power type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testPowerSetComputedType1()
    {
        $value = new Measure\Power('-100',Measure\Power::CALORIE_PER_HOUR,'de');
        $value->setType(Measure\Power::JOULE_PER_HOUR);
        $this->assertEquals(Measure\Power::JOULE_PER_HOUR, $value->getType(), 'Zend\Measure\Power type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testPowerSetComputedType2()
    {
        $value = new Measure\Power('-100',Measure\Power::JOULE_PER_HOUR,'de');
        $value->setType(Measure\Power::CALORIE_PER_HOUR);
        $this->assertEquals(Measure\Power::CALORIE_PER_HOUR, $value->getType(), 'Zend\Measure\Power type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testPowerSetTypeFailed()
    {
        try {
            $value = new Measure\Power('-100',Measure\Power::STANDARD,'de');
            $value->setType('Power::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testPowerToString()
    {
        $value = new Measure\Power('-100',Measure\Power::STANDARD,'de');
        $this->assertEquals('-100 W', $value->toString(), 'Value -100 W expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testPower_ToString()
    {
        $value = new Measure\Power('-100',Measure\Power::STANDARD,'de');
        $this->assertEquals('-100 W', $value->__toString(), 'Value -100 W expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testPowerConversionList()
    {
        $value = new Measure\Power('-100',Measure\Power::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
