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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
* @namespace
*/
namespace ZendTest\Feed\Writer;
use Zend\Feed\Writer;
use Zend\Date;

/**
* @category Zend
* @package Zend_Exception
* @subpackage UnitTests
* @group Zend_Feed
* @group Zend_Feed_Writer
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
class DeletedTest extends \PHPUnit_Framework_TestCase
{

    public function testSetsReference()
    {
        $entry = new Writer\Deleted;
        $entry->setReference('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $entry->getReference());
    }

    public function testSetReferenceThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setReference('');
            $this->fail();
        } catch (Writer\Exception $e) {
        }
    }

    public function testGetReferenceReturnsNullIfNotSet()
    {
        $entry = new Writer\Deleted;
        $this->assertTrue(is_null($entry->getReference()));
    }
    
    public function testSetWhenDefaultsToCurrentTime()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen();
        $dateNow = new Date\Date;
        $this->assertTrue($dateNow->isLater($entry->getWhen()) || $dateNow->equals($entry->getWhen()));
    }

    public function testSetWhenUsesGivenUnixTimestamp()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen(1234567890);
        $myDate = new Date\Date('1234567890', Date\Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($entry->getWhen()));
    }
 
    /**
     * @group ZF-12070
     */
    public function testSetWhenUsesGivenUnixTimestampWhenItIsLessThanTenDigits()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen(123456789);
        $myDate = new Date\Date('123456789', Date\Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($entry->getWhen()));
    }

    /**
     * @group ZF-11610
     */
    public function testSetWhenUsesGivenUnixTimestampWhenItIsAVerySmallInteger()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen(123);
        $myDate = new Date\Date('123', Date\Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($entry->getWhen()));
    }
    
    public function testSetWhenUsesZendDateObject()
    {
        $entry = new Writer\Deleted;
        $entry->setWhen(new Date\Date('1234567890', Date\Date::TIMESTAMP));
        $myDate = new Date\Date('1234567890', Date\Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($entry->getWhen()));
    }
    
    public function testSetWhenThrowsExceptionOnInvalidParameter()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setWhen('abc');
            $this->fail();
        } catch (Writer\Exception $e) {
        }
    }
    
    public function testGetWhenReturnsNullIfDateNotSet()
    {
        $entry = new Writer\Deleted;
        $this->assertTrue(is_null($entry->getWhen()));
    }
    
    public function testAddsByNameFromArray()
    {
        $entry = new Writer\Deleted;
        $entry->setBy(array('name'=>'Joe'));
        $this->assertEquals(array('name'=>'Joe'), $entry->getBy());
    }

    public function testAddsByEmailFromArray()
    {
        $entry = new Writer\Deleted;
        $entry->setBy(array('name'=>'Joe','email'=>'joe@example.com'));
        $this->assertEquals(array('name'=>'Joe', 'email' => 'joe@example.com'), $entry->getBy());
    }

    public function testAddsByUriFromArray()
    {
        $entry = new Writer\Deleted;
        $entry->setBy(array('name'=>'Joe','uri'=>'http://www.example.com'));
        $this->assertEquals(array('name'=>'Joe', 'uri' => 'http://www.example.com'), $entry->getBy());
    }

    public function testAddByThrowsExceptionOnInvalidNameFromArray()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setBy(array('name'=>''));
            $this->fail();
        } catch (Writer\Exception $e) {
        }
    }

    public function testAddByThrowsExceptionOnInvalidEmailFromArray()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setBy(array('name'=>'Joe','email'=>''));
            $this->fail();
        } catch (Writer\Exception $e) {
        }
    }

    public function testAddByThrowsExceptionOnInvalidUriFromArray()
    {
        $this->markTestIncomplete('Pending Zend\URI fix for validation');
        $entry = new Writer\Deleted;
        try {
            $entry->setBy(array('name'=>'Joe','uri'=>'notauri'));
            $this->fail();
        } catch (Writer\Exception $e) {
        }
    }

    public function testAddByThrowsExceptionIfNameOmittedFromArray()
    {
        $entry = new Writer\Deleted;
        try {
            $entry->setBy(array('uri'=>'notauri'));
            $this->fail();
        } catch (Writer\Exception $e) {
        }
    }

}
