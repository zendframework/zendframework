<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Adobe;

/**
 * This class implements authentication against XML file with roles for Flex Builder.
 *
 * @package    Zend_Amf
 * @subpackage Adobe
 */
class DbInspector
{
    /**
     * Connect to the database
     *
     * @see    Zend_Db::factory()
     * @param  string $dbType Database adapter type for Zend_Db
     * @param  array|object $dbDescription Adapter-specific connection settings
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _connect($dbType, $dbDescription)
    {
        if (is_object($dbDescription)) {
            $dbDescription = get_object_vars($dbDescription);
        }
        return \Zend_Db::factory($dbType, $dbDescription);
    }

    /**
     * Describe database object.
     *
     * Usage example:
     * $inspector->describeTable('Pdo_Mysql',
     *     array(
     *         'host'     => '127.0.0.1',
     *         'username' => 'webuser',
     *         'password' => 'xxxxxxxx',
     *         'dbname'   => 'test'
     *     ),
     *     'mytable'
     * );
     *
     * @see    Zend_Db::describeTable()
     * @see    Zend_Db::factory()
     * @param  string $dbType Database adapter type for Zend_Db
     * @param  array|object $dbDescription Adapter-specific connection settings
     * @param  string $tableName Table name
     * @return array Table description
     */
    public function describeTable($dbType, $dbDescription, $tableName)
    {
        $db = $this->_connect($dbType, $dbDescription);
        return $db->describeTable($tableName);
    }

    /**
     * Test database connection
     *
     * @see    Zend_Db::factory()
     * @param  string $dbType Database adapter type for Zend_Db
     * @param  array|object $dbDescription Adapter-specific connection settings
     * @return bool
     */
    public function connect($dbType, $dbDescription)
    {
        $db = $this->_connect($dbType, $dbDescription);
        $db->listTables();
        return true;
    }

    /**
     * Get the list of database tables
     *
     * @param  string $dbType Database adapter type for Zend_Db
     * @param  array|object $dbDescription Adapter-specific connection settings
     * @return array List of the tables
     */
    public function getTables($dbType, $dbDescription)
    {
        $db = $this->_connect($dbType, $dbDescription);
        return $db->listTables();
    }
}
