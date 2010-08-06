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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Log\Writer;

use \Zend\Log\Writer\Db as DbWriter,
    \Zend\Log\Logger;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class DbTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tableName = 'db-table-name';

        $this->db     = new DbTest_MockDbAdapter();
        $this->writer = new DbWriter($this->db, $this->tableName);
    }

    public function testFormattingIsNotSupported()
    {
        $this->setExpectedException('\\Zend\\Log\\Exception', 'does not support formatting');
        $this->writer->setFormatter(new \Zend\Log\Formatter\Simple);
    }

    public function testWriteWithDefaults()
    {
        // log to the mock db adapter
        $fields = array('message'  => 'foo',
                        'priority' => 42);

        $this->writer->write($fields);

        // insert should be called once...
        $this->assertContains('insert', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['insert']));

        // ...with the correct table and binds for the database
        $binds = array('message'  => $fields['message'],
                       'priority' => $fields['priority']);
        $this->assertEquals(array($this->tableName, $binds),
                            $this->db->calls['insert'][0]);
    }

    public function testWriteUsesOptionalCustomColumnNames()
    {
        $this->writer = new DbWriter($this->db, $this->tableName,
                                                array('new-message-field' => 'message',
                                                      'new-message-field' => 'priority'));

        // log to the mock db adapter
        $message  = 'message-to-log';
        $priority = 2;
        $this->writer->write(array('message' => $message, 'priority' => $priority));

        // insert should be called once...
        $this->assertContains('insert', array_keys($this->db->calls));
        $this->assertEquals(1, count($this->db->calls['insert']));

        // ...with the correct table and binds for the database
        $binds = array('new-message-field' => $message,
                       'new-message-field' => $priority);
        $this->assertEquals(array($this->tableName, $binds),
                            $this->db->calls['insert'][0]);
    }

    public function testShutdownRemovesReferenceToDatabaseInstance()
    {
        $this->writer->write(array('message' => 'this should not fail'));
        $this->writer->shutdown();

        $this->setExpectedException('\\Zend\\Log\\Exception', 'Database adapter is null');
        $this->writer->write(array('message' => 'this should fail'));
    }

    public function testFactory()
    {
        $cfg = array('log' => array('memory' => array(
            'writerName'   => "Db",
            'writerParams' => array(
                'db'    => $this->db,
                'table' => $this->tableName,
            ),
        )));

        $logger = Logger::factory($cfg['log']);
        $this->assertTrue($logger instanceof Logger);
    }

    /**
     * @group ZF-10089
     */
    public function testThrowStrictSetFormatter()
    {
    	$this->setExpectedException('PHPUnit_Framework_Error');
//        try {
            $this->writer->setFormatter(new \StdClass());
//        } catch (Exception $e) {
//            $this->assertType('PHPUnit_Framework_Error', $e);
//            $this->assertContains('must implement interface', $e->getMessage());
//        }
    }
}


class DbTest_MockDbAdapter
{
    public $calls = array();

    public function __call($method, $params)
    {
        $this->calls[$method][] = $params;
    }

}
