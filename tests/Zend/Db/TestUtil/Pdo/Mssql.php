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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Db_TestUtil_Pdo_Common
 */

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_TestUtil_Pdo_Mssql extends Zend_Db_TestUtil_Pdo_Common
{

    public function getParams(array $constants = array())
    {
        $constants = array (
            'host'     => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_HOSTNAME',
            'username' => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_USERNAME',
            'password' => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PASSWORD',
            'dbname'   => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_DATABASE',
            'port'     => 'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PORT'
        );

        return parent::getParams($constants);
    }

    public function getSqlType($type)
    {
        if ($type == 'IDENTITY') {
            return 'INT NOT NULL IDENTITY PRIMARY KEY';
        }
        return $type;
    }

    protected function _getColumnsDocuments()
    {
        return array(
            'doc_id'       => 'INTEGER NOT NULL',
            'doc_clob'     => 'VARCHAR(8000)',
            'doc_blob'     => 'VARCHAR(8000)',
            'PRIMARY KEY'  => 'doc_id'
            );
    }

    protected function _getColumnsBugs()
    {
        return array(
            'bug_id'          => 'IDENTITY',
            'bug_description' => 'VARCHAR(100) NULL',
            'bug_status'      => 'VARCHAR(20) NULL',
            'created_on'      => 'DATETIME NULL',
            'updated_on'      => 'DATETIME NULL',
            'reported_by'     => 'VARCHAR(100) NULL',
            'assigned_to'     => 'VARCHAR(100) NULL',
            'verified_by'     => 'VARCHAR(100) NULL'
        );
    }

    protected function _getSqlCreateTable($tableName)
    {
        $sql = "exec sp_tables @table_name = " . $this->_db->quoteIdentifier($tableName, true);
        $stmt = $this->_db->query($sql);
        $tableList = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

        if (count($tableList) > 0 && $tableName == $tableList[0]['TABLE_NAME']) {
            return null;
        }
        return 'CREATE TABLE ' . $this->_db->quoteIdentifier($tableName);
    }

    private function _getSqlDropElement($elementName, $typeElement = 'TABLE')
    {
        $sql = "exec sp_tables @table_name = " . $this->_db->quoteIdentifier($elementName, true);
        $stmt = $this->_db->query($sql);
        $elementList = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

        if (count($elementList) > 0 && $elementName == $elementList[0]['TABLE_NAME']) {
            return "DROP $typeElement " . $this->_db->quoteIdentifier($elementName);
        }
        return null;
    }

    protected function _getSqlDropTable($tableName)
    {
        return $this->_getSqlDropElement($tableName);
    }

    protected function _getSqlDropView($viewName)
    {
        return $this->_getSqlDropElement($viewName, 'VIEW');
    }

    public function createView()
    {
        parent::dropView();
        parent::createView();
    }
}
