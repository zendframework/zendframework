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
class EnergyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Energy initialisation
     * expected instance
     */
    public function testEnergyInit()
    {
        $value = new Measure\Energy('100',Measure\Energy::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Energy,'Zend\Measure\Energy Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testEnergyUnknownType()
    {
        try {
            $value = new Measure\Energy('100','Energy::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testEnergyUnknownValue()
    {
        try {
            $value = new Measure\Energy('novalue',Measure\Energy::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testEnergyUnknownLocale()
    {
        try {
            $value = new Measure\Energy('100',Measure\Energy::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testEnergyNoLocale()
    {
        $value = new Measure\Energy('100',Measure\Energy::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Energy value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testEnergyValuePositive()
    {
        $value = new Measure\Energy('100',Measure\Energy::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Energy value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testEnergyValueNegative()
    {
        $value = new Measure\Energy('-100',Measure\Energy::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Energy value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testEnergyValueDecimal()
    {
        $value = new Measure\Energy('-100,200',Measure\Energy::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Energy value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testEnergyValueDecimalSeperated()
    {
        $value = new Measure\Energy('-100.100,200',Measure\Energy::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Energy Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testEnergyValueString()
    {
        $value = new Measure\Energy('-100.100,200',Measure\Energy::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Energy Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testEnergyEquality()
    {
        $value = new Measure\Energy('-100.100,200',Measure\Energy::STANDARD,'de');
        $newvalue = new Measure\Energy('-100.100,200',Measure\Energy::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Energy Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testEnergyNoEquality()
    {
        $value = new Measure\Energy('-100.100,200',Measure\Energy::STANDARD,'de');
        $newvalue = new Measure\Energy('-100,200',Measure\Energy::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Energy Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testEnergySetPositive()
    {
        $value = new Measure\Energy('100',Measure\Energy::STANDARD,'de');
        $value->setValue('200',Measure\Energy::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Energy value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testEnergySetNegative()
    {
        $value = new Measure\Energy('-100',Measure\Energy::STANDARD,'de');
        $value->setValue('-200',Measure\Energy::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Energy value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testEnergySetDecimal()
    {
        $value = new Measure\Energy('-100,200',Measure\Energy::STANDARD,'de');
        $value->setValue('-200,200',Measure\Energy::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Energy value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testEnergySetDecimalSeperated()
    {
        $value = new Measure\Energy('-100.100,200',Measure\Energy::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Energy::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Energy Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testEnergySetString()
    {
        $value = new Measure\Energy('-100.100,200',Measure\Energy::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Energy::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Energy Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testEnergySetUnknownType()
    {
        try {
            $value = new Measure\Energy('100',Measure\Energy::STANDARD,'de');
            $value->setValue('-200.200,200','Energy::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testEnergySetUnknownValue()
    {
        try {
            $value = new Measure\Energy('100',Measure\Energy::STANDARD,'de');
            $value->setValue('novalue',Measure\Energy::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testEnergySetUnknownLocale()
    {
        try {
            $value = new Measure\Energy('100',Measure\Energy::STANDARD,'de');
            $value->setValue('200',Measure\Energy::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testEnergySetWithNoLocale()
    {
        $value = new Measure\Energy('100', Measure\Energy::STANDARD, 'de');
        $value->setValue('200', Measure\Energy::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Energy value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testEnergySetType()
    {
        $value = new Measure\Energy('-100',Measure\Energy::STANDARD,'de');
        $value->setType(Measure\Energy::ERG);
        $this->assertEquals(Measure\Energy::ERG, $value->getType(), 'Zend\Measure\Energy type expected');
    }


    /**
     * test setting computed type
     * expected new type
     * @group foo
     */
    public function testEnergySetComputedType1()
    {
        $value = new Measure\Energy('-100',Measure\Energy::ERG,'de');
        $value->setType(Measure\Energy::KILOTON);
        $this->assertEquals(Measure\Energy::KILOTON, $value->getType(), 'Zend\Measure\Energy type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testEnergySetComputedType2()
    {
        $value = new Measure\Energy('-100',Measure\Energy::KILOTON,'de');
        $value->setType(Measure\Energy::ERG);
        $this->assertEquals(Measure\Energy::ERG, $value->getType(), 'Zend\Measure\Energy type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testEnergySetTypeFailed()
    {
        try {
            $value = new Measure\Energy('-100',Measure\Energy::STANDARD,'de');
            $value->setType('Energy::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testEnergyToString()
    {
        $value = new Measure\Energy('-100',Measure\Energy::STANDARD,'de');
        $this->assertEquals('-100 J', $value->toString(), 'Value -100 J expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testEnergy_ToString()
    {
        $value = new Measure\Energy('-100',Measure\Energy::STANDARD,'de');
        $this->assertEquals('-100 J', $value->__toString(), 'Value -100 J expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testEnergyConversionList()
    {
        $value = new Measure\Energy('-100',Measure\Energy::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
