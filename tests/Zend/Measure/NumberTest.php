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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Measure_Number
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class Zend_Measure_NumberTest extends PHPUnit_Framework_TestCase
{
    /**
     * test for Number initialisation
     * expected instance
     */
    public function testNumberInit()
    {
        $value = new Zend_Measure_Number('100',Zend_Measure_Number::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Number,'Zend_Measure_Number Object not returned');
        $value = new Zend_Measure_Number('100','de');
        $this->assertTrue($value instanceof Zend_Measure_Number,'Zend_Measure_Number Object not returned');
        $value = new Zend_Measure_Number('100',Zend_Measure_Number::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Number value expected');
        $value = new Zend_Measure_Number('100',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Number value expected to be a positive integer');
        $value = new Zend_Measure_Number('-100',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Number value expected to be a negative integer');
        $value = new Zend_Measure_Number('-100,200',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Number value expected to be a decimal value');
        $value = new Zend_Measure_Number('-100.100,200',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(100100, $value->getValue(),'Zend_Measure_Number Object not returned');
        $value = new Zend_Measure_Number('-100.100,200',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(100100, $value->getValue(),'Zend_Measure_Number Object not returned');

        try {
            $value = new Zend_Measure_Number('100','Number::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
        try {
            $value = new Zend_Measure_Number('novalue',Zend_Measure_Number::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
        try {
            $value = new Zend_Measure_Number('100',Zend_Measure_Number::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test for equality
     * expected true
     */
    public function testNumberEquality()
    {
        $value = new Zend_Measure_Number('-100.100,200',Zend_Measure_Number::STANDARD,'de');
        $newvalue = new Zend_Measure_Number('-100.100,200',Zend_Measure_Number::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Number Object should be equal');

        $value = new Zend_Measure_Number('-100.100,200',Zend_Measure_Number::STANDARD,'de');
        $newvalue = new Zend_Measure_Number('-100,200',Zend_Measure_Number::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Number Object should be not equal');
    }


    /**
     * test for setValue
     * expected integer
     */
    public function testNumberSetValue()
    {
        $value = new Zend_Measure_Number('100',Zend_Measure_Number::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Number value expected to be a positive integer');
        $value->setValue('-200',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Number value expected to be a negative integer');
        $value->setValue('-200,200',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Number value expected to be a decimal value');
        $value->setValue('-200.200,200',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(200200, $value->getValue(),'Zend_Measure_Number Object not returned');
        $value->setValue('-200.200,200',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals(200200, $value->getValue(),'Zend_Measure_Number Object not returned');

        try {
            $value = new Zend_Measure_Number('100',Zend_Measure_Number::STANDARD,'de');
            $value->setValue('-200.200,200','Number::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
        try {
            $value = new Zend_Measure_Number('100',Zend_Measure_Number::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Number::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
        try {
            $value = new Zend_Measure_Number('100',Zend_Measure_Number::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Number::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Zend_Measure_Exception $e) {
            // success
        }

        $value->setValue('200', Zend_Measure_Number::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Number value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testNumberSetType()
    {
        $value = new Zend_Measure_Number('-100',Zend_Measure_Number::STANDARD,'de');
        $value->setType(Zend_Measure_Number::BINARY);
        $this->assertEquals(Zend_Measure_Number::BINARY, $value->getType(), 'Zend_Measure_Number type expected');
        $value->setType(Zend_Measure_Number::ROMAN);
        $this->assertEquals(Zend_Measure_Number::ROMAN, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1001020',Zend_Measure_Number::BINARY,'de');
        $value->setType(Zend_Measure_Number::HEXADECIMAL);
        $this->assertEquals(Zend_Measure_Number::HEXADECIMAL, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('MCXVII',Zend_Measure_Number::ROMAN,'de');
        $value->setType(Zend_Measure_Number::HEXADECIMAL);
        $this->assertEquals(Zend_Measure_Number::HEXADECIMAL, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('102122',Zend_Measure_Number::TERNARY,'de');
        $value->setType(Zend_Measure_Number::OCTAL);
        $this->assertEquals(Zend_Measure_Number::OCTAL, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1032402',Zend_Measure_Number::QUATERNARY,'de');
        $value->setType(Zend_Measure_Number::QUINARY);
        $this->assertEquals(Zend_Measure_Number::QUINARY, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1052402',Zend_Measure_Number::QUINARY,'de');
        $value->setType(Zend_Measure_Number::QUATERNARY);
        $this->assertEquals(Zend_Measure_Number::QUATERNARY, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1632402',Zend_Measure_Number::SENARY,'de');
        $value->setType(Zend_Measure_Number::SEPTENARY);
        $this->assertEquals(Zend_Measure_Number::SEPTENARY, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1632702', Zend_Measure_Number::SEPTENARY, 'de');
        $value->setType(Zend_Measure_Number::SENARY);
        $this->assertEquals(Zend_Measure_Number::SENARY, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1832402',Zend_Measure_Number::NONARY,'de');
        $value->setType(Zend_Measure_Number::SEPTENARY);
        $this->assertEquals(Zend_Measure_Number::SEPTENARY, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1632402',Zend_Measure_Number::DUODECIMAL,'de');
        $value->setType(Zend_Measure_Number::SEPTENARY);
        $this->assertEquals(Zend_Measure_Number::SEPTENARY, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1234ACE',Zend_Measure_Number::HEXADECIMAL,'de');
        $value->setType(Zend_Measure_Number::TERNARY);
        $this->assertEquals(Zend_Measure_Number::TERNARY, $value->getType(), 'Zend_Measure_Number type expected');

        $value = new Zend_Measure_Number('1234075',Zend_Measure_Number::OCTAL,'de');
        $value->setType(Zend_Measure_Number::TERNARY);
        $this->assertEquals(Zend_Measure_Number::TERNARY, $value->getType(), 'Zend_Measure_Number type expected');

        try {
            $value = new Zend_Measure_Number('-100',Zend_Measure_Number::STANDARD,'de');
            $value->setType('Number::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Zend_Measure_Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testNumberToString()
    {
        $value = new Zend_Measure_Number('-100',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals('100 ⑽', $value->toString(), 'Value 100 ⑽ expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testNumber_ToString()
    {
        $value = new Zend_Measure_Number('-100',Zend_Measure_Number::STANDARD,'de');
        $this->assertEquals('100 ⑽', $value->__toString(), 'Value 100 ⑽ expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testNumberConversionList()
    {
        $value = new Zend_Measure_Number('-100',Zend_Measure_Number::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }

    /**
     * test convertTo
     */
    public function testConvertTo()
    {
        $value = new Zend_Measure_Number('III',Zend_Measure_Number::ROMAN, 'en');
        $this->assertEquals('3 ⑽', $value->convertTo(Zend_Measure_Number::DECIMAL));

        $value = new Zend_Measure_Number('XXV',Zend_Measure_Number::ROMAN, 'en');
        $this->assertEquals('25 ⑽', $value->convertTo(Zend_Measure_Number::DECIMAL));

        $value = new Zend_Measure_Number('_X',Zend_Measure_Number::ROMAN, 'en');
        $this->assertEquals('10,000 ⑽', $value->convertTo(Zend_Measure_Number::DECIMAL));
    }
}
