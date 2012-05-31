<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace Zend\Service\WindowsAzure;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage Session
 */
class SessionHandler
{
    /**
     * Table storage
     *
     * @var Storage\Table
     */
    protected $_tableStorage;

    /**
     * Session table name
     *
     * @var string
     */
    protected $_sessionTable;

    /**
     * Session table partition
     *
     * @var string
     */
    protected $_sessionTablePartition;

    /**
     * @param Storage\Table $tableStorage          Table storage
     * @param string        $sessionTable          Session table name
     * @param string        $sessionTablePartition Session table partition
     */
    public function __construct(Storage\Table $tableStorage, $sessionTable = 'phpsessions',
                                $sessionTablePartition = 'sessions')
    {
        // Set properties
        $this->_tableStorage          = $tableStorage;
        $this->_sessionTable          = $sessionTable;
        $this->_sessionTablePartition = $sessionTablePartition;
    }

    /**
     * Registers the current session handler as PHP's session handler
     *
     * @return boolean
     */
    public function register()
    {
        return session_set_save_handler(array($this, 'open'),
                                        array($this, 'close'),
                                        array($this, 'read'),
                                        array($this, 'write'),
                                        array($this, 'destroy'),
                                        array($this, 'gc')
        );
    }

    /**
     * Open the session store
     *
     * @return bool
     */
    public function open()
    {
        // Make sure table exists
        $tableExists = $this->_tableStorage->tableExists($this->_sessionTable);
        if (!$tableExists) {
            $this->_tableStorage->createTable($this->_sessionTable);
        }

        // Ok!
        return true;
    }

    /**
     * Close the session store
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * Read a specific session
     *
     * @param int $id Session Id
     * @return string
     */
    public function read($id)
    {
        try {
            $sessionRecord = $this->_tableStorage->retrieveEntityById(
                $this->_sessionTable,
                $this->_sessionTablePartition,
                $id
            );
            return base64_decode($sessionRecord->serializedData);
        }
        catch (\Exception $ex) {
            return '';
        }
    }

    /**
     * Write a specific session
     *
     * @param int    $id             Session Id
     * @param string $serializedData Serialized PHP object
     */
    public function write($id, $serializedData)
    {
        $sessionRecord                 = new Storage\DynamicTableEntity($this->_sessionTablePartition, $id);
        $sessionRecord->sessionExpires = time();
        $sessionRecord->serializedData = base64_encode($serializedData);

        $sessionRecord->setAzurePropertyType('sessionExpires', 'Edm.Int32');

        try {
            $this->_tableStorage->updateEntity($this->_sessionTable, $sessionRecord);
        }
        catch (Exception\RuntimeException $unknownRecord) {
            $this->_tableStorage->insertEntity($this->_sessionTable, $sessionRecord);
        }
    }

    /**
     * Destroy a specific session
     *
     * @param int $id Session Id
     * @return boolean
     */
    public function destroy($id)
    {
        try {
            $sessionRecord = $this->_tableStorage->retrieveEntityById(
                $this->_sessionTable,
                $this->_sessionTablePartition,
                $id
            );
            $this->_tableStorage->deleteEntity($this->_sessionTable, $sessionRecord);

            return true;
        }
        catch (Exception\ExceptionInterface $ex) {
            return false;
        }
    }

    /**
     * Garbage collector
     *
     * @param int $lifeTime Session maximal lifetime
     * @see   session.gc_divisor  100
     * @see   session.gc_maxlifetime 1440
     * @see   session.gc_probability 1
     * @usage Execution rate 1/100 (session.gc_probability/session.gc_divisor)
     * @return boolean
     */
    public function gc($lifeTime)
    {
        try {
            $result = $this->_tableStorage->retrieveEntities($this->_sessionTable,
                                                             'PartitionKey eq \'' . $this->_sessionTablePartition .
                                                             '\' and sessionExpires lt ' . (time() - $lifeTime));
            foreach ($result as $sessionRecord) {
                $this->_tableStorage->deleteEntity($this->_sessionTable, $sessionRecord);
            }
            return true;
        }
        catch (Exception\ExceptionInterface $ex) {
            return false;
        }
    }
}
