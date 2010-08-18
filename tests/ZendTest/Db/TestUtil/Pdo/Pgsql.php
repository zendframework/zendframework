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
 * @namespace
 */
namespace ZendTest\Db\TestUtil\Pdo;

/**
 * @see Zend_Db_TestUtil_Pdo_Common
 */

\PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Pgsql extends \Zend_Db_TestUtil_Pdo_Common
{

    public function setUp(\Zend\Db\Adapter\AbstractAdapter $db)
    {
        $this->_db = $db;
        $this->createSequence('zfproducts_seq');
        parent::setUp($db);
    }

    public function getParams(array $constants = array())
    {
        $constants = array (
            'host'     => 'TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_HOSTNAME',
            'username' => 'TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_USERNAME',
            'password' => 'TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PASSWORD',
            'dbname'   => 'TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE'
        );
        return parent::getParams($constants);
    }

    public function getSchema()
    {
        return 'public';
    }

    /**
     * For PostgreSQL, override the Products table to use an
     * explicit sequence-based column.
     */
    protected function _getColumnsProducts()
    {
        return array(
            'product_id'   => 'INT NOT NULL PRIMARY KEY',
            'product_name' => 'VARCHAR(100)'
        );
    }

    protected function _getDataProducts()
    {
        $data = parent::_getDataProducts();
        foreach ($data as &$row) {
            $row['product_id'] = new \Zend\Db\Expr('NEXTVAL('.$this->_db->quote('zfproducts_seq').')');
        }
        return $data;
    }

    public function getSqlType($type)
    {
        if ($type == 'IDENTITY') {
            return 'SERIAL PRIMARY KEY';
        }
        if ($type == 'DATETIME') {
            return 'TIMESTAMP';
        }
        if ($type == 'CLOB') {
            return 'TEXT';
        }
        if ($type == 'BLOB') {
            return 'TEXT';
        }
        return $type;
    }

    protected function _getSqlCreateTable($tableName)
    {
        $tableList = $this->_db->fetchCol('SELECT relname AS table_name FROM pg_class '
            . $this->_db->quoteInto(' WHERE relkind = \'r\' AND relname = ?', $tableName)
        );
        if (in_array($tableName, $tableList)) {
            return null;
        }
        return 'CREATE TABLE ' . $this->_db->quoteIdentifier($tableName);
    }

    protected function _getSqlDropTable($tableName)
    {
        $tableList = $this->_db->fetchCol('SELECT relname AS table_name FROM pg_class '
            . $this->_db->quoteInto(' WHERE relkind = \'r\' AND relname = ?', $tableName)
        );
        if (in_array($tableName, $tableList)) {
            return 'DROP TABLE ' . $this->_db->quoteIdentifier($tableName) . ' CASCADE';
        }
        return null;
    }

    protected function _getSqlCreateSequence($sequenceName)
    {
        $seqList = $this->_db->fetchCol('SELECT relname AS sequence_name FROM pg_class '
            . $this->_db->quoteInto(' WHERE relkind = \'S\' AND relname = ?', $sequenceName)
        );
        if (in_array($sequenceName, $seqList)) {
            return null;
        }
        return 'CREATE SEQUENCE ' . $this->_db->quoteIdentifier($sequenceName);
    }

    protected function _getSqlDropSequence($sequenceName)
    {
        $seqList = $this->_db->fetchCol('SELECT relname AS sequence_name FROM pg_class '
            . $this->_db->quoteInto(' WHERE relkind = \'S\' AND relname = ?', $sequenceName)
        );
        if (in_array($sequenceName, $seqList)) {
            return 'DROP SEQUENCE ' . $this->_db->quoteIdentifier($sequenceName);
        }
        return null;
    }

}
