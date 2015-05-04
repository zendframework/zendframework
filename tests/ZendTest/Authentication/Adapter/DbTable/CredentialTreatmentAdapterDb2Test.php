<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Authentication\Adapter\DbTable;

use Zend\Authentication;
use Zend\Authentication\Adapter;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * @group Zend_Auth
 * @group Zend_Db_Table
 */
class CredentialTreatmentAdapterDb2Test extends \PHPUnit_Framework_TestCase
{
    /**
     * IbmDb2 database connection
     *
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $db = null;

    /**
     * Database table authentication adapter
     *
     * @var \Zend\Authentication\Adapter\DbTable
     */
    protected $adapter = null;

    /**
     * Database adapter configuration
     */
    protected $dbAdapterParams = array(
        'driver'           => 'IbmDb2',
        'dbname'           => TESTS_ZEND_AUTH_ADAPTER_DBTABLE_DB2_DATABASE,
        'username'         => TESTS_ZEND_AUTH_ADAPTER_DBTABLE_DB2_USERNAME,
        'password'         => TESTS_ZEND_AUTH_ADAPTER_DBTABLE_DB2_PASSWORD,
        'platform_options' => array('quote_identifiers' => false),
        'driver_options'   => array(),
    );

    /**
     * DB2 table to use for testing
     *
     * @var string in the format 'LIBRARY_NAME.TABLE_NAME' or
     */
    protected $tableName = TESTS_ZEND_AUTH_ADAPTER_DBTABLE_DB2_CREDENTIAL_TABLE;

    /**
     * Set up test configuration
     */
    public function setUp()
    {
        if (!defined('TESTS_ZEND_AUTH_ADAPTER_DBTABLE_DB2_ENABLED')
            || constant('TESTS_ZEND_AUTH_ADAPTER_DBTABLE_DB2_ENABLED') === false
        ) {
            $this->markTestSkipped('Tests are not enabled in TestConfiguration.php');
        }

        if (! extension_loaded('ibm_db2')) {
            $this->markTestSkipped('ibm_db2 extension is not loaded');
        }

        $this->dbAdapterParams['driver_options']['i5_commit'] = constant('DB2_I5_TXN_NO_COMMIT');
        $this->dbAdapterParams['driver_options']['i5_naming'] = constant('DB2_I5_NAMING_OFF');

        $this->setupDbAdapter();
        $this->setupAuthAdapter();
    }

    public function tearDown()
    {
        $this->authAdapter = null;
        if ($this->db instanceof DbAdapter) {
            // BIND, REBIND or DROP operations fail when the package is in use
            // by the same application process
            $this->db->getDriver()
                ->getConnection()
                ->disconnect();

            $this->db = new DbAdapter($this->dbAdapterParams);

            $this->db->query("DROP TABLE {$this->tableName}", DbAdapter::QUERY_MODE_EXECUTE);
            $this->db->getDriver()
                ->getConnection()
                ->disconnect();
        }
        $this->db = null;
    }

    /**
     * Ensures expected behavior for authentication success
     */
    public function testAuthenticateSuccess()
    {
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password');
        $result = $this->authAdapter->authenticate();
        $this->assertTrue($result->isValid());
    }

    /**
     * Ensures expected behavior for authentication success
     */
    public function testAuthenticateSuccessWithTreatment()
    {
        $this->authAdapter = new Adapter\DbTable($this->db, $this->tableName, 'username', 'password', '?');
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password');
        $result = $this->authAdapter->authenticate();
        $this->assertTrue($result->isValid());
    }

