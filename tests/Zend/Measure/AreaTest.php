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
class AreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for area initialisation
     * expected instance
     */
    public function testAreaInit()
    {
        $value = new Measure\Area('100',Measure\Area::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Area,'Zend\Measure\Area Object not returned');
    }

    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAreaUnknownType()
    {
        try {
            $value = new Measure\Area('100','Area::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown value
     * expected exception
     */
    public function testAreaUnknownValue()
    {
        try {
            $value = new Measure\Area('novalue',Measure\Area::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testAreaUnknownLocale()
    {
        try {
            $value = new Measure\Area('100',Measure\Area::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for standard locale
     * expected integer
     */
    public function testAreaNoLocale()
    {
        $value = new Measure\Area('100',Measure\Area::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Area value expected');
    }

    /**
     * test for positive value
     * expected integer
     */
    public function testAreaValuePositive()
    {
        $value = new Measure\Area('100',Measure\Area::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Area value expected to be a positive integer');
    }

    /**
     * test for negative value
     * expected integer
     */
    public function testAreaValueNegative()
    {
        $value = new Measure\Area('-100',Measure\Area::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Area value expected to be a negative integer');
    }

    /**
     * test for decimal value
     * expected float
     */
    public function testAreaValueDecimal()
    {
        $value = new Measure\Area('-100,200',Measure\Area::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Area value expected to be a decimal value');
    }

    /**
     * test for decimal seperated value
     * expected float
     */
    public function testAreaValueDecimalSeperated()
    {
        $value = new Measure\Area('-100.100,200',Measure\Area::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Area Object not returned');
    }

    /**
     * test for string with integrated value
     * expected float
     */
    public function testAreaValueString()
    {
        $value = new Measure\Area('-100.100,200',Measure\Area::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Area Object not returned');
    }

    /**
     * test for equality
     * expected true
     */
    public function testAreaEquality()
    {
        $value = new Measure\Area('-100.100,200',Measure\Area::STANDARD,'de');
        $newvalue = new Measure\Area('-100.100,200',Measure\Area::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Area Object should be equal');
    }

    /**
     * test for no equality
     * expected false
     */
    public function testAreaNoEquality()
    {
        $value = new Measure\Area('-100.100,200',Measure\Area::STANDARD,'de');
        $newvalue = new Measure\Area('-100,200',Measure\Area::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Area Object should be not equal');
    }

    /**
     * test for set positive value
     * expected integer
     */
    public function testAreaSetPositive()
    {
        $value = new Measure\Area('100',Measure\Area::STANDARD,'de');
        $value->setValue('200',Measure\Area::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Area value expected to be a positive integer');
    }

    /**
     * test for set negative value
     * expected integer
     */
    public function testAreaSetNegative()
    {
        $value = new Measure\Area('-100',Measure\Area::STANDARD,'de');
        $value->setValue('-200',Measure\Area::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Area value expected to be a negative integer');
    }

    /**
     * test for set decimal value
     * expected float
     */
    public function testAreaSetDecimal()
    {
        $value = new Measure\Area('-100,200',Measure\Area::STANDARD,'de');
        $value->setValue('-200,200',Measure\Area::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Area value expected to be a decimal value');
    }

    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testAreaSetDecimalSeperated()
    {
        $value = new Measure\Area('-100.100,200',Measure\Area::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Area::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Area Object not returned');
    }

    /**
     * test for set string with integrated value
     * expected float
     */
    public function testAreaSetString()
    {
        $value = new Measure\Area('-100.100,200',Measure\Area::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Area::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Area Object not returned');
    }

    /**
     * test for exception unknown type
     * expected exception
     */
    public function testAreaSetUnknownType()
    {
        try {
            $value = new Measure\Area('100',Measure\Area::STANDARD,'de');
            $value->setValue('-200.200,200','Area::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown value
     * expected exception
     */
    public function testAreaSetUnknownValue()
    {
        try {
            $value = new Measure\Area('100',Measure\Area::STANDARD,'de');
            $value->setValue('novalue',Measure\Area::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAreaSetUnknownLocale()
    {
        try {
            $value = new Measure\Area('100',Measure\Area::STANDARD,'de');
            $value->setValue('200',Measure\Area::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testAreaSetWithNoLocale()
    {
        $value = new Measure\Area('100', Measure\Area::STANDARD, 'de');
        $value->setValue('200', Measure\Area::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Area value expected to be a positive integer');
    }

    /**
     * test setting type
     * expected new type
     */
    public function testAreaSetType()
    {
        $value = new Measure\Area('-100',Measure\Area::STANDARD,'de');
        $value->setType(Measure\Area::MORGEN);
        $this->assertEquals(Measure\Area::MORGEN, $value->getType(), 'Zend\Measure\Area type expected');
    }

    /**
     * test setting computed type
     * expected new type
     */
    public function testAreaSetComputedType1()
    {
        $value = new Measure\Area('-100',Measure\Area::SQUARE_MILE,'de');
        $value->setType(Measure\Area::SQUARE_INCH);
        $this->assertEquals(Measure\Area::SQUARE_INCH, $value->getType(), 'Zend\Measure\Area type expected');
    }

    /**
     * test setting computed type
     * expected new type
     */
    public function testAreaSetComputedType2()
    {
        $value = new Measure\Area('-100',Measure\Area::SQUARE_INCH,'de');
        $value->setType(Measure\Area::SQUARE_MILE);
        $this->assertEquals(Measure\Area::SQUARE_MILE, $value->getType(), 'Zend\Measure\Area type expected');
    }

    /**
     * test setting unknown type
     * expected new type
     */
    public function testAreaSetTypeFailed()
    {
        try {
            $value = new Measure\Area('-100',Measure\Area::STANDARD,'de');
            $value->setType('Area::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }

    /**
     * test toString
     * expected string
     */
    public function testAreaToString()
    {
        $value = new Measure\Area('-100',Measure\Area::STANDARD,'de');
        $this->assertEquals('-100 m²', $value->toString(), 'Value -100 m² expected');
    }

    /**
     * test __toString
     * expected string
     */
    public function testArea_ToString()
    {
        $value = new Measure\Area('-100',Measure\Area::STANDARD,'de');
        $this->assertEquals('-100 m²', $value->__toString(), 'Value -100 m² expected');
    }

    /**
     * test getConversionList
     * expected array
     */
    public function testAreaConversionList()
    {
        $value = new Measure\Area('-100',Measure\Area::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
