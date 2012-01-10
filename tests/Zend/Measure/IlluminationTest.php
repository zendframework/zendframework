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
class IlluminationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Illumination initialisation
     * expected instance
     */
    public function testIlluminationInit()
    {
        $value = new Measure\Illumination('100',Measure\Illumination::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Illumination,'Zend\Measure\Illumination Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testIlluminationUnknownType()
    {
        try {
            $value = new Measure\Illumination('100','Illumination::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testIlluminationUnknownValue()
    {
        try {
            $value = new Measure\Illumination('novalue',Measure\Illumination::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testIlluminationUnknownLocale()
    {
        try {
            $value = new Measure\Illumination('100',Measure\Illumination::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testIlluminationNoLocale()
    {
        $value = new Measure\Illumination('100',Measure\Illumination::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Illumination value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testIlluminationValuePositive()
    {
        $value = new Measure\Illumination('100',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Illumination value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testIlluminationValueNegative()
    {
        $value = new Measure\Illumination('-100',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Illumination value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testIlluminationValueDecimal()
    {
        $value = new Measure\Illumination('-100,200',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Illumination value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testIlluminationValueDecimalSeperated()
    {
        $value = new Measure\Illumination('-100.100,200',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Illumination Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testIlluminationValueString()
    {
        $value = new Measure\Illumination('-100.100,200',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Illumination Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testIlluminationEquality()
    {
        $value = new Measure\Illumination('-100.100,200',Measure\Illumination::STANDARD,'de');
        $newvalue = new Measure\Illumination('-100.100,200',Measure\Illumination::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Illumination Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testIlluminationNoEquality()
    {
        $value = new Measure\Illumination('-100.100,200',Measure\Illumination::STANDARD,'de');
        $newvalue = new Measure\Illumination('-100,200',Measure\Illumination::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Illumination Object should be not equal');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testIlluminationSetPositive()
    {
        $value = new Measure\Illumination('100',Measure\Illumination::STANDARD,'de');
        $value->setValue('200',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Illumination value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testIlluminationSetNegative()
    {
        $value = new Measure\Illumination('-100',Measure\Illumination::STANDARD,'de');
        $value->setValue('-200',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Illumination value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testIlluminationSetDecimal()
    {
        $value = new Measure\Illumination('-100,200',Measure\Illumination::STANDARD,'de');
        $value->setValue('-200,200',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Illumination value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testIlluminationSetDecimalSeperated()
    {
        $value = new Measure\Illumination('-100.100,200',Measure\Illumination::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Illumination Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testIlluminationSetString()
    {
        $value = new Measure\Illumination('-100.100,200',Measure\Illumination::STANDARD,'de');
        $value->setValue('-200.200,200',Measure\Illumination::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Illumination Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testIlluminationSetUnknownType()
    {
        try {
            $value = new Measure\Illumination('100',Measure\Illumination::STANDARD,'de');
            $value->setValue('-200.200,200','Illumination::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testIlluminationSetUnknownValue()
    {
        try {
            $value = new Measure\Illumination('100',Measure\Illumination::STANDARD,'de');
            $value->setValue('novalue',Measure\Illumination::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testIlluminationSetUnknownLocale()
    {
        try {
            $value = new Measure\Illumination('100',Measure\Illumination::STANDARD,'de');
            $value->setValue('200',Measure\Illumination::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testIlluminationSetWithNoLocale()
    {
        $value = new Measure\Illumination('100', Measure\Illumination::STANDARD, 'de');
        $value->setValue('200', Measure\Illumination::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Illumination value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testIlluminationSetType()
    {
        $value = new Measure\Illumination('-100',Measure\Illumination::STANDARD,'de');
        $value->setType(Measure\Illumination::NOX);
        $this->assertEquals(Measure\Illumination::NOX, $value->getType(), 'Zend\Measure\Illumination type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testIlluminationSetTypeFailed()
    {
        try {
            $value = new Measure\Illumination('-100',Measure\Illumination::STANDARD,'de');
            $value->setType('Illumination::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testIlluminationToString()
    {
        $value = new Measure\Illumination('-100',Measure\Illumination::STANDARD,'de');
        $this->assertEquals('-100 lx', $value->toString(), 'Value -100 lx expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testIllumination_ToString()
    {
        $value = new Measure\Illumination('-100',Measure\Illumination::STANDARD,'de');
        $this->assertEquals('-100 lx', $value->__toString(), 'Value -100 lx expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testIlluminationConversionList()
    {
        $value = new Measure\Illumination('-100',Measure\Illumination::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
