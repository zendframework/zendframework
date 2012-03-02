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

namespace Zend\Db\Adapter\Driver\Mysqli;

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
     * @var Mysqli
     */
    protected $driver = null;
    /**
     * Connection paramters
     * 
     * @var array 
     */
    protected $connectionParameters = array();
    
    /**
     * @var \mysqli
     */
    protected $resource = null;

    /**
     * In transcaction
     * 
     * @var boolean
     */
    protected $inTransaction = false;    

    /**
     * Constructor
     * 
     * @param mysqli $connectionInfo 
     */
    public function __construct($connectionInfo = null)
    {
        if (is_array($connectionInfo)) {
            $this->setConnectionParameters($connectionInfo);
        } elseif ($connectionInfo instanceof \mysqli) {
            $this->setResource($connectionInfo);
        }
    }

    /**
     * @param Mysqli $driver
     * @return Connection
     */
    public function setDriver(Mysqli $driver)
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
     * Get default schema
     * 
     * @return string 
     */
    public function getDefaultSchema()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        /** @var $result \mysqli_result */
        $result = $this->resource->query('SELECT DATABASE()');
        $r = $result->fetch_row();
        return $r[0];
    }

    /**
     * Set resource
     * 
     * @param  mysqli $resource
     * @return Connection 
     */
    public function setResource(mysqli $resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Get resource
     * 
     * @return \mysqli
     */
    public function getResource()
    {
        $this->connect();
        return $this->resource;
    }
    /**
     * Connect
     * 
     * @return null 
     */
    public function connect()
    {
        if ($this->resource instanceof \mysqli) {
            return;
        }

        // localize
        $p = $this->connectionParameters;

        // given a list of key names, test for existence in $p
        $findParameterValue = function(array $names) use ($p) {
            foreach ($names as $name) {
                if (isset($p[$name])) {
                    return $p[$name];
                }
            }
            return null;
        };

        $hostname = $findParameterValue(array('hostname', 'host'));
        $username = $findParameterValue(array('username', 'user'));
        $password = $findParameterValue(array('password', 'passwd', 'pw'));
        $database = $findParameterValue(array('database', 'dbname', 'db', 'schema'));
        $port     = (isset($p['port'])) ? (int) $p['port'] : null;
        $socket   = (isset($p['socket'])) ? $p['socket'] : null;

        $this->resource = new \Mysqli($hostname, $username, $password, $database, $port, $socket);

        if ($this->resource->connect_error) {
            throw new \Exception('Connect Error (' . $this->resource->connect_errno . ') ' . $this->resource->connect_error);
        }

        if (!empty($p['charset'])) {
            $this->resource->set_charset($this->resource, $p['charset']);
        }

    }
    
    /**
     * Is connected
     * 
     * @return boolean 
     */
    public function isConnected()
    {
        return ($this->resource instanceof \Mysqli);
    }
    
    /**
     * Disconnect
     */
    public function disconnect()
    {
        if ($this->resource instanceof \PDO) {
            $this->resource->close();
        }
        unset($this->resource);
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->resource->autocommit(false);
        $this->inTransaction = true;
    }
    
    /**
     * Commit
     */
    public function commit()
    {
        if (!$this->resource) {
            $this->connect();
        }
        
        $this->resource->commit();
        
        $this->inTransaction = false;
    }
    
    /**
     * Rollback
     * 
     * @return Connection 
     */
    public function rollback()
    {
        if (!$this->resource) {
            throw new \Exception('Must be connected before you can rollback.');
        }
        
        if (!$this->inTransaction) {
            throw new \Exception('Must call commit() before you can rollback.');
        }
        
        $this->resource->rollback();
        return $this;
    }
    
    /**
     * Execute
     * 
     * @param  string $sql
     * @return Result 
     */
    public function execute($sql)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $resultResource = $this->resource->query($sql);

        // if the returnValue is something other than a mysqli_result, bypass wrapping it
        if ($resultResource === false) {
            throw new \Zend\Db\Adapter\Exception\InvalidQueryException($this->resource->error);
        }

        $resultPrototype = $this->driver->createResult(($resultResource === true) ? $this->resource : $resultResource);
        return $resultPrototype;
    }
    /**
     * Get last generated id
     * 
     * @return integer 
     */
    public function getLastGeneratedId()
    {
        return $this->resource->insert_id;
    }
}
    