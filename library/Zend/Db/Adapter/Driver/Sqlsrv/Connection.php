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
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Driver\ConnectionInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Connection implements ConnectionInterface
{
    /**
     * @var Sqlsrv
     */
    protected $driver = null;

    /**
     * @var array
     */
    protected $connectionParameters = array();
    
    /**
     * @var resource
     */
    protected $resource = null;

    /**
     * @var bool
     */
    protected $inTransaction = false;
    
    /**
     * Constructor
     * 
     * @param mixed $connectionInfo 
     */
    public function __construct($connectionInfo)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif (is_resource($connectionInfo)) {
            $this->setResource($connectionInfo);
        }
    }
    /**
     * Set driver
     * 
     * @param  Sqlsrv $driver
     * @return Connection 
     */
    public function setDriver(Sqlsrv $driver)
    {
        $this->driver = $driver;
        return $this;
    }
    /**
     * Set connection parameters
     * 
     * @param  array $connectionParameters
     * @return Connection 
     */
    public function setConnectionParameters(array $connectionParameters)
    {
        $this->connectionParameters = $connectionParameters;
        return $this;
    }
    /**
     * Get connection parameters
     * 
     * @return array 
     */
    public function getConnectionParameters()
    {
        return $this->connectionParameters;
    }
    /**
     * Get default catalog
     * 
     * @return null 
     */
    public function getDefaultCatalog()
    {
        return null;
    }
    /**
     * Get dafault schema
     * 
     * @return string 
     */
    public function getDefaultSchema()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $result = sqlsrv_query($this->resource, 'SELECT SCHEMA_NAME()');
        $r = sqlsrv_fetch_array($result);
        return $r[0];
    }
    /**
     * Set resource
     * 
     * @param  resource $resource
     * @return Connection 
     */
    public function setResource($resource)
    {
        if (get_resource_type($resource) !== 'SQL Server Connection') {
            throw new \Exception('Resource provided was not of type SQL Server Connection');
        }
        $this->resource = $resource;
        return $this;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }
    /**
     * Connect
     * 
     * @return null 
     */
    public function connect()
    {
        if ($this->resource) {
            return;
        }
        
        $serverName = '.';
        $params = array();
        foreach ($this->connectionParameters as $cpName => $cpValue) {
            switch (strtolower($cpName)) {
                case 'hostname':
                case 'servername':
                    $serverName = $cpValue;
                    break;
                case 'username':
                case 'uid':
                    $params['UID'] = $cpValue;
                    break;
                case 'password':
                case 'pwd':
                    $params['PWD'] = $cpValue;
                    break;
                case 'database':
                case 'dbname':
                    $params['Database'] = $cpValue;
                    break;
            }
        }

        $this->resource = sqlsrv_connect($serverName, $params);

        if (!$this->resource) {
            $prevErrorException = new ErrorException(sqlsrv_errors());
            throw new \Exception('Connect Error', null, $prevErrorException);
        }

    }
    /**
     * Is connected
     * 
     * @return boolean 
     */
    public function isConnected()
    {
        return (is_resource($this->resource));
    }
    /**
     * Disconnect
     * 
     */
    public function disconnect()
    {
        sqlsrv_close($this->resource);
        unset($this->resource);
    }
    
    /**
     * Begin transaction
     * 
     */
    public function beginTransaction()
    {
        // http://msdn.microsoft.com/en-us/library/cc296151.aspx
        /*
        $this->resource->autocommit(false);
        $this->inTransaction = true;
        */
    }
    /**
     * Commit
     */
    public function commit()
    {
        // http://msdn.microsoft.com/en-us/library/cc296194.aspx
        /*
        if (!$this->resource) {
            $this->connect();
        }
        
        $this->resource->commit();
        
        $this->inTransaction = false;
        */
    }
    /**
     * Rollback 
     */
    public function rollback()
    {
        // http://msdn.microsoft.com/en-us/library/cc296176.aspx
        /*
        if (!$this->resource) {
            throw new \Exception('Must be connected before you can rollback.');
        }
        
        if (!$this->_inCommit) {
            throw new \Exception('Must call commit() before you can rollback.');
        }
        
        $this->resource->rollback();
        return $this;
        */
    }
    
    /**
     * Execute
     * 
     * @param  string $sql
     * @return mixed 
     */
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $returnValue = sqlsrv_query($this->resource, $sql);
        
        // if the returnValue is something other than a Sqlsrv_result, bypass wrapping it
        if ($returnValue === false) {
            $errors = sqlsrv_errors();
            // ignore general warnings
            if ($errors[0]['SQLSTATE'] != '01000') {
                throw new \RuntimeException($errors[0]['message']);
            }
        }

        $result = $this->driver->createResult($returnValue);
        return $result;
    }
    /**
     * Prepare
     * 
     * @param  string $sql
     * @return string 
     */
    public function prepare($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $statement = $this->driver->createStatement($sql);
        return $statement;
    }
    /**
     * Get last generated id
     * 
     * @return mixed 
     */
    public function getLastGeneratedId()
    {
        $sql = 'SELECT SCOPE_IDENTITY() as Current_Identity';
        $result = sqlsrv_query($this->resource, $sql);
        $row = sqlsrv_fetch_array($result);
        return $row['Current_Identity'];
    }

}
    