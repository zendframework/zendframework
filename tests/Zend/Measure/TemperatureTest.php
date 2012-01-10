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
use Zend\Registry;
use Zend\Locale\Data\Cldr;

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class TemperatureTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (Registry::isRegistered('Zend_Locale')) {
            $registry = Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        Cldr::removeCache();

        $this->_locale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, 'de');
    }

    public function tearDown()
    {
        if (is_string($this->_locale) && strpos($this->_locale, ';')) {
            $locales = array();
            foreach (explode(';', $this->_locale) as $l) {
                $tmp = explode('=', $l);
                $locales[$tmp[0]] = $tmp[1];
            }
            setlocale(LC_ALL, $locales);
            return;
        }
        setlocale(LC_ALL, $this->_locale);
    }

    /**
     * test for Temperature initialisation
     * expected instance
     */
    public function testTemperatureInit()
    {
        $value = new Measure\Temperature('100',Measure\Temperature::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Temperature,'Zend\Measure\Temperature Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testTemperatureUnknownType()
    {
        try {
            $value = new Measure\Temperature('100','Temperature::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testTemperatureUnknownValue()
    {
        try {
            $value = new Measure\Temperature('novalue',Measure\Temperature::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testTemperatureUnknownLocale()
    {
        try {
            $value = new Measure\Temperature('100',Measure\Temperature::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testTemperatureNoLocale()
    {
        $value = new Measure\Temperature('100',Measure\Temperature::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Temperature value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testTemperatureValuePositive()
    {
        $value = new Measure\Temperature('100',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Temperature value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testTemperatureValueNegative()
    {
        $value = new Measure\Temperature('-100',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Temperature value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testTemperatureValueDecimal()
    {
        $value = new Measure\Temperature('-100,200',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Temperature value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testTemperatureValueDecimalSeperated()
    {
        $value = new Measure\Temperature('-100.100,200',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Temperature Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testTemperatureValueString()
    {
        $value = new Measure\Temperature('-100.100,200',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Temperature Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testTemperatureEquality()
    {
        $value = new Measure\Temperature('-100.100,200',Measure\Temperature::STANDARD,'de');
        $newvalue = new Measure\Temperature('-100.100,200',Measure\Temperature::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Temperature Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testTemperatureNoEquality()
    {
        $value = new Measure\Temperature('-100.100,200',Measure\Temperature::STANDARD,'de');
        $newvalue = new Measure\Temperature('-100,200',Measure\Temperature::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Temperature Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testTemperatureSetPositive()
    {
        $value = new Measure\Temperature('100',Measure\Temperature::STANDARD,'de');
        $value->setValue('200',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Temperature value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testTemperatureSetNegative()
    {
        $value = new Measure\Temperature('-100',Measure\Temperature::STANDARD,'de');
        $value->setValue('-200',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Temperature value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testTemperatureSetDecimal()
    {
        $value = new Measure\Temperature('-100,200',Measure\Temperature::STANDARD,'de');
        $value->setValue('-200,200',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Temperature value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testTemperatureSetDecimalSeperated()
    {
        $value = new Measure\Temperature('-100.100,200',Measure\Temperature::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Temperature Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testTemperatureSetString()
    {
        $value = new Measure\Temperature('-100.100,200',Measure\Temperature::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Temperature::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Temperature Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testTemperatureSetUnknownType()
    {
        try {
            $value = new Measure\Temperature('100',Measure\Temperature::STANDARD,'de');
            $value->setValue('-200.200,200','Temperature::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testTemperatureSetUnknownValue()
    {
        try {
            $value = new Measure\Temperature('100',Measure\Temperature::STANDARD,'de');
            $value->setValue('novalue',Measure\Temperature::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testTemperatureSetUnknownLocale()
    {
        try {
            $value = new Measure\Temperature('100',Measure\Temperature::STANDARD,'de');
            $value->setValue('200',Measure\Temperature::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testTemperatureSetWithNoLocale()
    {
        $value = new Measure\Temperature('100', Measure\Temperature::STANDARD, 'de');
        $value->setValue('200', Measure\Temperature::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Temperature value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testTemperatureSetType()
    {
        $value = new Measure\Temperature('-100',Measure\Temperature::STANDARD,'de');
        $value->setType(Measure\Temperature::KELVIN);
        $this->assertEquals(Measure\Temperature::KELVIN, $value->getType(), 'Zend\Measure\Temperature type expected');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testTemperatureSetType1()
    {
        $value = new Measure\Temperature('-100',Measure\Temperature::FAHRENHEIT,'de');
        $value->setType(Measure\Temperature::REAUMUR);
        $this->assertEquals(Measure\Temperature::REAUMUR, $value->getType(), 'Zend\Measure\Temperature type expected');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testTemperatureSetType2()
    {
        $value = new Measure\Temperature('-100',Measure\Temperature::REAUMUR,'de');
        $value->setType(Measure\Temperature::FAHRENHEIT);
        $this->assertEquals(Measure\Temperature::FAHRENHEIT, $value->getType(), 'Zend\Measure\Temperature type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testTemperatureSetTypeFailed()
    {
        try {
            $value = new Measure\Temperature('-100',Measure\Temperature::STANDARD,'de');
            $value->setType('Temperature::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testTemperatureToString()
    {
        $value = new Measure\Temperature('-100',Measure\Temperature::STANDARD,'de');
        $this->assertEquals('-100 °K', $value->toString(), 'Value -100 °K expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testTemperature_ToString()
    {
        $value = new Measure\Temperature('-100',Measure\Temperature::STANDARD,'de');
        $this->assertEquals('-100 °K', $value->__toString(), 'Value -100 °K expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testTemperatureConversionList()
    {
        $value = new Measure\Temperature('-100',Measure\Temperature::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }


    /**
     * test Detail conversions which often fail
     *
     */
    public function testDetailConversion()
    {
        $unit= new Measure\Temperature(100, Measure\Temperature::KELVIN, 'de');
        $this->assertSame('-280 °F', $unit->convertTo(Measure\Temperature::FAHRENHEIT, 0));

        $unit= new Measure\Temperature(100, Measure\Temperature::FAHRENHEIT, 'de');
        $this->assertSame('311 °K', $unit->convertTo(Measure\Temperature::KELVIN, 0));
    }
}
