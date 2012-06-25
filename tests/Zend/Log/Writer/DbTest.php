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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Writer;

use ZendTest\Log\TestAsset\MockDbAdapter;
use ZendTest\Log\TestAsset\MockDbDriver;
use Zend\Log\Writer\Db as DbWriter;
use Zend\Log\Logger;
use Zend\Log\Formatter\Simple as SimpleFormatter;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

    public function testFormattingIsNotSupported()
    {
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'does not support formatting');
        $this->writer->setFormatter(new SimpleFormatter);
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

    /**
     * @group ZF-10089
     */
    public function testThrowStrictSetFormatter()
    {
    	$this->setExpectedException('PHPUnit_Framework_Error');
        $this->writer->setFormatter(new \StdClass());
    }
}
