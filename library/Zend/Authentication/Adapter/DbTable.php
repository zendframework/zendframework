<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Authentication\Adapter;

use stdClass;
use Zend\Authentication\Result as AuthenticationResult;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql;
use Zend\Db\Sql\Expression as SqlExpr;
use Zend\Db\Sql\Predicate\Operator as SqlOp;

class DbTable extends AbstractAdapter
{

    /**
     * Database Connection
     *
     * @var DbAdapter
     */
    protected $zendDb = null;

    /**
     * @var Sql\Select
     */
    protected $dbSelect = null;

    /**
     * $tableName - the table name to check
     *
     * @var string
     */
    protected $tableName = null;

    /**
     * $identityColumn - the column to use as the identity
     *
     * @var string
     */
    protected $identityColumn = null;

    /**
     * $credentialColumns - columns to be used as the credentials
     *
     * @var string
     */
    protected $credentialColumn = null;

    /**
     * $credentialTreatment - Treatment applied to the credential, such as MD5() or PASSWORD()
     *
     * @var string
     */
    protected $credentialTreatment = null;

    /**
     * $authenticateResultInfo
     *
     * @var array
     */
    protected $authenticateResultInfo = null;

    /**
     * $resultRow - Results of database authentication query
     *
     * @var array
     */
    protected $resultRow = null;

    /**
     * $ambiguityIdentity - Flag to indicate same Identity can be used with
     * different credentials. Default is FALSE and need to be set to true to
     * allow ambiguity usage.
     *
     * @var bool
     */
    protected $ambiguityIdentity = false;

    /**
     * __construct() - Sets configuration options
     *
     * @param  DbAdapter $zendDb
     * @param  string    $tableName           Optional
     * @param  string    $identityColumn      Optional
     * @param  string    $credentialColumn    Optional
     * @param  string    $credentialTreatment Optional
     * @return \Zend\Authentication\Adapter\DbTable
     */
    public function __construct(DbAdapter $zendDb, $tableName = null, $identityColumn = null,
                                $credentialColumn = null, $credentialTreatment = null)
    {
        $this->zendDb = $zendDb;

        if (null !== $tableName) {
            $this->setTableName($tableName);
        }

        if (null !== $identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }

        if (null !== $credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }

        if (null !== $credentialTreatment) {
            $this->setCredentialTreatment($credentialTreatment);
        }
    }

    /**
     * setTableName() - set the table name to be used in the select query
     *
     * @param  string $tableName
     * @return DbTable Provides a fluent interface
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * setIdentityColumn() - set the column name to be used as the identity column
     *
     * @param  string $identityColumn
     * @return DbTable Provides a fluent interface
     */
    public function setIdentityColumn($identityColumn)
    {
        $this->identityColumn = $identityColumn;
        return $this;
    }

    /**
     * setCredentialColumn() - set the column name to be used as the credential column
     *
     * @param  string $credentialColumn
     * @return DbTable Provides a fluent interface
     */
    public function setCredentialColumn($credentialColumn)
    {
        $this->credentialColumn = $credentialColumn;
        return $this;
    }

    /**
     * setCredentialTreatment() - allows the developer to pass a parametrized string that is
     * used to transform or treat the input credential data.
     *
     * In many cases, passwords and other sensitive data are encrypted, hashed, encoded,
     * obscured, or otherwise treated through some function or algorithm. By specifying a
     * parametrized treatment string with this method, a developer may apply arbitrary SQL
     * upon input credential data.
     *
     * Examples:
     *
     *  'PASSWORD(?)'
     *  'MD5(?)'
     *
     * @param  string $treatment
     * @return DbTable Provides a fluent interface
     */
    public function setCredentialTreatment($treatment)
    {
        $this->credentialTreatment = $treatment;
        return $this;
    }

