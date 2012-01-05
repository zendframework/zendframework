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
class MoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for mole initialisation
     * expected instance
     */
    public function testMoleInit()
    {
        $value = new Flow\Mole('100',Flow\Mole::STANDARD,'de');
        $this->assertTrue($value instanceof Flow\Mole,'Zend\Measure\Flow\Mole Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_MoleUnknownType()
    {
        try {
            $value = new Flow\Mole('100','Flow_Mole::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_MoleUnknownValue()
    {
        try {
            $value = new Flow\Mole('novalue',Flow\Mole::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testFlow_MoleUnknownLocale()
    {
        try {
            $value = new Flow\Mole('100',Flow\Mole::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testFlow_MoleNoLocale()
    {
        $value = new Flow\Mole('100',Flow\Mole::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Flow\Mole value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testFlow_MoleValuePositive()
    {
        $value = new Flow\Mole('100',Flow\Mole::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Flow\Mole value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testFlow_MoleValueNegative()
    {
        $value = new Flow\Mole('-100',Flow\Mole::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Flow\Mole value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testFlow_MoleValueDecimal()
    {
        $value = new Flow\Mole('-100,200',Flow\Mole::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Flow\Mole value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testFlow_MoleValueDecimalSeperated()
    {
        $value = new Flow\Mole('-100.100,200',Flow\Mole::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Flow\Mole Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testFlow_MoleValueString()
    {
        $value = new Flow\Mole('-100.100,200',Flow\Mole::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Flow\Mole Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testFlow_MoleEquality()
    {
        $value = new Flow\Mole('-100.100,200',Flow\Mole::STANDARD,'de');
        $newvalue = new Flow\Mole('-100.100,200',Flow\Mole::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Flow\Mole Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testFlow_MoleNoEquality()
    {
        $value = new Flow\Mole('-100.100,200',Flow\Mole::STANDARD,'de');
        $newvalue = new Flow\Mole('-100,200',Flow\Mole::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Flow\Mole Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testFlow_MoleSetPositive()
    {
        $value = new Flow\Mole('100',Flow\Mole::STANDARD,'de');
        $value->setValue('200',Flow\Mole::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Flow\Mole value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testFlow_MoleSetNegative()
    {
        $value = new Flow\Mole('-100',Flow\Mole::STANDARD,'de');
        $value->setValue('-200',Flow\Mole::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Flow\Mole value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testFlow_MoleSetDecimal()
    {
        $value = new Flow\Mole('-100,200',Flow\Mole::STANDARD,'de');
        $value->setValue('-200,200',Flow\Mole::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Flow\Mole value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testFlow_MoleSetDecimalSeperated()
    {
        $value = new Flow\Mole('-100.100,200',Flow\Mole::STANDARD,'de');
        $value->setValue('-200.200,200',Flow\Mole::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Flow\Mole Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testFlow_MoleSetString()
    {
        $value = new Flow\Mole('-100.100,200',Flow\Mole::STANDARD,'de');
        $value->setValue('-200.200,200',Flow\Mole::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Flow\Mole Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_MoleSetUnknownType()
    {
        try {
            $value = new Flow\Mole('100',Flow\Mole::STANDARD,'de');
            $value->setValue('-200.200,200','Flow_Mole::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_MoleSetUnknownValue()
    {
        try {
            $value = new Flow\Mole('100',Flow\Mole::STANDARD,'de');
            $value->setValue('novalue',Flow\Mole::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_MoleSetUnknownLocale()
    {
        try {
            $value = new Flow\Mole('100',Flow\Mole::STANDARD,'de');
            $value->setValue('200',Flow\Mole::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_MoleSetWithNoLocale()
    {
        $value = new Flow\Mole('100', Flow\Mole::STANDARD, 'de');
        $value->setValue('200', Flow\Mole::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Flow\Mole value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testFlow_MoleSetType()
    {
        $value = new Flow\Mole('-100',Flow\Mole::STANDARD,'de');
        $value->setType(Flow\Mole::MILLIMOLE_PER_DAY);
        $this->assertEquals(Flow\Mole::MILLIMOLE_PER_DAY, $value->getType(), 'Zend\Measure\Flow\Mole type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_MoleSetComputedType1()
    {
        $value = new Flow\Mole('-100',Flow\Mole::STANDARD,'de');
        $value->setType(Flow\Mole::MILLIMOLE_PER_DAY);
        $this->assertEquals(Flow\Mole::MILLIMOLE_PER_DAY, $value->getType(), 'Zend\Measure\Flow\Mole type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_MoleSetComputedType2()
    {
        $value = new Flow\Mole('-100',Flow\Mole::MILLIMOLE_PER_DAY,'de');
        $value->setType(Flow\Mole::STANDARD);
        $this->assertEquals(Flow\Mole::STANDARD, $value->getType(), 'Zend\Measure\Flow\Mole type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testFlow_MoleSetTypeFailed()
    {
        try {
            $value = new Flow\Mole('-100',Flow\Mole::STANDARD,'de');
            $value->setType('Flow_Mole::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testFlow_MoleToString()
    {
        $value = new Flow\Mole('-100',Flow\Mole::STANDARD,'de');
        $this->assertEquals('-100 mol/s', $value->toString(), 'Value -100 mol/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testFlow_Mole_ToString()
    {
        $value = new Flow\Mole('-100',Flow\Mole::STANDARD,'de');
        $this->assertEquals('-100 mol/s', $value->__toString(), 'Value -100 mol/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testFlow_MoleConversionList()
    {
        $value = new Flow\Mole('-100',Flow\Mole::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
