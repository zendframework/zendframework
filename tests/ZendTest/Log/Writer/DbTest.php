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
use ZendTest\Log\TestAsset\MockDbAdapter;
use Zend\Log\Writer\Db as DbWriter;
use Zend\Log\Formatter\FormatterInterface;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class DbTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tableName = 'db-table-name';

        $this->db     = new MockDbAdapter();
        $this->writer = new DbWriter($this->db, $this->tableName);
    }

    public function testNotPassingTableNameToConstructorThrowsException()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'table name');
        $writer = new DbWriter($this->db);
    }

    public function testNotPassingDbToConstructorThrowsException()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'Adapter');
        $writer = new DbWriter(array());
    }

    public function testPassingTableNameAsArgIsOK()
    {
        $options = array(
            'db'    => $this->db,
            'table' => $this->tableName,
        );
        $writer = new DbWriter($options);
        $this->assertInstanceOf('Zend\Log\Writer\Db', $writer);
        $this->assertAttributeEquals($this->tableName, 'tableName', $writer);
    }

    public function testWriteWithDefaults()
    {
        // log to the mock db adapter
        $fields = array(
            'message'  => 'foo',
            'priority' => 42
        );

        $this->writer->write($fields);

        // insert should be called once...
        $this->assertContains('query', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['query']));
        $this->assertContains('execute', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['execute']));
        $this->assertEquals(array($fields), $this->db->calls['execute'][0]);
    }

    public function testWriteWithDefaultsUsingArray()
    {
        // log to the mock db adapter
        $message  = 'message-to-log';
        $priority = 2;
        $events = array(
            'file' => 'test',
            'line' => 1
        );
        $this->writer->write(array(
            'message'  => $message,
            'priority' => $priority,
            'events'   => $events
        ));
        $this->assertContains('query', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['query']));

        $binds = array(
            'message' => $message,
            'priority' => $priority,
            'events_line' => $events['line'],
            'events_file' => $events['file']
        );
        $this->assertEquals(array($binds), $this->db->calls['execute'][0]);
    }

    public function testWriteWithDefaultsUsingArrayAndSeparator()
    {
        $this->writer = new DbWriter($this->db, $this->tableName, null, '-');

        // log to the mock db adapter
        $message  = 'message-to-log';
        $priority = 2;
        $events = array(
            'file' => 'test',
            'line' => 1
        );
        $this->writer->write(array(
            'message'  => $message,
            'priority' => $priority,
            'events'   => $events
        ));
        $this->assertContains('query', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['query']));

        $binds = array(
            'message' => $message,
            'priority' => $priority,
            'events-line' => $events['line'],
            'events-file' => $events['file']
        );
        $this->assertEquals(array($binds), $this->db->calls['execute'][0]);
    }

    public function testWriteUsesOptionalCustomColumnNames()
    {
        $this->writer = new DbWriter($this->db, $this->tableName, array(
            'message' => 'new-message-field' ,
            'priority' => 'new-priority-field'
        ));

        // log to the mock db adapter
        $message  = 'message-to-log';
        $priority = 2;
        $this->writer->write(array(
            'message' => $message,
            'priority' => $priority
        ));

        // insert should be called once...
        $this->assertContains('query', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['query']));

        // ...with the correct table and binds for the database
        $binds = array(
            'new-message-field' => $message,
            'new-priority-field' => $priority
        );
        $this->assertEquals(array($binds), $this->db->calls['execute'][0]);
    }

    public function testWriteUsesParamsWithArray()
    {
        $this->writer = new DbWriter($this->db, $this->tableName, array(
            'message' => 'new-message-field' ,
            'priority' => 'new-priority-field',
            'events' => array(
                'line' => 'new-line',
                'file' => 'new-file'
            )
        ));

        // log to the mock db adapter
        $message  = 'message-to-log';
        $priority = 2;
        $events = array(
            'file' => 'test',
            'line' => 1
        );
        $this->writer->write(array(
            'message'  => $message,
            'priority' => $priority,
            'events'   => $events
        ));
        $this->assertContains('query', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['query']));
        // ...with the correct table and binds for the database
        $binds = array(
            'new-message-field' => $message,
            'new-priority-field' => $priority,
            'new-line' => $events['line'],
            'new-file' => $events['file']
        );
        $this->assertEquals(array($binds), $this->db->calls['execute'][0]);
    }

    public function testShutdownRemovesReferenceToDatabaseInstance()
    {
        $this->writer->write(array('message' => 'this should not fail'));
        $this->writer->shutdown();

        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'Database adapter is null');
        $this->writer->write(array('message' => 'this should fail'));
    }

    public function testWriteDateTimeAsTimestamp()
    {
        $date = new DateTime();
        $event = array('timestamp'=> $date);
        $this->writer->write($event);

        $this->assertContains('query', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['query']));

        $this->assertEquals(array(array(
            'timestamp' => $date->format(FormatterInterface::DEFAULT_DATETIME_FORMAT)
        )), $this->db->calls['execute'][0]);
    }

    public function testWriteDateTimeAsExtraValue()
    {
        $date = new DateTime();
        $event = array(
            'extra'=> array(
                'request_time' => $date
            )
        );
        $this->writer->write($event);

        $this->assertContains('query', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['query']));

        $this->assertEquals(array(array(
            'extra_request_time' => $date->format(FormatterInterface::DEFAULT_DATETIME_FORMAT)
        )), $this->db->calls['execute'][0]);
    }

    public function testConstructWithOptions()
    {
        $formatter = new \Zend\Log\Formatter\Simple();
        $filter    = new \Zend\Log\Filter\Mock();
        $writer = new DbWriter(array(
            'filters'   => $filter,
            'formatter' => $formatter,
            'table'     => $this->tableName,
            'db'        => $this->db,

        ));
        $this->assertInstanceOf('Zend\Log\Writer\Db', $writer);
        $this->assertAttributeEquals($this->tableName, 'tableName', $writer);

        $filters = self::readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertEquals($filter, $filters[0]);
    }
}
