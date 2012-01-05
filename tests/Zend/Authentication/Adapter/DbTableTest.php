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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Authentication\Adapter;

use Zend\Authentication\Adapter,
    Zend\Authentication,
    Zend\Db\Db,
    Zend\Db\Adapter\Pdo\Sqlite as SQLiteAdapter,
    Zend\Db\Select as DBSelect;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Auth
 * @group      Zend_Db_Table
 */
class DbTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sqlite database connection
     *
     * @var Zend_Db_Adapter_Pdo_Sqlite
     */
    protected $_db = null;

    /**
     * Database table authentication adapter
     *
     * @var Zend_Auth_Adapter_DbTable
     */
    protected $_adapter = null;

    /**
     * Set up test configuration
     *
     * @return void
     */
    public function setUp()
    {
        if (!defined('TESTS_ZEND_AUTH_ADAPTER_DBTABLE_PDO_SQLITE_ENABLED') ||
            constant('TESTS_ZEND_AUTH_ADAPTER_DBTABLE_PDO_SQLITE_ENABLED') === false
        ) {
            $this->markTestSkipped('Tests are not enabled in TestConfiguration.php');
            return;
        } elseif (!extension_loaded('pdo')) {
            $this->markTestSkipped('PDO extension is not loaded');
            return;
        } elseif (!in_array('sqlite', \PDO::getAvailableDrivers())) {
            $this->markTestSkipped('SQLite PDO driver is not available');
            return;
        }

        $this->_setupDbAdapter();
        $this->_setupAuthAdapter();
    }

    public function tearDown()
    {
        $this->_adapter = null;
        if ($this->_db instanceof Db\Adapter\AbstractAdapter) {
            $this->_db->query('DROP TABLE [users]');
        }
        $this->_db = null;
    }

    /**
     * Ensures expected behavior for authentication success
     *
     * @return void
     */
    public function testAuthenticateSuccess()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertTrue($result->isValid());
    }

    /**
     * Ensures expected behavior for authentication success
     *
     * @return void
     */
    public function testAuthenticateSuccessWithTreatment()
    {
        $this->_adapter = new Adapter\DbTable($this->_db, 'users', 'username', 'password', '?');
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertTrue($result->isValid());
    }

    /**
     * Ensures expected behavior for for authentication failure
     * reason: Identity not found.
     *
     */
    public function testAuthenticateFailureIdentityNotFound()
    {
        $this->_adapter->setIdentity('non_existent_username');
        $this->_adapter->setCredential('my_password');

        try {
            $result = $this->_adapter->authenticate();
            $this->assertEquals(Authentication\Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        } catch (Adapter\Exception\RuntimeException $e) {
            $this->fail('Exception should have been thrown');
        }
    }

    /**
     * Ensures expected behavior for for authentication failure
     * reason: Identity not found.
     *
     */
    public function testAuthenticateFailureIdentityAmbigious()
    {
        $sql_insert = 'INSERT INTO users (username, password, real_name) VALUES ("my_username", "my_password", "My Real Name")';
        $this->_db->query($sql_insert);

        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');

        try {
            $result = $this->_adapter->authenticate();
            $this->assertEquals(Authentication\Result::FAILURE_IDENTITY_AMBIGUOUS, $result->getCode());
        } catch (Adapter\Exception\RuntimeException $e) {
            $this->fail('Exception should have been thrown');
        }
    }

    /**
     * Ensures expected behavior for authentication failure because of a bad password
     *
     * @return void
     */
    public function testAuthenticateFailureInvalidCredential()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password_bad');
        $result = $this->_adapter->authenticate();
        $this->assertFalse($result->isValid());
    }

    /**
     * Ensures that getResultRowObject() works for successful authentication
     *
     * @return void
     */
    public function testGetResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject();
        $this->assertEquals($resultRow->username, 'my_username');
    }

    /**
     * Ensure that ResultRowObject returns only what told to be included
     *
     */
    public function testGetSpecificResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject(array('username', 'real_name'));
        $this->assertEquals('O:8:"stdClass":2:{s:8:"username";s:11:"my_username";s:9:"real_name";s:12:"My Real Name";}', serialize($resultRow));
    }

    /**
     * Ensure that ResultRowObject returns an object has specific omissions
     *
     */
    public function testGetOmittedResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject(null, 'password');
        $this->assertEquals('O:8:"stdClass":3:{s:2:"id";s:1:"1";s:8:"username";s:11:"my_username";s:9:"real_name";s:12:"My Real Name";}', serialize($resultRow));
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterCanReturnDbSelectObject()
    {
        $this->assertTrue($this->_adapter->getDbSelect() instanceof DBSelect);
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterCanUseModifiedDbSelectObject()
    {
        $this->_db->getProfiler()->setEnabled(true);
        $select = $this->_adapter->getDbSelect();
        $select->where('1 = 1');
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_adapter->authenticate();
        $profiler = $this->_db->getProfiler();
        $this->assertEquals(
            'SELECT "users".*, (CASE WHEN "password" = \'my_password\' THEN 1 ELSE 0 END) AS "zend_auth_credential_match" FROM "users" WHERE (1 = 1) AND ("username" = \'my_username\')',
            $profiler->getLastQueryProfile()->getQuery()
            );
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterReturnsASelectObjectWithoutAuthTimeModificationsAfterAuth()
    {
        $select = $this->_adapter->getDbSelect();
        $select->where('1 = 1');
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_adapter->authenticate();
        $selectAfterAuth = $this->_adapter->getDbSelect();
        $whereParts = $selectAfterAuth->getPart(DBSelect::WHERE);
        $this->assertEquals(1, count($whereParts));
        $this->assertEquals('(1 = 1)', array_pop($whereParts));
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoTable()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException', 'A table must be supplied for');
        $adapter = new Adapter\DbTable($this->_db);
        $result = $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoIdentityColumn()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException', 'An identity column must be supplied for the');
        $adapter = new Adapter\DbTable($this->_db, 'users');
        $result = $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoCredentialColumn()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException', 'A credential column must be supplied');
        $adapter = new Adapter\DbTable($this->_db, 'users', 'username');
        $result = $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoIdentity()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException', 'A value for the identity was not provided prior');
        $result = $this->_adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoCredential()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException', 'A credential value was not provided prior');
        $this->_adapter->setIdentity('my_username');
        $result = $this->_adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionBadSql()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException', 'The supplied parameters to');
        $this->_adapter->setTableName('bad_table_name');
        $this->_adapter->setIdentity('value');
        $this->_adapter->setCredential('value');
        $result = $this->_adapter->authenticate();
    }

    /**
     *
     * @group ZF-3068
     */
    public function testDbTableAdapterUsesCaseFolding()
    {
        $this->tearDown();
        $this->_setupDbAdapter(array(Db::CASE_FOLDING => Db::CASE_UPPER));
        $this->_setupAuthAdapter();

        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_db->foldCase(Db::CASE_UPPER);
        $this->_adapter->authenticate();
    }


    /**
     * Test fallback to default database adapter, when no such adapter set
     *
     * @group ZF-7510
     */
    public function testAuthenticateWithDefaultDbAdapterNoAdapterException()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException', 'Null was provided');

        // make sure that no default adapter exists
        \Zend\Db\Table\AbstractTable::setDefaultAdapter(null);
        $this->_adapter = new Adapter\DbTable();
    }

    /**
     * Test fallback to default database adapter
     *
     * @group ZF-7510
     */
    public function testAuthenticateWithDefaultDbAdapter()
    {
        // preserve default adapter between cases
        $tmp = \Zend\Db\Table\AbstractTable::getDefaultAdapter();

        // make sure that default db adapter exists
        \Zend\Db\Table\AbstractTable::setDefaultAdapter($this->_db);

        // check w/o passing adapter
        $this->_adapter = new Adapter\DbTable();
        $this->_adapter
             ->setTableName('users')
             ->setIdentityColumn('username')
             ->setCredentialColumn('password')
             ->setTableName('users')
             ->setIdentity('my_username')
             ->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertTrue($result->isValid());

        // restore adapter
        \Zend\Db\Table\AbstractTable::setDefaultAdapter($tmp);
    }
    /**
     * Test to see same usernames with different passwords can not authenticate
     * when flag is not set. This is the current state of 
     * Zend_Auth_Adapter_DbTable (up to ZF 1.10.6)
     * 
     * @group   ZF-7289
     */
    public function testEqualUsernamesDifferentPasswordShouldNotAuthenticateWhenFlagIsNotSet()
    {
        $this->_db->insert('users', array (
            'username' => 'my_username',
            'password' => 'my_otherpass',
            'real_name' => 'Test user 2',
        ));
        
        // test if user 1 can authenticate
        $this->_adapter->setIdentity('my_username')
                       ->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertTrue(in_array('More than one record matches the supplied identity.',
            $result->getMessages()));
        $this->assertFalse($result->isValid());
    }
    /**
     * Test to see same usernames with different passwords can authenticate when
     * a flag is set
     * 
     * @group   ZF-7289
     */
    public function testEqualUsernamesDifferentPasswordShouldAuthenticateWhenFlagIsSet()
    {
        $this->_db->insert('users', array (
            'username' => 'my_username',
            'password' => 'my_otherpass',
            'real_name' => 'Test user 2',
        ));
        
        // test if user 1 can authenticate
        $this->_adapter->setIdentity('my_username')
                       ->setCredential('my_password')
                       ->setAmbiguityIdentity(true);
        $result = $this->_adapter->authenticate();
        $this->assertFalse(in_array('More than one record matches the supplied identity.',
            $result->getMessages()));
        $this->assertTrue($result->isValid());
        $this->assertEquals('my_username', $result->getIdentity());
        
        $this->_adapter = null;
        $this->_setupAuthAdapter();
        
        // test if user 2 can authenticate
        $this->_adapter->setIdentity('my_username')
                       ->setCredential('my_otherpass')
                       ->setAmbiguityIdentity(true);
        $result2 = $this->_adapter->authenticate();
        $this->assertFalse(in_array('More than one record matches the supplied identity.',
            $result->getMessages()));
        $this->assertTrue($result2->isValid());
        $this->assertEquals('my_username', $result2->getIdentity());
    }


    protected function _setupDbAdapter($optionalParams = array())
    {
        $params = array('dbname' => TESTS_ZEND_AUTH_ADAPTER_DBTABLE_PDO_SQLITE_DATABASE);

        if (!empty($optionalParams)) {
            $params['options'] = $optionalParams;
        }

        $this->_db = new SQLiteAdapter($params);

        $sqlCreate = 'CREATE TABLE [users] ( '
                   . '[id] INTEGER  NOT NULL PRIMARY KEY, '
                   . '[username] VARCHAR(50) NOT NULL, '
                   . '[password] VARCHAR(32) NULL, '
                   . '[real_name] VARCHAR(150) NULL)';
        $this->_db->query($sqlCreate);

        $sqlInsert = 'INSERT INTO users (username, password, real_name) '
                   . 'VALUES ("my_username", "my_password", "My Real Name")';
        $this->_db->query($sqlInsert);
    }

    protected function _setupAuthAdapter()
    {
        $this->_adapter = new Adapter\DbTable($this->_db, 'users', 'username', 'password');
    }
}