    /**
     * setAmbiguityIdentity() - sets a flag for usage of identical identities
     * with unique credentials. It accepts integers (0, 1) or boolean (true,
     * false) parameters. Default is false.
     *
     * @param  int|bool $flag
     * @return DbTable Provides a fluent interface
     */
    public function setAmbiguityIdentity($flag)
    {
        if (is_integer($flag)) {
            $this->ambiguityIdentity = (1 === $flag ? true : false);
        } elseif (is_bool($flag)) {
            $this->ambiguityIdentity = $flag;
        }
        return $this;
    }

    /**
     * getAmbiguityIdentity() - returns TRUE for usage of multiple identical
     * identities with different credentials, FALSE if not used.
     *
     * @return bool
     */
    public function getAmbiguityIdentity()
    {
        return $this->ambiguityIdentity;
    }

    /**
     * getDbSelect() - Return the preauthentication Db Select object for userland select query modification
     *
     * @return Sql\Select
     */
    public function getDbSelect()
    {
        if ($this->dbSelect == null) {
            $this->dbSelect = new Sql\Select();
        }
        return $this->dbSelect;
    }

    /**
     * getResultRowObject() - Returns the result row as a stdClass object
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return stdClass|bool
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null)
    {
        if (!$this->resultRow) {
            return false;
        }

        $returnObject = new stdClass();

        if (null !== $returnColumns) {

            $availableColumns = array_keys($this->resultRow);
            foreach ((array) $returnColumns as $returnColumn) {
                if (in_array($returnColumn, $availableColumns)) {
                    $returnObject->{$returnColumn} = $this->resultRow[$returnColumn];
                }
            }
            return $returnObject;

        } elseif (null !== $omitColumns) {

            $omitColumns = (array) $omitColumns;
            foreach ($this->resultRow as $resultColumn => $resultValue) {
                if (!in_array($resultColumn, $omitColumns)) {
                    $returnObject->{$resultColumn} = $resultValue;
                }
            }
            return $returnObject;

        }

        foreach ($this->resultRow as $resultColumn => $resultValue) {
            $returnObject->{$resultColumn} = $resultValue;
        }
        return $returnObject;
    }

    /**
     * This method is called to attempt an authentication. Previous to this
     * call, this adapter would have already been configured with all
     * necessary information to successfully connect to a database table and
     * attempt to find a record matching the provided identity.
     *
     * @throws Exception\RuntimeException if answering the authentication query is impossible
     * @return AuthenticationResult
     */
    public function authenticate()
    {
        $this->_authenticateSetup();
        $dbSelect         = $this->_authenticateCreateSelect();
        $resultIdentities = $this->_authenticateQuerySelect($dbSelect);

        if (($authResult = $this->_authenticateValidateResultSet($resultIdentities)) instanceof AuthenticationResult) {
            return $authResult;
        }

        // At this point, ambiguity is already done. Loop, check and break on success.
        foreach ($resultIdentities as $identity) {
            $authResult = $this->_authenticateValidateResult($identity);
            if ($authResult->isValid()) {
                break;
            }
        }

        return $authResult;
    }

    /**
     * _authenticateSetup() - This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws Exception\RuntimeException in the event that setup was not done properly
     * @return bool
     */
    protected function _authenticateSetup()
    {
        $exception = null;

        if ($this->tableName == '') {
            $exception = 'A table must be supplied for the DbTable authentication adapter.';
        } elseif ($this->identityColumn == '') {
            $exception = 'An identity column must be supplied for the DbTable authentication adapter.';
        } elseif ($this->credentialColumn == '') {
            $exception = 'A credential column must be supplied for the DbTable authentication adapter.';
        } elseif ($this->identity == '') {
            $exception = 'A value for the identity was not provided prior to authentication with DbTable.';
        } elseif ($this->credential === null) {
            $exception = 'A credential value was not provided prior to authentication with DbTable.';
        }

        if (null !== $exception) {
            throw new Exception\RuntimeException($exception);
        }

        $this->authenticateResultInfo = array(
            'code'     => AuthenticationResult::FAILURE,
            'identity' => $this->identity,
            'messages' => array()
        );

        return true;
    }

