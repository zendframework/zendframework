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
class NumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for Number initialisation
     * expected instance
     */
    public function testNumberInit()
    {
        $value = new Measure\Number('100',Measure\Number::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Number,'Zend\Measure\Number Object not returned');
        $value = new Measure\Number('100','de');
        $this->assertTrue($value instanceof Measure\Number,'Zend\Measure\Number Object not returned');
        $value = new Measure\Number('100',Measure\Number::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Number value expected');
        $value = new Measure\Number('100',Measure\Number::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Number value expected to be a positive integer');
        $value = new Measure\Number('-100',Measure\Number::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Number value expected to be a negative integer');
        $value = new Measure\Number('-100,200',Measure\Number::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Number value expected to be a decimal value');
        $value = new Measure\Number('-100.100,200',Measure\Number::STANDARD,'de');
        $this->assertEquals(100100, $value->getValue(),'Zend\Measure\Number Object not returned');
        $value = new Measure\Number('-100.100,200',Measure\Number::STANDARD,'de');
        $this->assertEquals(100100, $value->getValue(),'Zend\Measure\Number Object not returned');

        try {
            $value = new Measure\Number('100','Number::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
        try {
            $value = new Measure\Number('novalue',Measure\Number::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
        try {
            $value = new Measure\Number('100',Measure\Number::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test for equality
     * expected true
     */
    public function testNumberEquality()
    {
        $value = new Measure\Number('-100.100,200',Measure\Number::STANDARD,'de');
        $newvalue = new Measure\Number('-100.100,200',Measure\Number::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Number Object should be equal');

        $value = new Measure\Number('-100.100,200',Measure\Number::STANDARD,'de');
        $newvalue = new Measure\Number('-100,200',Measure\Number::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Number Object should be not equal');
    }


    /**
     * test for setValue
     * expected integer
     */
    public function testNumberSetValue()
    {
        $value = new Measure\Number('100',Measure\Number::STANDARD,'de');
        $value->setValue('200',Measure\Number::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Number value expected to be a positive integer');
        $value->setValue('-200',Measure\Number::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Number value expected to be a negative integer');
        $value->setValue('-200,200',Measure\Number::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Number value expected to be a decimal value');
        $value->setValue('-200.200,200',Measure\Number::STANDARD,'de');
        $this->assertEquals(200200, $value->getValue(),'Zend\Measure\Number Object not returned');
        $value->setValue('-200.200,200',Measure\Number::STANDARD,'de');
        $this->assertEquals(200200, $value->getValue(),'Zend\Measure\Number Object not returned');

        try {
            $value = new Measure\Number('100',Measure\Number::STANDARD,'de');
            $value->setValue('-200.200,200','Number::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
        try {
            $value = new Measure\Number('100',Measure\Number::STANDARD,'de');
            $value->setValue('novalue',Measure\Number::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
        try {
            $value = new Measure\Number('100',Measure\Number::STANDARD,'de');
            $value->setValue('200',Measure\Number::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }

        $value->setValue('200', Measure\Number::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Number value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testNumberSetType()
    {
        $value = new Measure\Number('-100',Measure\Number::STANDARD,'de');
        $value->setType(Measure\Number::BINARY);
        $this->assertEquals(Measure\Number::BINARY, $value->getType(), 'Zend\Measure\Number type expected');
        $value->setType(Measure\Number::ROMAN);
        $this->assertEquals(Measure\Number::ROMAN, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1001020',Measure\Number::BINARY,'de');
        $value->setType(Measure\Number::HEXADECIMAL);
        $this->assertEquals(Measure\Number::HEXADECIMAL, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('MCXVII',Measure\Number::ROMAN,'de');
        $value->setType(Measure\Number::HEXADECIMAL);
        $this->assertEquals(Measure\Number::HEXADECIMAL, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('102122',Measure\Number::TERNARY,'de');
        $value->setType(Measure\Number::OCTAL);
        $this->assertEquals(Measure\Number::OCTAL, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1032402',Measure\Number::QUATERNARY,'de');
        $value->setType(Measure\Number::QUINARY);
        $this->assertEquals(Measure\Number::QUINARY, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1052402',Measure\Number::QUINARY,'de');
        $value->setType(Measure\Number::QUATERNARY);
        $this->assertEquals(Measure\Number::QUATERNARY, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1632402',Measure\Number::SENARY,'de');
        $value->setType(Measure\Number::SEPTENARY);
        $this->assertEquals(Measure\Number::SEPTENARY, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1632702', Measure\Number::SEPTENARY, 'de');
        $value->setType(Measure\Number::SENARY);
        $this->assertEquals(Measure\Number::SENARY, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1832402',Measure\Number::NONARY,'de');
        $value->setType(Measure\Number::SEPTENARY);
        $this->assertEquals(Measure\Number::SEPTENARY, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1632402',Measure\Number::DUODECIMAL,'de');
        $value->setType(Measure\Number::SEPTENARY);
        $this->assertEquals(Measure\Number::SEPTENARY, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1234ACE',Measure\Number::HEXADECIMAL,'de');
        $value->setType(Measure\Number::TERNARY);
        $this->assertEquals(Measure\Number::TERNARY, $value->getType(), 'Zend\Measure\Number type expected');

        $value = new Measure\Number('1234075',Measure\Number::OCTAL,'de');
        $value->setType(Measure\Number::TERNARY);
        $this->assertEquals(Measure\Number::TERNARY, $value->getType(), 'Zend\Measure\Number type expected');

        try {
            $value = new Measure\Number('-100',Measure\Number::STANDARD,'de');
            $value->setType('Number::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testNumberToString()
    {
        $value = new Measure\Number('-100',Measure\Number::STANDARD,'de');
        $this->assertEquals('100 ⑽', $value->toString(), 'Value 100 ⑽ expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testNumber_ToString()
    {
        $value = new Measure\Number('-100',Measure\Number::STANDARD,'de');
        $this->assertEquals('100 ⑽', $value->__toString(), 'Value 100 ⑽ expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testNumberConversionList()
    {
        $value = new Measure\Number('-100',Measure\Number::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }

    /**
     * test convertTo
     */
    public function testConvertTo()
    {
        $value = new Measure\Number('III',Measure\Number::ROMAN, 'en');
        $this->assertEquals('3 ⑽', $value->convertTo(Measure\Number::DECIMAL));

        $value = new Measure\Number('XXV',Measure\Number::ROMAN, 'en');
        $this->assertEquals('25 ⑽', $value->convertTo(Measure\Number::DECIMAL));

        $value = new Measure\Number('_X',Measure\Number::ROMAN, 'en');
        $this->assertEquals('10,000 ⑽', $value->convertTo(Measure\Number::DECIMAL));
    }
}
