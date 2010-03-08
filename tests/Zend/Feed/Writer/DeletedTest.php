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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */



/**
 * @category   Zend
 * @package    Zend_Exception
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_DeletedTest extends PHPUnit_Framework_TestCase
{

    public function testSetsReference()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $entry->setReference('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $entry->getReference());
    }

    public function testSetReferenceThrowsExceptionOnInvalidParameter()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        try {
            $entry->setReference('');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testGetReferenceReturnsNullIfNotSet()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $this->assertTrue(is_null($entry->getReference()));
    }
    
    public function testSetWhenDefaultsToCurrentTime()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $entry->setWhen();
        $dateNow = new Zend_Date;
        $this->assertTrue($dateNow->isLater($entry->getWhen()) || $dateNow->equals($entry->getWhen()));
    }

    public function testSetWhenUsesGivenUnixTimestamp()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $entry->setWhen(1234567890);
        $myDate = new Zend_Date('1234567890', Zend_Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($entry->getWhen()));
    }

    public function testSetWhenUsesZendDateObject()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $entry->setWhen(new Zend_Date('1234567890', Zend_Date::TIMESTAMP));
        $myDate = new Zend_Date('1234567890', Zend_Date::TIMESTAMP);
        $this->assertTrue($myDate->equals($entry->getWhen()));
    }
    
    public function testSetWhenThrowsExceptionOnInvalidParameter()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        try {
            $entry->setWhen('abc');
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }
    
    public function testGetWhenReturnsNullIfDateNotSet()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $this->assertTrue(is_null($entry->getWhen()));
    }
    
    public function testAddsByNameFromArray()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $entry->setBy(array('name'=>'Joe'));
        $this->assertEquals(array('name'=>'Joe'), $entry->getBy());
    }

    public function testAddsByEmailFromArray()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $entry->setBy(array('name'=>'Joe','email'=>'joe@example.com'));
        $this->assertEquals(array('name'=>'Joe', 'email' => 'joe@example.com'), $entry->getBy());
    }

    public function testAddsByUriFromArray()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        $entry->setBy(array('name'=>'Joe','uri'=>'http://www.example.com'));
        $this->assertEquals(array('name'=>'Joe', 'uri' => 'http://www.example.com'), $entry->getBy());
    }

    public function testAddByThrowsExceptionOnInvalidNameFromArray()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        try {
            $entry->setBy(array('name'=>''));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddByThrowsExceptionOnInvalidEmailFromArray()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        try {
            $entry->setBy(array('name'=>'Joe','email'=>''));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddByThrowsExceptionOnInvalidUriFromArray()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        try {
            $entry->setBy(array('name'=>'Joe','uri'=>'notauri'));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

    public function testAddByThrowsExceptionIfNameOmittedFromArray()
    {
        $entry = new Zend_Feed_Writer_Deleted;
        try {
            $entry->setBy(array('uri'=>'notauri'));
            $this->fail();
        } catch (Zend_Feed_Exception $e) {
        }
    }

}
