<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\Adapter;

use Zend\Authentication;
use Zend\Authentication\Adapter;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql\Select as DBSelect;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @group      Zend_Auth
 * @group      Zend_Db_Table
 */
class DbTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * SQLite database connection
     *
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $_db = null;

    /**
     * Database table authentication adapter
     *
     * @var \Zend\Authentication\Adapter\DbTable
     */
    protected $_adapter = null;

    /**
     * Set up test configuration
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
        if ($this->_db instanceof DbAdapter) {
            $this->_db->query('DROP TABLE [users]');
        }
        $this->_db = null;
    }

    /**
     * Ensures expected behavior for authentication success
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
     */
    public function testAuthenticateFailureIdentityNotFound()
    {
        $this->_adapter->setIdentity('non_existent_username');
        $this->_adapter->setCredential('my_password');

        $result = $this->_adapter->authenticate();
        $this->assertEquals(Authentication\Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
    }

    /**
     * Ensures expected behavior for for authentication failure
     * reason: Identity not found.
     */
    public function testAuthenticateFailureIdentityAmbiguous()
    {
        $sqlInsert = 'INSERT INTO users (username, password, real_name) VALUES ("my_username", "my_password", "My Real Name")';
        $this->_db->query($sqlInsert, DbAdapter::QUERY_MODE_EXECUTE);

        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');

        $result = $this->_adapter->authenticate();
        $this->assertEquals(Authentication\Result::FAILURE_IDENTITY_AMBIGUOUS, $result->getCode());
    }

    /**
     * Ensures expected behavior for authentication failure because of a bad password
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
     */
    public function testGetResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject();
        $this->assertEquals($resultRow->username, 'my_username');
    }

    /**
     * Ensure that ResultRowObject returns only what told to be included
     */
    public function testGetSpecificResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject(array('username', 'real_name'));
        $this->assertEquals('O:8:"stdClass":2:{s:8:"username";s:11:"my_username";s:9:"real_name";s:12:"My Real Name";}',
                            serialize($resultRow));
    }

    /**
     * Ensure that ResultRowObject returns an object has specific omissions
     */
    public function testGetOmittedResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject(null, 'password');
        $this->assertEquals('O:8:"stdClass":3:{s:2:"id";s:1:"1";s:8:"username";s:11:"my_username";s:9:"real_name";s:12:"My Real Name";}',
                            serialize($resultRow));
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
        $select = $this->_adapter->getDbSelect();
        $select->where('1 = 0');
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');

        $result = $this->_adapter->authenticate();
        $this->assertEquals(Authentication\Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
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
        $whereParts      = $selectAfterAuth->where->getPredicates();
        $this->assertEquals(1, count($whereParts));

        $lastWherePart  = array_pop($whereParts);
        $expressionData = $lastWherePart[1]->getExpressionData();
        $this->assertEquals('1 = 1', $expressionData[0][0]);
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoTable()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException',
                                    'A table must be supplied for');
        $adapter = new Adapter\DbTable($this->_db);
        $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoIdentityColumn()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException',
                                    'An identity column must be supplied for the');
        $adapter = new Adapter\DbTable($this->_db, 'users');
        $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoCredentialColumn()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException',
                                    'A credential column must be supplied');
        $adapter = new Adapter\DbTable($this->_db, 'users', 'username');
        $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoIdentity()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException',
                                    'A value for the identity was not provided prior');
        $this->_adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoCredential()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException',
                                    'A credential value was not provided prior');
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->authenticate();
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionBadSql()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\Exception\RuntimeException',
                                    'The supplied parameters to');
        $this->_adapter->setTableName('bad_table_name');
        $this->_adapter->setIdentity('value');
        $this->_adapter->setCredential('value');
        $this->_adapter->authenticate();
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
        $sqlInsert = 'INSERT INTO users (username, password, real_name) '
                   . 'VALUES ("my_username", "my_otherpass", "Test user 2")';
        $this->_db->query($sqlInsert, DbAdapter::QUERY_MODE_EXECUTE);

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
        $sqlInsert = 'INSERT INTO users (username, password, real_name) '
                   . 'VALUES ("my_username", "my_otherpass", "Test user 2")';
        $this->_db->query($sqlInsert, DbAdapter::QUERY_MODE_EXECUTE);

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
        $params = array('driver' => 'pdo_sqlite',
                        'dbname' => TESTS_ZEND_AUTH_ADAPTER_DBTABLE_PDO_SQLITE_DATABASE);

        if (!empty($optionalParams)) {
            $params['options'] = $optionalParams;
        }

        $this->_db = new DbAdapter($params);

        $sqlCreate = 'CREATE TABLE IF NOT EXISTS [users] ( '
                   . '[id] INTEGER  NOT NULL PRIMARY KEY, '
                   . '[username] VARCHAR(50) NOT NULL, '
                   . '[password] VARCHAR(32) NULL, '
                   . '[real_name] VARCHAR(150) NULL)';
        $this->_db->query($sqlCreate, DbAdapter::QUERY_MODE_EXECUTE);

        $sqlDelete = 'DELETE FROM users';
        $this->_db->query($sqlDelete, DbAdapter::QUERY_MODE_EXECUTE);

        $sqlInsert = 'INSERT INTO users (username, password, real_name) '
                   . 'VALUES ("my_username", "my_password", "My Real Name")';
        $this->_db->query($sqlInsert, DbAdapter::QUERY_MODE_EXECUTE);
    }

    protected function _setupAuthAdapter()
    {
        $this->_adapter = new Adapter\DbTable($this->_db, 'users', 'username', 'password');
    }

}
