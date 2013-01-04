<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Writer;

use DateTime;
use MongoDate;
use Zend\Log\Writer\MongoDB as MongoDBWriter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class MongoDBTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('The mongo PHP extension is not available');
        }

        $this->database = 'zf2_test';
        $this->collection = 'logs';

        $mongoClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? 'Mongo' : 'MongoClient';

        $this->mongo = $this->getMockBuilder($mongoClass)
            ->disableOriginalConstructor()
            ->setMethods(array('selectCollection'))
            ->getMock();

        $this->mongoCollection = $this->getMockBuilder('MongoCollection')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $this->mongo->expects($this->any())
            ->method('selectCollection')
            ->with($this->database, $this->collection)
            ->will($this->returnValue($this->mongoCollection));
    }

    public function testFormattingIsNotSupported()
    {
        $writer = new MongoDBWriter($this->mongo, $this->database, $this->collection);

        $writer->setFormatter($this->getMock('Zend\Log\Formatter\FormatterInterface'));
        $this->assertAttributeEmpty('formatter', $writer);
    }

    public function testWriteWithDefaultSaveOptions()
    {
        $event = array('message'=> 'foo', 'priority' => 42);

        $this->mongoCollection->expects($this->once())
            ->method('save')
            ->with($event, array());

        $writer = new MongoDBWriter($this->mongo, $this->database, $this->collection);

        $writer->write($event);
    }

    public function testWriteWithCustomSaveOptions()
    {
        $event = array('message' => 'foo', 'priority' => 42);
        $saveOptions = array('safe' => false, 'fsync' => false, 'timeout' => 100);

        $this->mongoCollection->expects($this->once())
            ->method('save')
            ->with($event, $saveOptions);

        $writer = new MongoDBWriter($this->mongo, $this->database, $this->collection, $saveOptions);

        $writer->write($event);
    }

    public function testWriteConvertsDateTimeToMongoDate()
    {
        $date = new DateTime();
        $event = array('timestamp'=> $date);

        $this->mongoCollection->expects($this->once())
            ->method('save')
            ->with($this->contains(new MongoDate($date->getTimestamp()), false));

        $writer = new MongoDBWriter($this->mongo, $this->database, $this->collection);

        $writer->write($event);
    }
}
