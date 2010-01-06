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

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log_Writer_Mock */
require_once 'Zend/Log/Writer/Db.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class Zend_Log_Writer_DbTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tableName = 'db-table-name';

        $this->db     = new Zend_Log_Writer_DbTest_MockDbAdapter();
        $this->writer = new Zend_Log_Writer_Db($this->db, $this->tableName);
    }

    public function testFormattingIsNotSupported()
    {
        try {
            $this->writer->setFormatter(new stdclass);
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/does not support formatting/i', $e->getMessage());
        }
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
        $this->writer = new Zend_Log_Writer_Db($this->db, $this->tableName,
                                                array('new-message-field'  => 'message',
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

        try {
            $this->writer->write(array('message' => 'this should fail'));
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertEquals('Database adapter is null', $e->getMessage());
        }
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

        $logger = Zend_Log::factory($cfg['log']);
        $this->assertTrue($logger instanceof Zend_Log);
    }
}


class Zend_Log_Writer_DbTest_MockDbAdapter
{
    public $calls = array();

    public function __call($method, $params)
    {
        $this->calls[$method][] = $params;
    }

}
