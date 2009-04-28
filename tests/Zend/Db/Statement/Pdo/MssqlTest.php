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
 */

require_once 'Zend/Db/Statement/Pdo/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Statement_Pdo_MssqlTest extends Zend_Db_Statement_Pdo_TestCommon
{

    public function testStatementGetColumnMeta()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support meta data.');
    }

    public function testStatementExecuteWithParams()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementExecuteWithParams();
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function testStatementBindParamByPosition()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementBindParamByPosition();
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function testStatementBindParamByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementBindParamByName();
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function testStatementBindValueByPosition()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementBindValueByPosition();
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function testStatementBindValueByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        // Make IDENTITY column accept explicit value.
        // This can be done in only one table in a given session.
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products ON");
        parent::testStatementBindValueByName();
        $this->_db->getConnection()->exec("SET IDENTITY_INSERT $products OFF");
    }

    public function getDriver()
    {
        return 'Pdo_Mssql';
    }

}
