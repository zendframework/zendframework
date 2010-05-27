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
class Sqlite extends \Zend_Db_TestUtil_Pdo_Common
{
    protected $_enabledConstantName = 'TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_ENABLED';

    public function getParams(array $constants = array())
    {
        $constants = array (
            'dbname'   => 'TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE'
        );
        return parent::getParams($constants);
    }

    protected function _getSqlCreateTable($tableName)
    {
        return 'CREATE TABLE IF NOT EXISTS ' . $this->_db->quoteIdentifier($tableName);
    }

    protected function _getSqlDropTable($tableName)
    {
        return 'DROP TABLE IF EXISTS ' . $this->_db->quoteIdentifier($tableName);
    }

    public function getSqlType($type)
    {
        if ($type == 'IDENTITY') {
            return 'INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT';
        }
        return $type;
    }

    protected function _getSqlCreateView($viewName)
    {
        return 'CREATE VIEW IF NOT EXISTS ' . $this->_db->quoteIdentifier($viewName, true);
    }

    protected function _getSqlDropView($viewName)
    {
        return 'DROP VIEW IF EXISTS ' . $this->_db->quoteIdentifier($viewName, true);
    }
}
