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
use Zend\Locale;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
class AccelerationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * test for new object
     * expected instance
     */
    public function testAccelerationInit()
    {
        $value = new Measure\Acceleration('100',Measure\Acceleration::STANDARD,'de');
        $this->assertTrue($value instanceof Measure\Acceleration,'Zend\Measure\Acceleration Object not returned');
        $this->assertEquals(100, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a positive integer');
        // no type
        $value = new Measure\Acceleration('100','de');
        $this->assertTrue($value instanceof Measure\Acceleration,'Zend\Measure\Acceleration Object not returned');
        // unknown type
        try {
            $value = new Measure\Acceleration('100','Acceleration::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
        // no value
        try {
            $value = new Measure\Acceleration('novalue',Measure\Acceleration::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }
        // false locale
        try {
            $value = new Measure\Acceleration('100',Measure\Acceleration::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
        // no locale
        $value = new Measure\Acceleration('100',Measure\Acceleration::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend\Measure\Acceleration value expected');

        // negative value
        $locale = new Locale\Locale('de');
        $value = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,$locale);
        $this->assertEquals(-100, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a negative integer');
        // seperated value
        $value = new Measure\Acceleration('-100,200',Measure\Acceleration::STANDARD,$locale);
        $this->assertEquals(-100.200, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a decimal value');
        // negative seperated value
        $value = new Measure\Acceleration('-100.100,200',Measure\Acceleration::STANDARD,$locale);
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Acceleration Object not returned');
        // value with string
        $value = new Measure\Acceleration('-100.100,200',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend\Measure\Acceleration Object not returned');
    }


    /**
     * test for equals()
     * expected true
     */
    public function testAccelerationEquals()
    {
        $value = new Measure\Acceleration('-100.100,200',Measure\Acceleration::STANDARD,'de');
        $newvalue = new Measure\Acceleration('-100.100,200',Measure\Acceleration::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend\Measure\Acceleration Object should be equal');

        $value = new Measure\Acceleration('-100.100,200',Measure\Acceleration::STANDARD,'de');
        $newvalue = new Measure\Acceleration('-100,200',Measure\Acceleration::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend\Measure\Acceleration Object should be not equal');
    }


    /**
     * test for setvalue()
     * expected integer
     */
    public function testAccelerationSetValue()
    {
        $value = new Measure\Acceleration('100',Measure\Acceleration::STANDARD,'de');
        $value->setValue('200',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a positive integer');

        $locale = new Locale\Locale('de_AT');
        $value->setValue('200',$locale);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a positive integer');
        $value->setValue('200','de');
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a positive integer');
        $value->setValue('-200',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a negative integer');
        $value->setValue('-200,200',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a decimal value');
        $value->setValue('-200.200,200',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Acceleration Object not returned');
        $value->setValue('-200.200,200',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend\Measure\Acceleration Object not returned');
        $value->setValue('200', Measure\Acceleration::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend\Measure\Acceleration value expected to be a positive integer');

        try {
            $value->setValue('-200.200,200','Acceleration::UNKNOWN','de');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }

        try {
            $value->setValue('novalue',Measure\Acceleration::STANDARD,'de');
            $this->fail('Exception expected because of empty value');
        } catch (Measure\Exception $e) {
            // success
        }

        try {
            $value = new Measure\Acceleration('100',Measure\Acceleration::STANDARD,'de');
            $value->setValue('200',Measure\Acceleration::STANDARD,'nolocale');
            $this->fail('Exception expected because of unknown locale');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test setting type
     * expected new type
     */
    public function testAccelerationSetType()
    {
        $value = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
        $value->setType(Measure\Acceleration::GRAV);
        $this->assertEquals(Measure\Acceleration::GRAV, $value->getType(), 'Zend\Measure\Acceleration type expected');

        $value = new Measure\Acceleration('-100',Measure\Acceleration::MILE_PER_HOUR_MINUTE,'de');
        $value->setType(Measure\Acceleration::GRAV);
        $this->assertEquals(Measure\Acceleration::GRAV, $value->getType(), 'Zend\Measure\Acceleration type expected');

        $value = new Measure\Acceleration('-100',Measure\Acceleration::GRAV,'de');
        $value->setType(Measure\Acceleration::MILE_PER_HOUR_MINUTE);
        $this->assertEquals(Measure\Acceleration::MILE_PER_HOUR_MINUTE, $value->getType(), 'Zend\Measure\Acceleration type expected');

        try {
            $value = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
            $value->setType('Acceleration::UNKNOWN');
            $this->fail('Exception expected because of unknown type');
        } catch (Measure\Exception $e) {
            // success
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testAccelerationToString()
    {
        $value = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals('-100 m/s²', $value->toString(), 'Value -100 m/s² expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testAcceleration_ToString()
    {
        $value = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals('-100 m/s²', $value->__toString(), 'Value -100 m/s² expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testAccelerationConversionList()
    {
        $value = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }


    /**
     * test convertTo
     * expected array
     */
    public function testAccelerationConvertTo()
    {
        $value = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
        $unit  = $value->convertTo(Measure\Acceleration::GRAV);
        $this->assertEquals(Measure\Acceleration::GRAV, $value->getType(), 'Zend\Measure\Acceleration type expected');
    }


    /**
     * test add
     * expected array
     */
    public function testAccelerationAdd()
    {
        $value  = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
        $value2 = new Measure\Acceleration('200',Measure\Acceleration::STANDARD,'de');
        $value->add($value2);
        $this->assertEquals(100, $value->getValue(), 'value 100 expected');
    }


    /**
     * test add
     * expected array
     */
    public function testAccelerationSub()
    {
        $value  = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
        $value2 = new Measure\Acceleration('200',Measure\Acceleration::STANDARD,'de');
        $value->sub($value2);
        $this->assertEquals(-300, $value->getValue(), 'value -300 expected');
    }


    /**
     * test add
     * expected array
     */
    public function testAccelerationCompare()
    {
        $value  = new Measure\Acceleration('-100',Measure\Acceleration::STANDARD,'de');
        $value2 = new Measure\Acceleration('200',Measure\Acceleration::STANDARD,'de');
        $value3 = new Measure\Acceleration('200',Measure\Acceleration::STANDARD,'de');
        $this->assertEquals(-1, $value->compare( $value2));
        $this->assertEquals( 1, $value2->compare($value ));
        $this->assertEquals( 0, $value2->compare($value3));
    }
}
