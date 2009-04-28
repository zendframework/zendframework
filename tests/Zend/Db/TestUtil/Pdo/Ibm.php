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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $$
 */


/**
 * @see Zend_Db_TestUtil_Db2
 */
require_once 'Zend/Db/TestUtil/Db2.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_TestUtil_Pdo_Ibm extends Zend_Db_TestUtil_Db2
{
    public function getSchema()
    {
        $desc = $this->_db->describeTable('zfproducts');
        return $desc['product_id']['SCHEMA_NAME'];
    }

    protected function _getDataProducts()
    {
        $data = parent::_getDataProducts();

        $server = $this->getServer();
        if ($server == 'IDS') {
            foreach ($data as &$row) {
                $row['product_id'] = new Zend_Db_Expr($this->_db->quoteIdentifier('zfproducts_seq', true) . ".NEXTVAL");
            }
        }
        return $data;
    }

    protected function _getDataDocuments()
    {
        $server = $this->getServer();

        if ($server == 'IDS') {
            return array (
            array(
                'doc_id'    => 1,
                'doc_clob'  => 'this is the clob that never ends...'.
                               'this is the clob that never ends...'.
                               'this is the clob that never ends...',
                'doc_blob'  => 'this is the blob that never ends...'.
                               'this is the blob that never ends...'.
                               'this is the blob that never ends...'
                )
            );
        }

        return parent::_getDataDocuments();
    }

    public function getSqlType($type)
    {
        $server = $this->getServer();

        if ($server == 'IDS') {

            if ($type == 'IDENTITY') {
                return 'SERIAL(1) PRIMARY KEY';
            }
            if ($type == 'DATETIME') {
                return 'DATE';
            }
            return $type;
        }
        return parent::getSqlType($type);
    }

    protected function _getSqlCreateTable($tableName)
    {
        $server = $this->getServer();

        if ($server == 'IDS') {
            $tableList = $this->_db->fetchCol('SELECT T.TABNAME FROM SYSTABLES T '
            . $this->_db->quoteInto(' WHERE T.TABNAME = ?', $tableName)
            );
            if (in_array($tableName, $tableList)) {
                return null;
            }
            return 'CREATE TABLE ' . $this->_db->quoteIdentifier($tableName, true);
        }

        return parent::_getSqlCreateTable($tableName);
    }

    protected function _getSqlDropTable($tableName)
    {
        $server = $this->getServer();

        if ($server == 'IDS') {
            $tableList = $this->_db->fetchCol('SELECT T.TABNAME FROM SYSTABLES T '
            . $this->_db->quoteInto(' WHERE T.TABNAME = ?', $tableName)
            );
            if (in_array($tableName, $tableList)) {
                return 'DROP TABLE ' . $this->_db->quoteIdentifier($tableName, true);
            }
            return null;
        }

        return parent::_getSqlDropTable($tableName);
    }

    protected function _getSqlCreateSequence($sequenceName)
    {
        $server = $this->getServer();

        if ($server == 'IDS') {
            $seqList = $this->_db->fetchCol('SELECT S.TABNAME FROM SYSTABLES S '
            . $this->_db->quoteInto(' WHERE S.TABNAME = ?', $sequenceName)
            . " AND S.TABTYPE = 'Q'"
            );

            if (in_array($sequenceName, $seqList)) {
                return null;
            }
            return 'CREATE SEQUENCE ' . $this->_db->quoteIdentifier($sequenceName, true) . ' START WITH 1 INCREMENT BY 1 MINVALUE 1';
        }

        return parent::_getSqlCreateSequence($sequenceName);
    }

    protected function _getSqlDropSequence($sequenceName)
    {
        $server = $this->getServer();

        if ($server == 'IDS') {
            $seqList = $this->_db->fetchCol('SELECT S.TABNAME FROM SYSTABLES S '
            . $this->_db->quoteInto(' WHERE S.TABNAME = ?', $sequenceName)
            . " AND S.TABTYPE = 'Q'"
            );

            if (in_array($sequenceName, $seqList)) {
                return 'DROP SEQUENCE ' . $this->_db->quoteIdentifier($sequenceName, true);
            }
            return null;
        }

        return parent::_getSqlDropSequence($sequenceName);
    }

    public function getServer()
    {
        return substr($this->_db->getConnection()->getAttribute(PDO::ATTR_SERVER_INFO), 0, 3);
    }

    protected function _rawQuery($sql)
    {
        $conn = $this->_db->getConnection();
        $retval = $conn->query($sql);
        if (!$retval) {
            $e = $conn->error;
            require_once 'Zend/Db/Exception.php';
            throw new Zend_Db_Exception("SQL error for \"$sql\": $e");
        }
    }
}
