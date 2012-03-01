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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Session\SaveHandler;

use Zend\Session\SaveHandler\DbTable,
    Zend\Session\Exception as SaveHandlerException,
    Zend\Session\Manager,
    Zend\Db\Db,
    Zend\Db\Adapter\AbstractAdapter,
    Zend\Db\Table\AbstractTable,
    Zend\Config\Config,
    ZendTest\Session\TestAsset\TestManager;

/**
 * Unit testing for DbTable include all tests for
 * regular session handling
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Session
 * @group      Zend_Db_Table
 */
class DbTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\Config\Config
     */
    protected $saveHandlerTableConfig = array(
        'name'              => 'sessions',
        'primary'           => array(
            'id',
            'save_path',
            'name',
        ),
        DbTable::MODIFIED_COLUMN    => 'modified',
        DbTable::LIFETIME_COLUMN    => 'lifetime',
        DbTable::DATA_COLUMN        => 'data',
        DbTable::PRIMARY_ASSIGNMENT => array(
            DbTable::PRIMARY_ASSIGNMENT_SESSION_ID,
            DbTable::PRIMARY_ASSIGNMENT_SESSION_SAVE_PATH,
            DbTable::PRIMARY_ASSIGNMENT_SESSION_NAME,
        ),
    );

    /**
     * @var Zend\Db\Adapter\AbstractAdapter
     */
    protected $_db;

    /**
     * Array to collect used DbTable objects, so they are not
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
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Zend\Session\SaveHandler\DbTable tests are not enabled due to missing PDO_Sqlite extension');
        }

        $this->saveHandlerTableConfig = new Config(array(
            'name'              => 'sessions',
            'primary'           => array(
                'id',
                'save_path',
                'name',
            ),
            DbTable::MODIFIED_COLUMN    => 'modified',
            DbTable::LIFETIME_COLUMN    => 'lifetime',
            DbTable::DATA_COLUMN        => 'data',
            DbTable::PRIMARY_ASSIGNMENT => array(
                DbTable::PRIMARY_ASSIGNMENT_SESSION_ID,
                DbTable::PRIMARY_ASSIGNMENT_SESSION_SAVE_PATH,
                DbTable::PRIMARY_ASSIGNMENT_SESSION_NAME,
            ),
        ), true);
        
        // $this->markTestSkipped('Skipped until Zend\Db is refactored, this tests assumptions are generally bad, more assertions are needed');

        $this->manager = $manager = new TestManager();
        $this->saveHandlerTableConfig['manager'] = $this->manager;
        $this->setupDb($this->saveHandlerTableConfig['primary']);
    }

    /**
     * Tear-down operations performed after each test method
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->_db instanceof AbstractAdapter) {
            $this->dropTable();
        }
    }

    public function testConfigPrimaryAssignmentFullConfig()
    {
        $sh = new DbTable($this->saveHandlerTableConfig);
        $this->assertInstanceOf('Zend\Db\Table\AbstractTable', $sh);
    }

    public function testTableNameSchema()
    {
        $config = $this->saveHandlerTableConfig;
        $config['name'] = 'schema.session';
        $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
    }

    public function testPrimaryAssignmentIdNotSet()
    {
        $config = $this->saveHandlerTableConfig;
        $config['primary'] = array('id');
        $config[DbTable::PRIMARY_ASSIGNMENT]
            = DbTable::PRIMARY_ASSIGNMENT_SESSION_SAVE_PATH;
            
        $this->setExpectedException(
        	'Zend\Session\Exception\RuntimeException',
        	'Value for configuration option \'primaryAssignment\' must have an assignment for the session id (\'sessionId\')'
            );
        $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
        /**
         * @todo Test something other than that an exception is thrown
         */
    }

    public function testPrimaryAssignmentNotArray()
    {
        $config = $this->saveHandlerTableConfig;
        $config['primary'] = array('id');
        $config[DbTable::PRIMARY_ASSIGNMENT]
            = DbTable::PRIMARY_ASSIGNMENT_SESSION_ID;
        $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
        /**
         * @todo Test something other than that an exception is not thrown
         */
    }

    public function testModifiedColumnNotSet()
    {
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::MODIFIED_COLUMN]);
        $this->setExpectedException(
            'Zend\Session\Exception\RuntimeException',
       	    'Configuration must define \'modifiedColumn\' which names the session table last modification time column'
            );
        $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
        /**
         * @todo Test something other than that an exception is thrown
         */
    }

    public function testLifetimeColumnNotSet()
    {
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::LIFETIME_COLUMN]);
        
        $this->setExpectedException(
            'Zend\Session\Exception\RuntimeException',
            'Configuration must define \'lifetimeColumn\' which names the session table lifetime column'
            );
        $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
    }

    public function testDataColumnNotSet()
    {
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::DATA_COLUMN]);
        
        $this->setExpectedException(
            'Zend\Session\Exception\RuntimeException',
            'Configuration must define \'dataColumn\' which names the session table data column'
            );
        $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
    }

    public function testDifferentArraySize()
    {
        //different number of args between primary and primaryAssignment
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::PRIMARY_ASSIGNMENT]);
        
        $this->setExpectedException(
            'Zend\Session\Exception\RuntimeException'
        );
        $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
    }

    public function testEmptyPrimaryAssignment()
    {
        //test the default - no primaryAssignment
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = $config['primary'][0];
        $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
    }

    public function testSessionIdPresent()
    {
        //test that the session Id must be in the primary assignment config
        try {
            $config = $this->saveHandlerTableConfig;
            $config[DbTable::PRIMARY_ASSIGNMENT] = array(
                DbTable::PRIMARY_ASSIGNMENT_SESSION_NAME,
            );
            $this->_usedSaveHandlers[] = $saveHandler = new DbTable($config);
            $this->fail();
        } catch (SaveHandlerException $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testModifiedColumnDefined()
    {
        //test the default - no primaryAssignment
        try {
            $config = $this->saveHandlerTableConfig;
            unset($config[DbTable::PRIMARY_ASSIGNMENT]);
            unset($config[DbTable::MODIFIED_COLUMN]);
            $this->_usedSaveHandlers[] =
                $saveHandler = new DbTable($config);
            $this->fail();
        } catch (SaveHandlerException $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testLifetimeColumnDefined()
    {
        //test the default - no primaryAssignment
        try {
            $config = $this->saveHandlerTableConfig;
            unset($config[DbTable::PRIMARY_ASSIGNMENT]);
            unset($config[DbTable::LIFETIME_COLUMN]);
            $this->_usedSaveHandlers[] =
                $saveHandler = new DbTable($config);
            $this->fail();
        } catch (SaveHandlerException $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testDataColumnDefined()
    {
        //test the default - no primaryAssignment
        try {
            $config = $this->saveHandlerTableConfig;
            unset($config[DbTable::PRIMARY_ASSIGNMENT]);
            unset($config[DbTable::DATA_COLUMN]);
            $this->_usedSaveHandlers[] =
                $saveHandler = new DbTable($config);
            $this->fail();
        } catch (SaveHandlerException $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }
    }

    public function testLifetime()
    {
        $config = $this->saveHandlerTableConfig;
        unset($config['lifetime']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);
        $this->assertSame($saveHandler->getLifetime(), (int) ini_get('session.gc_maxlifetime'),
            'lifetime must default to session.gc_maxlifetime'
        );

        $config = $this->saveHandlerTableConfig;
        $lifetime = $config['lifetime'] = 1242;
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);
        $this->assertSame($lifetime, $saveHandler->getLifetime());
    }

    public function testOverrideLifetime()
    {
        try {
            $config = $this->saveHandlerTableConfig;
            $config['overrideLifetime'] = true;
            $this->_usedSaveHandlers[] =
                $saveHandler = new DbTable($config);
        } catch (SaveHandlerException $e) {
            /**
             * @todo Test something other than that an exception is thrown
             */
        }

        $this->assertTrue($saveHandler->getOverrideLifetime(), '');
    }

    public function testSessionSaving()
    {
        $this->dropTable();

        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);

        $this->setupDb($config['primary']);

        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);
        $manager = new TestManager(null, null, $saveHandler);
        $manager->start();

        /**
         * @see Zend_Session_Namespace
         */

        $session = new \Zend\Session\Container('SaveHandler', $manager);
        $session->testArray = $this->saveHandlerTableConfig;

        $tmp = array('SaveHandler' => serialize(array('testArray' => $this->saveHandlerTableConfig)));
        $testAgainst = '';
        foreach ($tmp as $key => $val) {
            $testAgainst .= $key . "|" . $val;
        }

        session_write_close();

        foreach ($this->_db->query('SELECT * FROM Sessions')->fetchAll() as $row) {
            $this->assertSame($row[$config[DbTable::DATA_COLUMN]],
                $testAgainst, 'Data was not saved properly'
            );
        }
    }

    public function testReadWrite()
    {
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $this->setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $data = unserialize($saveHandler->read($id));
        $this->assertEquals($config, $data, 'Expected ' . var_export($config, 1) . "\nbut got: " . var_export($data, 1));
    }

    public function testReadWriteComplex()
    {
        $config = $this->saveHandlerTableConfig;
        $this->setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);
        $saveHandler->open('savepath', 'sessionname');

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertEquals($config, unserialize($saveHandler->read($id)));
    }

    public function testReadWriteTwice()
    {
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $this->setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertEquals($config, unserialize($saveHandler->read($id)));

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertEquals($config, unserialize($saveHandler->read($id)));
    }

    public function testReadWriteTwiceAndExpire()
    {
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $config['lifetime'] = 1;

        $this->setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);

        $id = '242';

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertEquals($config, unserialize($saveHandler->read($id)));

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        sleep(2);

        $this->assertSame(false, unserialize($saveHandler->read($id)));
    }

    public function testReadWriteThreeTimesAndGc()
    {
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $config['lifetime'] = 1;

        $this->setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);

        $id = 242;

        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertEquals($config, unserialize($saveHandler->read($id)));

        $id++;
        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertEquals($config, unserialize($saveHandler->read($id)));

        $id++;
        $this->assertTrue($saveHandler->write($id, serialize($config)));

        $this->assertEquals($config, unserialize($saveHandler->read($id)));

        foreach ($this->_db->query('SELECT * FROM Sessions')->fetchAll() as $row) {
            $this->assertEquals($config, unserialize($row['data']));
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
        $config = $this->saveHandlerTableConfig;
        unset($config[DbTable::PRIMARY_ASSIGNMENT]);
        $config['primary'] = array($config['primary'][0]);
        $config['lifetime'] = 1;

        $this->setupDb($config['primary']);
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($config);
        $this->assertSame(1, $saveHandler->getLifetime());

        $saveHandler->setLifetime(27);

        $this->assertSame(27, $saveHandler->getLifetime());
    }

    public function testZendConfig()
    {
        $this->_usedSaveHandlers[] =
            $saveHandler = new DbTable($this->saveHandlerTableConfig);
        /**
         * @todo Test something other than that an exception is not thrown
         */
    }

    /**
     * Sets up the database connection and creates the table for session data
     *
     * @param  Zend\Config\Config $primary
     * @return void
     */
    protected function setupDb(Config $primary)
    {
        $primary = $primary->toArray();
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension must be available and enabled for this test');
        }

        $this->_db = Db::factory('Pdo\Sqlite', array('dbname' => ':memory:'));
        AbstractTable::setDefaultAdapter($this->_db);
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
    protected function dropTable()
    {
        if (!$this->_db instanceof AbstractAdapter) {
            return;
        }
        $this->_db->query('DROP TABLE Sessions');
    }
}
