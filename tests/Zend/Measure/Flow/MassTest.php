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
class MassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Mass initialisation
     * expected instance
     */
    public function testMassInit()
    {
        $value = new Flow\Mass('100',Flow\Mass::STANDARD,'de');
        $this->assertTrue($value instanceof Flow\Mass,'Zend\Measure\Flow\Mass Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_MassUnknownType()
    {
        try {
            $value = new Flow\Mass('100','Flow_Mass::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_MassUnknownValue()
    {
        try {
            $value = new Flow\Mass('novalue',Flow\Mass::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testFlow_MassUnknownLocale()
    {
        try {
            $value = new Flow\Mass('100',Flow\Mass::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testFlow_MassNoLocale()
    {
        $value = new Flow\Mass('100',Flow\Mass::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Flow\Mass value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testFlow_MassValuePositive()
    {
        $value = new Flow\Mass('100',Flow\Mass::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Flow\Mass value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testFlow_MassValueNegative()
    {
        $value = new Flow\Mass('-100',Flow\Mass::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Flow\Mass value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testFlow_MassValueDecimal()
    {
        $value = new Flow\Mass('-100,200',Flow\Mass::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Flow\Mass value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testFlow_MassValueDecimalSeperated()
    {
        $value = new Flow\Mass('-100.100,200',Flow\Mass::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Flow\Mass Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testFlow_MassValueString()
    {
        $value = new Flow\Mass('-100.100,200',Flow\Mass::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Flow\Mass Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testFlow_MassEquality()
    {
        $value = new Flow\Mass('-100.100,200',Flow\Mass::STANDARD,'de');
        $newvalue = new Flow\Mass('-100.100,200',Flow\Mass::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Flow\Mass Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testFlow_MassNoEquality()
    {
        $value = new Flow\Mass('-100.100,200',Flow\Mass::STANDARD,'de');
        $newvalue = new Flow\Mass('-100,200',Flow\Mass::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Flow\Mass Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testFlow_MassSetPositive()
    {
        $value = new Flow\Mass('100',Flow\Mass::STANDARD,'de');
        $value->setValue('200',Flow\Mass::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Flow\Mass value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testFlow_MassSetNegative()
    {
        $value = new Flow\Mass('-100',Flow\Mass::STANDARD,'de');
        $value->setValue('-200',Flow\Mass::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Flow\Mass value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testFlow_MassSetDecimal()
    {
        $value = new Flow\Mass('-100,200',Flow\Mass::STANDARD,'de');
        $value->setValue('-200,200',Flow\Mass::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Flow\Mass value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testFlow_MassSetDecimalSeperated()
    {
        $value = new Flow\Mass('-100.100,200',Flow\Mass::STANDARD,'de');
        $value->setValue('-200.200,200',Flow\Mass::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Flow\Mass Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testFlow_MassSetString()
    {
        $value = new Flow\Mass('-100.100,200',Flow\Mass::STANDARD,'de');
        $value->setValue('-200.200,200',Flow\Mass::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Flow\Mass Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_MassSetUnknownType()
    {
        try {
            $value = new Flow\Mass('100',Flow\Mass::STANDARD,'de');
            $value->setValue('-200.200,200','Flow_Mass::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_MassSetUnknownValue()
    {
        try {
            $value = new Flow\Mass('100',Flow\Mass::STANDARD,'de');
            $value->setValue('novalue',Flow\Mass::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_MassSetUnknownLocale()
    {
        try {
            $value = new Flow\Mass('100',Flow\Mass::STANDARD,'de');
            $value->setValue('200',Flow\Mass::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_MassSetWithNoLocale()
    {
        $value = new Flow\Mass('100', Flow\Mass::STANDARD, 'de');
        $value->setValue('200', Flow\Mass::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Flow\Mass value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testFlow_MassSetType()
    {
        $value = new Flow\Mass('-100',Flow\Mass::STANDARD,'de');
        $value->setType(Flow\Mass::GRAM_PER_DAY);
        $this->assertEquals(Flow\Mass::GRAM_PER_DAY, $value->getType(), 'Zend\Measure\Flow\Mass type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_MassSetComputedType1()
    {
        $value = new Flow\Mass('-100',Flow\Mass::STANDARD,'de');
        $value->setType(Flow\Mass::GRAM_PER_DAY);
        $this->assertEquals(Flow\Mass::GRAM_PER_DAY, $value->getType(), 'Zend\Measure\Flow\Mass type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_MassSetComputedType2()
    {
        $value = new Flow\Mass('-100',Flow\Mass::GRAM_PER_DAY,'de');
        $value->setType(Flow\Mass::STANDARD);
        $this->assertEquals(Flow\Mass::STANDARD, $value->getType(), 'Zend\Measure\Flow\Mass type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testFlow_MassSetTypeFailed()
    {
        try {
            $value = new Flow\Mass('-100',Flow\Mass::STANDARD,'de');
            $value->setType('Flow_Mass::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testFlow_MassToString()
    {
        $value = new Flow\Mass('-100',Flow\Mass::STANDARD,'de');
        $this->assertEquals('-100 kg/s', $value->toString(), 'Value -100 kg/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testFlow_Mass_ToString()
    {
        $value = new Flow\Mass('-100',Flow\Mass::STANDARD,'de');
        $this->assertEquals('-100 kg/s', $value->__toString(), 'Value -100 kg/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testFlow_MassConversionList()
    {
        $value = new Flow\Mass('-100',Flow\Mass::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