    /**
     * Ensures expected behavior for for authentication failure
     * reason: Identity not found.
     */
    public function testAuthenticateFailureIdentityNotFound()
    {
        $this->authAdapter->setIdentity('non_existent_username');
        $this->authAdapter->setCredential('my_password');

        $result = $this->authAdapter->authenticate();
        $this->assertEquals(Authentication\Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
    }

    /**
     * Ensures expected behavior for for authentication failure
     * reason: Identity ambiguous.
     */
    public function testAuthenticateFailureIdentityAmbiguous()
    {
        $sqlInsert = "INSERT INTO {$this->tableName} (id, username, password, real_name) VALUES (2, 'my_username', 'my_password', 'My Real Name')";
        $this->db->query($sqlInsert, DbAdapter::QUERY_MODE_EXECUTE);

        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password');

        $result = $this->authAdapter->authenticate();
        $this->assertEquals(Authentication\Result::FAILURE_IDENTITY_AMBIGUOUS, $result->getCode());
    }

    /**
     * Ensures expected behavior for authentication failure because of a bad password
     */
    public function testAuthenticateFailureInvalidCredential()
    {
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password_bad');
        $result = $this->authAdapter->authenticate();
        $this->assertFalse($result->isValid());
    }

    /**
     * Ensures that getResultRowObject() works for successful authentication
     */
    public function testGetResultRow()
    {
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password');
        $this->authAdapter->authenticate();
        $resultRow = $this->authAdapter->getResultRowObject();
        // Since we did not set db2_attr_case, column name is upper case, as expected
        $this->assertEquals($resultRow->USERNAME, 'my_username');
    }

    /**
     * Ensure that ResultRowObject returns only what told to be included
     */
    public function testGetSpecificResultRow()
    {
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password');
        $this->authAdapter->authenticate();
        // Since we did not set db2_attr_case, column names will be upper case, as expected
        $resultRow = $this->authAdapter->getResultRowObject(array(
            'USERNAME',
            'REAL_NAME'
        ));
        $this->assertEquals(
            'O:8:"stdClass":2:{s:8:"USERNAME";s:11:"my_username";s:9:"REAL_NAME";s:12:"My Real Name";}', serialize($resultRow)
        );
    }

    /**
     * Ensure that ResultRowObject returns an object that has specific omissions
     */
    public function testGetOmittedResultRow()
    {
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password');
        $this->authAdapter->authenticate();
        // Since we did not set db2_attr_case, column names will be upper case, as expected
        $resultRow = $this->authAdapter->getResultRowObject(null, 'PASSWORD');
        $this->assertEquals(
            'O:8:"stdClass":3:{s:2:"ID";i:1;s:8:"USERNAME";s:11:"my_username";s:9:"REAL_NAME";s:12:"My Real Name";}', serialize($resultRow)
        );
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterCanReturnDbSelectObject()
    {
        $this->assertInstanceOf('Zend\Db\Sql\Select', $this->authAdapter->getDbSelect());
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterCanUseModifiedDbSelectObject()
    {
        $select = $this->authAdapter->getDbSelect();
        $select->where('1 = 0');
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password');

        $result = $this->authAdapter->authenticate();
        $this->assertEquals(Authentication\Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterReturnsASelectObjectWithoutAuthTimeModificationsAfterAuth()
    {
        $select = $this->authAdapter->getDbSelect();
        $select->where('1 = 1');
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->setCredential('my_password');
        $this->authAdapter->authenticate();
        $selectAfterAuth = $this->authAdapter->getDbSelect();
        $whereParts = $selectAfterAuth->where->getPredicates();
        $this->assertEquals(1, count($whereParts));

        $lastWherePart = array_pop($whereParts);
        $expressionData = $lastWherePart[1]->getExpressionData();
        $this->assertEquals('1 = 1', $expressionData[0][0]);
    }

    /**
     * Ensure that exceptions are caught
     */
    public function testCatchExceptionNoTable()
    {
        $this->setExpectedException('Zend\Authentication\Adapter\DbTable\Exception\RuntimeException', 'A table must be supplied for');
        $adapter = new Adapter\DbTable($this->db);
        $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are thrown
     */
    public function testCatchExceptionNoIdentityColumn()
    {
        $this->setExpectedException(
            'Zend\Authentication\Adapter\DbTable\Exception\RuntimeException',
            'An identity column must be supplied for the'
        );
        $adapter = new Adapter\DbTable($this->db, 'users');
        $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are thrown
     */
    public function testCatchExceptionNoCredentialColumn()
    {
        $this->setExpectedException(
            'Zend\Authentication\Adapter\DbTable\Exception\RuntimeException',
            'A credential column must be supplied'
        );
        $adapter = new Adapter\DbTable($this->db, 'users', 'username');
        $adapter->authenticate();
    }

    /**
     * Ensure that exceptions are thrown
     */
    public function testCatchExceptionNoIdentity()
    {
        $this->setExpectedException(
            'Zend\Authentication\Adapter\DbTable\Exception\RuntimeException',
            'A value for the identity was not provided prior'
        );
        $this->authAdapter->authenticate();
    }

    /**
     * Ensure that exceptions are thrown
     */
    public function testCatchExceptionNoCredential()
    {
        $this->setExpectedException(
            'Zend\Authentication\Adapter\DbTable\Exception\RuntimeException',
            'A credential value was not provided prior'
        );
        $this->authAdapter->setIdentity('my_username');
        $this->authAdapter->authenticate();
    }

    /**
     * Ensure that exceptions are thrown
     */
    public function testCatchExceptionBadSql()
    {
        $this->setExpectedException(
            'Zend\Authentication\Adapter\DbTable\Exception\RuntimeException',
            'The supplied parameters to'
        );
        $this->authAdapter->setTableName('bad_table_name');
        $this->authAdapter->setIdentity('value');
        $this->authAdapter->setCredential('value');
        $this->authAdapter->authenticate();
    }

    /**
     * Test to see same usernames with different passwords can not authenticate
     * when flag is not set.
     * This is the current state of
     * Zend_Auth_Adapter_DbTable (up to ZF 1.10.6)
     *
     * @group ZF-7289
     */
    public function testEqualUsernamesDifferentPasswordShouldNotAuthenticateWhenFlagIsNotSet()
    {
        $sqlInsert = "INSERT INTO $this->tableName (id, username, password, real_name) "
                   . "VALUES (2, 'my_username', 'my_otherpass', 'Test user 2')";
        $this->db->query($sqlInsert, DbAdapter::QUERY_MODE_EXECUTE);

        // test if user 1 can authenticate
        $this->authAdapter->setIdentity('my_username')->setCredential('my_password');
        $result = $this->authAdapter->authenticate();
        $this->assertContains('More than one record matches the supplied identity.', $result->getMessages());
        $this->assertFalse($result->isValid());
    }

    /**
     * Test to see same usernames with different passwords can authenticate when
     * a flag is set
     *
     * @group ZF-7289
     */
    public function testEqualUsernamesDifferentPasswordShouldAuthenticateWhenFlagIsSet()
    {
        $sqlInsert = "INSERT INTO $this->tableName (id, username, password, real_name) "
                   . "VALUES (2, 'my_username', 'my_otherpass', 'Test user 2')";
        $this->db->query($sqlInsert, DbAdapter::QUERY_MODE_EXECUTE);

        // test if user 1 can authenticate
        $this->authAdapter->setIdentity('my_username')
            ->setCredential('my_password')
            ->setAmbiguityIdentity(true);
        $result = $this->authAdapter->authenticate();
        $this->assertNotContains('More than one record matches the supplied identity.', $result->getMessages());
        $this->assertTrue($result->isValid());
        $this->assertEquals('my_username', $result->getIdentity());

        $this->authAdapter = null;
        $this->setupAuthAdapter();

        // test if user 2 can authenticate
        $this->authAdapter->setIdentity('my_username')
            ->setCredential('my_otherpass')
            ->setAmbiguityIdentity(true);
        $result2 = $this->authAdapter->authenticate();
        $this->assertNotContains('More than one record matches the supplied identity.', $result->getMessages());
        $this->assertTrue($result2->isValid());
        $this->assertEquals('my_username', $result2->getIdentity());
    }

    protected function setupDbAdapter($optionalParams = array())
    {
        $this->createDbAdapter($optionalParams);

        $sqlInsert = "INSERT INTO $this->tableName (id, username, password, real_name) "
                   . "VALUES (1, 'my_username', 'my_password', 'My Real Name')";

        $this->db->query($sqlInsert, DbAdapter::QUERY_MODE_EXECUTE);
    }

    protected function createDbAdapter($optionalParams = array())
    {
        if (! empty($optionalParams)) {
            $this->dbAdapterParams['options'] = $optionalParams;
        }

        $this->db = new DbAdapter($this->dbAdapterParams);

        $sqlCreate = "CREATE TABLE {$this->tableName} ( "
                   . 'id INTEGER NOT NULL, '
                   . 'username VARCHAR(50) NOT NULL, '
                   . 'password VARCHAR(32), '
                   . 'real_name VARCHAR(150), '
                   . 'PRIMARY KEY(id))';

        $this->db->query($sqlCreate, DbAdapter::QUERY_MODE_EXECUTE);
    }

    protected function setupAuthAdapter()
    {
        $this->authAdapter = new Adapter\DbTable\CredentialTreatmentAdapter(
            $this->db, $this->tableName, 'username', 'password'
        );
    }
}
