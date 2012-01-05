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
class BinaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Binary initialisation
     * expected instance
     */
    public function testBinaryInit()
    {
        $value = new Measure\Binary('100',Measure\Binary::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Binary,'Zend\Measure\Binary Object not returned');
    }

    /**
     * test for exception unknown type
     * expected exception
     */
    public function testBinaryUnknownType()
    {
        try {
            $value = new Measure\Binary('100','Binary::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown value
     * expected exception
     */
    public function testBinaryUnknownValue()
    {
        try {
            $value = new Measure\Binary('novalue',Measure\Binary::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testBinaryUnknownLocale()
    {
        try {
            $value = new Measure\Binary('100',Measure\Binary::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception no locale
     * expected root value
     */
    public function testBinaryNoLocale()
    {
        $value = new Measure\Binary('100',Measure\Binary::STANDARD);
        $this->assertTrue(is_object($value),'Object expected because of standard locale');
    }

    /**
     * test for positive value
     * expected integer
     */
    public function testBinaryValuePositive()
    {
        $value = new Measure\Binary('100',Measure\Binary::STANDARD,'de');
        $this->assertEquals('100', $value->getValue(), 'Zend\Measure\Binary value expected to be a positive integer');
    }

    /**
     * test for negative value
     * expected integer
     */
    public function testBinaryValueNegative()
    {
        $value = new Measure\Binary('-100',Measure\Binary::STANDARD,'de');
        $this->assertEquals('-100', $value->getValue(), 'Zend\Measure\Binary value expected to be a negative integer');
    }

    /**
     * test for decimal value
     * expected float
     */
    public function testBinaryValueDecimal()
    {
        $value = new Measure\Binary('-100,200',Measure\Binary::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Binary value expected to be a decimal value');
    }

    /**
     * test for decimal seperated value
     * expected float
     */
    public function testBinaryValueDecimalSeperated()
    {
        $value = new Measure\Binary('-100.100,200',Measure\Binary::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Binary Object not returned');
    }

    /**
     * test for equality
     * expected true
     */
    public function testBinaryEquality()
    {
        $value = new Measure\Binary('-100.100,200',Measure\Binary::STANDARD,'de');
        $newvalue = new Measure\Binary('-100100,200',Measure\Binary::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Binary Object should be equal');
    }

    /**
     * test for no equality
     * expected false
     */
    public function testBinaryNoEquality()
    {
        $value = new Measure\Binary('-100.100,200',Measure\Binary::STANDARD,'de');
        $newvalue = new Measure\Binary('-100,200',Measure\Binary::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Binary Object should be not equal');
    }

    /**
     * test for set positive value
     * expected integer
     */
    public function testBinarySetPositive()
    {
        $value = new Measure\Binary('100',Measure\Binary::STANDARD,'de');
        $value->setValue('200',Measure\Binary::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Binary value expected to be a positive integer');
    }

    /**
     * test for set negative value
     * expected integer
     */
    public function testBinarySetNegative()
    {
        $value = new Measure\Binary('-100',Measure\Binary::STANDARD,'de');
        $value->setValue('-200',Measure\Binary::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Binary value expected to be a negative integer');
    }

    /**
     * test for set decimal value
     * expected float
     */
    public function testBinarySetDecimal()
    {
        $value = new Measure\Binary('-100,200',Measure\Binary::STANDARD,'de');
        $value->setValue('-200,200',Measure\Binary::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Binary value expected to be a decimal value');
    }

    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testBinarySetDecimalSeperated()
    {
        $value = new Measure\Binary('-100.100,200',Measure\Binary::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Binary::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Binary Object not returned');
    }

    /**
     * test for set string with integrated value
     * expected float
     */
    public function testBinarySetString()
    {
        $value = new Measure\Binary('-100.100,200', Measure\Binary::STANDARD, 'de');
        $value->setValue('-200.200,200', Measure\Binary::STANDARD, 'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Binary Object not returned');
    }

    /**
     * test for exception unknown type
     * expected exception
     */
    public function testBinarySetUnknownType()
    {
        try {
            $value = new Measure\Binary('100',Measure\Binary::STANDARD,'de');
            $value->setValue('-200.200,200','Binary::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown value
     * expected exception
     */
    public function testBinarySetUnknownValue()
    {
        try {
            $value = new Measure\Binary('100',Measure\Binary::STANDARD,'de');
            $value->setValue('novalue',Measure\Binary::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testBinarySetUnknownLocale()
    {
        try {
            $value = new Measure\Binary('100',Measure\Binary::STANDARD,'de');
            $value->setValue('200',Measure\Binary::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testBinarySetWithNoLocale()
    {
        $value = new Measure\Binary('100', Measure\Binary::STANDARD, 'de');
        $value->setValue('200', Measure\Binary::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Binary value expected to be a positive integer');
    }

    /**
     * test setting type
     * expected new type
     */
    public function testBinarySetType()
    {
        $value = new Measure\Binary('-100',Measure\Binary::STANDARD,'de');
        $value->setType(Measure\Binary::GIGABYTE);
        $this->assertEquals(Measure\Binary::GIGABYTE, $value->getType(), 'Zend\Measure\Binary type expected');
    }

    /**
     * test setting computed type
     * expected new type
     */
    public function testBinarySetComputedType1()
    {
        $value = new Measure\Binary('-100',Measure\Binary::MEGABYTE,'de');
        $value->setType(Measure\Binary::TERABYTE);
        $this->assertEquals(Measure\Binary::TERABYTE, $value->getType(), 'Zend\Measure\Binary type expected');
    }

    /**
     * test setting computed type
     * expected new type
     */
    public function testBinarySetComputedType2()
    {
        $value = new Measure\Binary('-100',Measure\Binary::TERABYTE,'de');
        $value->setType(Measure\Binary::KILOBYTE);
        $this->assertEquals(Measure\Binary::KILOBYTE, $value->getType(), 'Zend\Measure\Binary type expected');
    }

    /**
     * test setting unknown type
     * expected new type
     */
    public function testBinarySetTypeFailed()
    {
        try {
            $value = new Measure\Binary('-100',Measure\Binary::STANDARD,'de');
            $value->setType('Binary::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test toString
     * expected string
     */
    public function testBinaryToString()
    {
        $value = new Measure\Binary('-100',Measure\Binary::STANDARD,'de');
        $this->assertEquals('-100 B', $value->toString(), 'Value -100 B expected');
    }

    /**
     * test __toString
     * expected string
     */
    public function testBinary_ToString()
    {
        $value = new Measure\Binary('-100',Measure\Binary::STANDARD,'de');
        $this->assertEquals('-100 B', $value->__toString(), 'Value -100 B expected');
    }

    /**
     * test getConversionList
     * expected array
     */
    public function testBinaryConversionList()
    {
        $value = new Measure\Binary('-100',Measure\Binary::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
