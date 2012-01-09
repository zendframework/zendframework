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
class FrequencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Frequency initialisation
     * expected instance
     */
    public function testFrequencyInit()
    {
        $value = new Measure\Frequency('100',Measure\Frequency::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Frequency,'Zend\Measure\Frequency Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFrequencyUnknownType()
    {
        try {
            $value = new Measure\Frequency('100','Frequency::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFrequencyUnknownValue()
    {
        try {
            $value = new Measure\Frequency('novalue',Measure\Frequency::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testFrequencyUnknownLocale()
    {
        try {
            $value = new Measure\Frequency('100',Measure\Frequency::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testFrequencyNoLocale()
    {
        $value = new Measure\Frequency('100',Measure\Frequency::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Frequency value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testFrequencyValuePositive()
    {
        $value = new Measure\Frequency('100',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Frequency value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testFrequencyValueNegative()
    {
        $value = new Measure\Frequency('-100',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Frequency value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testFrequencyValueDecimal()
    {
        $value = new Measure\Frequency('-100,200',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Frequency value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testFrequencyValueDecimalSeperated()
    {
        $value = new Measure\Frequency('-100.100,200',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Frequency Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testFrequencyValueString()
    {
        $value = new Measure\Frequency('-100.100,200',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Frequency Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testFrequencyEquality()
    {
        $value = new Measure\Frequency('-100.100,200',Measure\Frequency::STANDARD,'de');
        $newvalue = new Measure\Frequency('-100.100,200',Measure\Frequency::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Frequency Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testFrequencyNoEquality()
    {
        $value = new Measure\Frequency('-100.100,200',Measure\Frequency::STANDARD,'de');
        $newvalue = new Measure\Frequency('-100,200',Measure\Frequency::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Frequency Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testFrequencySetPositive()
    {
        $value = new Measure\Frequency('100',Measure\Frequency::STANDARD,'de');
        $value->setValue('200',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Frequency value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testFrequencySetNegative()
    {
        $value = new Measure\Frequency('-100',Measure\Frequency::STANDARD,'de');
        $value->setValue('-200',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Frequency value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testFrequencySetDecimal()
    {
        $value = new Measure\Frequency('-100,200',Measure\Frequency::STANDARD,'de');
        $value->setValue('-200,200',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Frequency value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testFrequencySetDecimalSeperated()
    {
        $value = new Measure\Frequency('-100.100,200',Measure\Frequency::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Frequency Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testFrequencySetString()
    {
        $value = new Measure\Frequency('-100.100,200',Measure\Frequency::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Frequency::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Frequency Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFrequencySetUnknownType()
    {
        try {
            $value = new Measure\Frequency('100',Measure\Frequency::STANDARD,'de');
            $value->setValue('-200.200,200','Frequency::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFrequencySetUnknownValue()
    {
        try {
            $value = new Measure\Frequency('100',Measure\Frequency::STANDARD,'de');
            $value->setValue('novalue',Measure\Frequency::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFrequencySetUnknownLocale()
    {
        try {
            $value = new Measure\Frequency('100',Measure\Frequency::STANDARD,'de');
            $value->setValue('200',Measure\Frequency::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFrequencySetWithNoLocale()
    {
        $value = new Measure\Frequency('100', Measure\Frequency::STANDARD, 'de');
        $value->setValue('200', Measure\Frequency::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Frequency value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testFrequencySetType()
    {
        $value = new Measure\Frequency('-100',Measure\Frequency::STANDARD,'de');
        $value->setType(Measure\Frequency::KILOHERTZ);
        $this->assertEquals(Measure\Frequency::KILOHERTZ, $value->getType(), 'Zend\Measure\Frequency type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFrequencySetComputedType1()
    {
        $value = new Measure\Frequency('-100',Measure\Frequency::RADIAN_PER_HOUR,'de');
        $value->setType(Measure\Frequency::RPM);
        $this->assertEquals(Measure\Frequency::RPM, $value->getType(), 'Zend\Measure\Frequency type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFrequencySetComputedType2()
    {
        $value = new Measure\Frequency('-100',Measure\Frequency::RPM,'de');
        $value->setType(Measure\Frequency::RADIAN_PER_HOUR);
        $this->assertEquals(Measure\Frequency::RADIAN_PER_HOUR, $value->getType(), 'Zend\Measure\Frequency type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testFrequencySetTypeFailed()
    {
        try {
            $value = new Measure\Frequency('-100',Measure\Frequency::STANDARD,'de');
            $value->setType('Frequency::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testFrequencyToString()
    {
        $value = new Measure\Frequency('-100',Measure\Frequency::STANDARD,'de');
        $this->assertEquals('-100 Hz', $value->toString(), 'Value -100 Hz expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testFrequency_ToString()
    {
        $value = new Measure\Frequency('-100',Measure\Frequency::STANDARD,'de');
        $this->assertEquals('-100 Hz', $value->__toString(), 'Value -100 Hz expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testFrequencyConversionList()
    {
        $value = new Measure\Frequency('-100',Measure\Frequency::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
