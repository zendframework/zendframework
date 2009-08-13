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
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Session_SaveHandler_DbTable
 */
require_once 'Zend/Session/SaveHandler/DbTable.php';

/**
 * Unit testing for Zend_Session_SaveHandler_DbTable include all tests for
 * regular session handling
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Session
 * @group      Zend_Db_Table
 */
class Zend_Session_SaveHandler_DbTableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $_saveHandlerTableConfig = array(
        'name'              => 'sessions',
        'primary'           => array(
            'id',
            'save_path',
            'name',
        ),
        Zend_Session_SaveHandler_DbTable::MODIFIED_COLUMN    => 'modified',
        Zend_Session_SaveHandler_DbTable::LIFETIME_COLUMN    => 'lifetime',
        Zend_Session_SaveHandler_DbTable::DATA_COLUMN        => 'data',
        Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT => array(
            Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT_SESSION_ID,
            Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT_SESSION_SAVE_PATH,
            Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT_SESSION_NAME,
        ),
    );

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Array to collect used Zend_Session_SaveHandler_DbTable objects, so they are not
     * destroyed before all tests are done and session is not closed
     *
     * @var array
     */
    protected $_usedSaveHandlers = array();

    /**
     * Setup performed prior to each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_setupDb($this->_saveHandlerTableConfig['primary']);
    }

    /**
     * Tear-down operations performed after each test method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->_dropTable();
    }

    public function testConfigPrimaryAssignmentFullConfig()
    {
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($this->_saveHandlerTableConfig);
        /**
         * @todo Test something other than that an exception is not thrown
         */
    }

    public function testConstructorThrowsExceptionGivenConfigAsNull()
    {
        try {
            $this->_usedSaveHandlers[] =
                $saveHandler = new Zend_Session_SaveHandler_DbTable(null);
            $this->fail('Expected Zend_Session_SaveHandler_Exception not thrown');
        } catch (Zend_Session_SaveHandler_Exception $e) {
            $this->assertContains('$config must be', $e->getMessage());
        }
    }

    public function testTableNameSchema()
    {
        $config = $this->_saveHandlerTableConfig;
        $config['name'] = 'schema.session';
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
    }

    public function testTableEmptyNamePullFromSavePath()
    {
        $config = $this->_saveHandlerTableConfig;
        unset($config['name']);
        try {
            $savePath = ini_get('session.save_path');
            ini_set('session.save_path', dirname(__FILE__));
            $this->_usedSaveHandlers[] =
                $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
            $this->fail();
        } catch (Zend_Session_SaveHandler_Exception $e) {
            ini_set('session.save_path', $savePath);
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testPrimaryAssignmentIdNotSet()
    {
        $this->setExpectedException('Zend_Session_SaveHandler_Exception');
        $config = $this->_saveHandlerTableConfig;
        $config['primary'] = array('id');
        $config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]
            = Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT_SESSION_SAVE_PATH;
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        /**
         * @todo Test something other than that an exception is thrown
         */
    }

    public function testPrimaryAssignmentNotArray()
    {
        $config = $this->_saveHandlerTableConfig;
        $config['primary'] = array('id');
        $config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]
            = Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT_SESSION_ID;
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        /**
         * @todo Test something other than that an exception is not thrown
         */
    }

    public function testModifiedColumnNotSet()
    {
        $this->setExpectedException('Zend_Session_SaveHandler_Exception');
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::MODIFIED_COLUMN]);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        /**
         * @todo Test something other than that an exception is thrown
         */
    }

    public function testLifetimeColumnNotSet()
    {
        $this->setExpectedException('Zend_Session_SaveHandler_Exception');
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::LIFETIME_COLUMN]);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        /**
         * @todo Test something other than that an exception is thrown
         */
    }

    public function testDataColumnNotSet()
    {
        $this->setExpectedException('Zend_Session_SaveHandler_Exception');
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::DATA_COLUMN]);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        /**
         * @todo Test something other than that an exception is thrown
         */
    }

    public function testDifferentArraySize()
    {
        //different number of args between primary and primaryAssignment
        try {
            $config = $this->_saveHandlerTableConfig;
            array_pop($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
            $this->_usedSaveHandlers[] =
                $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
            $this->fail();
        } catch (Zend_Session_SaveHandler_Exception $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testEmptyPrimaryAssignment()
    {
        //test the default - no primaryAssignment
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = $config['primary'][0];
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        /**
         * @todo Test something other than that an exception is not thrown
         */
    }

    public function testSessionIdPresent()
    {
        //test that the session Id must be in the primary assignment config
        try {
            $config = $this->_saveHandlerTableConfig;
            $config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT] = array(
                Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT_SESSION_NAME,
            );
            $this->_usedSaveHandlers[] =
                $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
            $this->fail();
        } catch (Zend_Session_SaveHandler_Exception $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testModifiedColumnDefined()
    {
        //test the default - no primaryAssignment
        try {
            $config = $this->_saveHandlerTableConfig;
            unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
            unset($config[Zend_Session_SaveHandler_DbTable::MODIFIED_COLUMN]);
            $this->_usedSaveHandlers[] =
                $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
            $this->fail();
        } catch (Zend_Session_SaveHandler_Exception $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testLifetimeColumnDefined()
    {
        //test the default - no primaryAssignment
        try {
            $config = $this->_saveHandlerTableConfig;
            unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
            unset($config[Zend_Session_SaveHandler_DbTable::LIFETIME_COLUMN]);
            $this->_usedSaveHandlers[] =
                $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
            $this->fail();
        } catch (Zend_Session_SaveHandler_Exception $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testDataColumnDefined()
    {
        //test the default - no primaryAssignment
        try {
            $config = $this->_saveHandlerTableConfig;
            unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
            unset($config[Zend_Session_SaveHandler_DbTable::DATA_COLUMN]);
            $this->_usedSaveHandlers[] =
                $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
            $this->fail();
        } catch (Zend_Session_SaveHandler_Exception $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testLifetime()
    {
        $config = $this->_saveHandlerTableConfig;
        unset($config['lifetime']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        $this->assertSame($saveHandler->getLifetime(), (int) ini_get('session.gc_maxlifetime'),
            'lifetime must default to session.gc_maxlifetime'
        );

        $config = $this->_saveHandlerTableConfig;
        $lifetime = $config['lifetime'] = 1242;
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        $this->assertSame($lifetime, $saveHandler->getLifetime());
    }

    public function testOverrideLifetime()
    {
        try {
            $config = $this->_saveHandlerTableConfig;
            $config['overrideLifetime'] = true;
            $this->_usedSaveHandlers[] =
                $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        } catch (Zend_Session_SaveHandler_Exception $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }

        $this->assertTrue($saveHandler->getOverrideLifetime(), '');
    }

    public function testSessionSaving()
    {
        $this->_dropTable();

        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);

        $this->_setupDb($config['primary']);

        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        Zend_Session::setSaveHandler($saveHandler);
        Zend_Session::start();

        /**
         * @see Zend_Session_Namespace
         */
        require_once 'Zend/Session/Namespace.php';

        $session = new Zend_Session_Namespace('SaveHandler');
        $session->testArray = $this->_saveHandlerTableConfig;

        $tmp = array('SaveHandler' => serialize(array('testArray' => $this->_saveHandlerTableConfig)));
        $testAgainst = '';
        foreach ($tmp as $key => $val) {
            $testAgainst .= $key . "|" . $val;
        }

        session_write_close();

        foreach ($this->_db->query('SELECT * FROM Sessions')->fetchAll() as $row) {
            $this->assertSame($row[$config[Zend_Session_SaveHandler_DbTable::DATA_COLUMN]],
                $testAgainst, 'Data was not saved properly'
            );
        }
    }

    public function testReadWrite()
    {
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $this->_setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertSame($config, unserialize($saveHandler->read($id)));
    }

    public function testReadWriteComplex()
    {
        $config = $this->_saveHandlerTableConfig;
        $this->_setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        $saveHandler->open('savepath', 'sessionname');

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertSame($config, unserialize($saveHandler->read($id)));
    }

    public function testReadWriteTwice()
    {
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $this->_setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertSame($config, unserialize($saveHandler->read($id)));

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertSame($config, unserialize($saveHandler->read($id)));
    }

    public function testReadWriteTwiceAndExpire()
    {
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $config['lifetime'] = 1;

        $this->_setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertSame($config, unserialize($saveHandler->read($id)));

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        sleep(2);

        $this->assertSame(false, unserialize($saveHandler->read($id)));
    }

    public function testReadWriteThreeTimesAndGc()
    {
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $config['lifetime'] = 1;

        $this->_setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);

        $id = 242;

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertSame($config, unserialize($saveHandler->read($id)));

        $id++;
        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertSame($config, unserialize($saveHandler->read($id)));

        $id++;
        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertSame($config, unserialize($saveHandler->read($id)));

        foreach ($this->_db->query('SELECT * FROM Sessions')->fetchAll() as $row) {
            $this->assertSame($config, unserialize($row['data']));
        }

        sleep(2);

        $saveHandler->gc(false);

        foreach ($this->_db->query('SELECT * FROM Sessions')->fetchAll() as $row) {
            //should be empty!
            $this->fail();
        }
    }

    public function testSetLifetime()
    {
        $config = $this->_saveHandlerTableConfig;
        unset($config[Zend_Session_SaveHandler_DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $config['lifetime'] = 1;

        $this->_setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable($config);
        $this->assertSame(1, $saveHandler->getLifetime());

        $saveHandler->setLifetime(27);

        $this->assertSame(27, $saveHandler->getLifetime());
    }

    public function testZendConfig()
    {
        $this->_usedSaveHandlers[] =
            $saveHandler = new Zend_Session_SaveHandler_DbTable(new Zend_Config($this->_saveHandlerTableConfig));
        /**
         * @todo Test something other than that an exception is not thrown
         */
    }

    /**
     * Sets up the database connection and creates the table for session data
     *
     * @param  array $primary
     * @return void
     */
    protected function _setupDb(array $primary)
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension must be available and enabled for this test');
        }

        $this->_db = Zend_Db::factory('Pdo_Sqlite', array('dbname' => ':memory:'));
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_db);
        $query = array();
        $query[] = 'CREATE TABLE `Sessions` ( ';
        $query[] = '`id` varchar(32) NOT NULL, ';
        if (in_array('save_path', $primary)) {
            $query[] = '`save_path` varchar(32) NOT NULL, ';
        }
        if (in_array('name', $primary)) {
            $query[] = '`name` varchar(32) NOT NULL, ';
        }
        $query[] = '`modified` int(11) default NULL, ';
        $query[] = '`lifetime` int(11) default NULL, ';
        $query[] = '`data` text, ';
        $query[] = 'PRIMARY KEY  (' . implode(', ', $primary) . ') ';
        $query[] = ');';
        $this->_db->query(implode("\n", $query));
    }

    /**
     * Drops the database table for session data
     *
     * @return void
     */
    protected function _dropTable()
    {
        $this->_db->query('DROP TABLE Sessions');
    }
}

/**
 * This class is used by Zend_Session_SaveHandler_AllTests to produce one skip message when pdo_sqlite is unavailable
 */
class Zend_Session_SaveHandler_DbTableTestSkip extends PHPUnit_Framework_TestCase
{
    public function testNothing()
    {
        $this->markTestSkipped('The pdo_sqlite extension must be available and enabled for this test');
    }
}
