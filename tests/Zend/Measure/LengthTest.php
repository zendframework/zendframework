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
class LengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Length initialisation
     * expected instance
     */
    public function testLengthInit()
    {
        $value = new Measure\Length('100',Measure\Length::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Length,'Zend\Measure\Length Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testLengthUnknownType()
    {
        try {
            $value = new Measure\Length('100','Length::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (\Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testLengthUnknownValue()
    {
        try {
            $value = new Measure\Length('novalue',Measure\Length::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (\Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testLengthUnknownLocale()
    {
        try {
            $value = new Measure\Length('100',Measure\Length::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (\Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testLengthNoLocale()
    {
        $value = new Measure\Length('100',Measure\Length::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Length value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testLengthValuePositive()
    {
        $value = new Measure\Length('100',Measure\Length::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Length value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testLengthValueNegative()
    {
        $value = new Measure\Length('-100',Measure\Length::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Length value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testLengthValueDecimal()
    {
        $value = new Measure\Length('-100,200',Measure\Length::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Length value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testLengthValueDecimalSeperated()
    {
        $value = new Measure\Length('-100.100,200',Measure\Length::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Length Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testLengthValueString()
    {
        $value = new Measure\Length('-100.100,200',Measure\Length::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Length Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testLengthEquality()
    {
        $value = new Measure\Length('-100.100,200',Measure\Length::STANDARD,'de');
        $newvalue = new Measure\Length('-100.100,200',Measure\Length::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Length Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testLengthNoEquality()
    {
        $value = new Measure\Length('-100.100,200',Measure\Length::STANDARD,'de');
        $newvalue = new Measure\Length('-100,200',Measure\Length::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Length Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testLengthSetPositive()
    {
        $value = new Measure\Length('100',Measure\Length::STANDARD,'de');
        $value->setValue('200',Measure\Length::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Length value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testLengthSetNegative()
    {
        $value = new Measure\Length('-100',Measure\Length::STANDARD,'de');
        $value->setValue('-200',Measure\Length::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Length value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testLengthSetDecimal()
    {
        $value = new Measure\Length('-100,200',Measure\Length::STANDARD,'de');
        $value->setValue('-200,200',Measure\Length::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Length value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testLengthSetDecimalSeperated()
    {
        $value = new Measure\Length('-100.100,200',Measure\Length::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Length::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Length Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testLengthSetString()
    {
        $value = new Measure\Length('-100.100,200',Measure\Length::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Length::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Length Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testLengthSetUnknownType()
    {
        try {
            $value = new Measure\Length('100',Measure\Length::STANDARD,'de');
            $value->setValue('-200.200,200','Length::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (\Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testLengthSetUnknownValue()
    {
        try {
            $value = new Measure\Length('100',Measure\Length::STANDARD,'de');
            $value->setValue('novalue',Measure\Length::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (\Exception $e) {
            return; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testLengthSetUnknownLocale()
    {
        try {
            $value = new Measure\Length('100',Measure\Length::STANDARD,'de');
            $value->setValue('200',Measure\Length::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (\Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testLengthSetWithNoLocale()
    {
        $value = new Measure\Length('100', Measure\Length::STANDARD, 'de');
        $value->setValue('200', Measure\Length::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Length value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testLengthSetType()
    {
        $value = new Measure\Length('-100',Measure\Length::STANDARD,'de');
        $value->setType(Measure\Length::MILE);
        $this->assertEquals(Measure\Length::MILE, $value->getType(), 'Zend\Measure\Length type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testLengthSetComputedType1()
    {
        $value = new Measure\Length('-100',Measure\Length::STANDARD,'de');
        $value->setType(Measure\Length::LINK);
        $this->assertEquals(Measure\Length::LINK, $value->getType(), 'Zend\Measure\Length type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testLengthSetComputedType2()
    {
        $value = new Measure\Length('-100',Measure\Length::LINK,'de');
        $value->setType(Measure\Length::KEN);
        $this->assertEquals(Measure\Length::KEN, $value->getType(), 'Zend\Measure\Length type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testLengthSetTypeFailed()
    {
        try {
            $value = new Measure\Length('-100',Measure\Length::STANDARD,'de');
            $value->setType('Length::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testLengthToString()
    {
        $value = new Measure\Length('-100',Measure\Length::STANDARD,'de');
        $this->assertEquals('-100 m', $value->toString(), 'Value -100 m expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testLength_ToString()
    {
        $value = new Measure\Length('-100',Measure\Length::STANDARD,'de');
        $this->assertEquals('-100 m', $value->__toString(), 'Value -100 m expected');
    }

    /**
     * test getConversionList
     * expected array
     */
    public function testLengthConversionList()
    {
        $value = new Measure\Length('-100',Measure\Length::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }

    /**
     * @ZF-8009
     */
    public function testConvertingToSmallerUnit()
    {
        $unit   = new Measure\Length(231, Measure\Length::CENTIMETER, 'de');
        $unit2  = new Measure\Length(1, Measure\Length::METER, 'de');
        $result = $unit->add($unit2);
        $result->setType(Measure\Length::METER);

        $this->assertEquals('3.31', $result->getValue());
    }
}