    /**
     * _authenticateCreateSelect() - This method creates a Zend\Db\Sql\Select object that
     * is completely configured to be queried against the database.
     *
     * @return Sql\Select
     */
    protected function _authenticateCreateSelect()
    {
        // build credential expression
        if (empty($this->credentialTreatment) || (strpos($this->credentialTreatment, '?') === false)) {
            $this->credentialTreatment = '?';
        }

        $credentialExpression = new SqlExpr(
            '(CASE WHEN ?' . ' = ' . $this->credentialTreatment . ' THEN 1 ELSE 0 END) AS ?',
            array($this->credentialColumn, $this->credential, 'zend_auth_credential_match'),
            array(SqlExpr::TYPE_IDENTIFIER, SqlExpr::TYPE_VALUE, SqlExpr::TYPE_IDENTIFIER)
        );

        // get select
        $dbSelect = clone $this->getDbSelect();
        $dbSelect->from($this->tableName)
            ->columns(array('*', $credentialExpression))
            ->where(new SqlOp($this->identityColumn, '=', $this->identity));

        return $dbSelect;
    }

    /**
     * _authenticateQuerySelect() - This method accepts a Zend\Db\Sql\Select object and
     * performs a query against the database with that object.
     *
     * @param  Sql\Select $dbSelect
     * @throws Exception\RuntimeException when an invalid select object is encountered
     * @return array
     */
    protected function _authenticateQuerySelect(Sql\Select $dbSelect)
    {
        $sql = new Sql\Sql($this->zendDb);
        $statement = $sql->prepareStatementForSqlObject($dbSelect);
        try {
            $result = $statement->execute();
            $resultIdentities = array();
            // iterate result, most cross platform way
            foreach ($result as $row) {
                $resultIdentities[] = $row;
            }
        } catch (\Exception $e) {
            throw new Exception\RuntimeException(
                'The supplied parameters to DbTable failed to '
                    . 'produce a valid sql statement, please check table and column names '
                    . 'for validity.', 0, $e
            );
        }
        return $resultIdentities;
    }

    /**
     * _authenticateValidateResultSet() - This method attempts to make
     * certain that only one record was returned in the resultset
     *
     * @param  array $resultIdentities
     * @return bool|\Zend\Authentication\Result
     */
    protected function _authenticateValidateResultSet(array $resultIdentities)
    {

        if (count($resultIdentities) < 1) {
            $this->authenticateResultInfo['code']       = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $this->authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
            return $this->_authenticateCreateAuthResult();
        } elseif (count($resultIdentities) > 1 && false === $this->getAmbiguityIdentity()) {
            $this->authenticateResultInfo['code']       = AuthenticationResult::FAILURE_IDENTITY_AMBIGUOUS;
            $this->authenticateResultInfo['messages'][] = 'More than one record matches the supplied identity.';
            return $this->_authenticateCreateAuthResult();
        }

        return true;
    }

    /**
     * _authenticateValidateResult() - This method attempts to validate that
     * the record in the resultset is indeed a record that matched the
     * identity provided to this adapter.
     *
     * @param  array $resultIdentity
     * @return AuthenticationResult
     */
    protected function _authenticateValidateResult($resultIdentity)
    {
        if ($resultIdentity['zend_auth_credential_match'] != '1') {
            $this->authenticateResultInfo['code']       = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
            $this->authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
            return $this->_authenticateCreateAuthResult();
        }

        unset($resultIdentity['zend_auth_credential_match']);
        $this->resultRow = $resultIdentity;

        $this->authenticateResultInfo['code']       = AuthenticationResult::SUCCESS;
        $this->authenticateResultInfo['messages'][] = 'Authentication successful.';
        return $this->_authenticateCreateAuthResult();
    }

    /**
     * Creates a Zend\Authentication\Result object from the information that
     * has been collected during the authenticate() attempt.
     *
     * @return AuthenticationResult
     */
    protected function _authenticateCreateAuthResult()
    {
        return new AuthenticationResult(
            $this->authenticateResultInfo['code'],
            $this->authenticateResultInfo['identity'],
            $this->authenticateResultInfo['messages']
        );
    }
}
