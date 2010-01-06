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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * Mock Db adapter for Zend_Validate_Db tests
 *
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Db_MockHasResult extends Zend_Db_Adapter_Abstract
{
    /**
     * Returns an array to emulate a result
     *
     * @param string|Zend_Db_Select $sql An SQL SELECT statement.
     * @param mixed $bind Data to bind into SELECT placeholders.
     * @param mixed                 $fetchMode Override current fetch mode.
     * @return array
     */
    public function fetchRow($sql, $bind = array(), $fetchMode = null)
    {
        return array('one' => 'one');
    }

    /**
     * Override for the constructor
     * @param array $config
     */
    public function __construct($config = null)
    {
        // Do Nothing.
    }

    /**
     * The below methods are un-needed, but need to be implemented for this to
     * be a concrete class
     */
    public function listTables()
    {
        return null;
    }
    public function describeTable($tableName, $schemaName = null)
    {
        return null;
    }
    protected function _connect()
    {
        return null;
    }
    public function isConnected()
    {
        return null;
    }
    public function closeConnection()
    {
        return null;
    }
    public function prepare($sql)
    {
        return null;
    }
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        return null;
    }
    protected function _beginTransaction()
    {
        return null;
    }
    protected function _commit()
    {
        return null;
    }
    protected function _rollBack()
    {
        return null;
    }
    public function setFetchMode($mode)
    {
        return null;
    }
    public function limit($sql, $count, $offset = 0)
    {
        return null;
    }
    public function supportsParameters($type)
    {
        return null;
    }
    public function getServerVersion()
    {
        return null;
    }

}
