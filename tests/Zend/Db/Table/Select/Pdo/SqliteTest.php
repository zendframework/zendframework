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

require_once 'Zend/Db/Table/Select/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Table_Select_Pdo_SqliteTest extends Zend_Db_Table_Select_TestCommon
{

    public function testSelectFromQualified()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support qualified table names');
    }

    public function testSelectJoinQualified()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support qualified table names');
    }

    public function testSelectFromForUpdate()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support FOR UPDATE');
    }

    public function testSelectJoinRight()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support RIGHT OUTER JOIN');
    }

    public function testSelectGroupBy()
    {
        $select = $this->_selectGroupBy();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $key = "$bugs_products.$bug_id";
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0][$key]);
        $this->assertEquals(3, $result[0]['thecount'],
            'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1][$key]);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    public function testSelectGroupByQualified()
    {
        $select = $this->_selectGroupByQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $key = "$bugs_products.$bug_id";
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0][$key]);
        $this->assertEquals(3, $result[0]['thecount'],
            'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1][$key]);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    public function testSelectHaving()
    {
        $select = $this->_selectHaving();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $key = "$bugs_products.$bug_id";
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0][$key]);
        $this->assertEquals(3, $result[0]['thecount']);
    }

    public function testSelectHavingWithParameter()
    {
        $select = $this->_selectHavingWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $key = "$bugs_products.$bug_id";
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0][$key]);
        $this->assertEquals(3, $result[0]['thecount']);
    }

    public function testSelectHavingOr()
    {
        $select = $this->_selectHavingOr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $key = "$bugs_products.$bug_id";
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0][$key]);
        $this->assertEquals(3, $result[0]['thecount']);
        $this->assertEquals(2, $result[1][$key]);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    public function testSelectHavingOrWithParameter()
    {
        $select = $this->_selectHavingOrWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $key = "$bugs_products.$bug_id";
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0][$key]);
        $this->assertEquals(3, $result[0]['thecount']);
        $this->assertEquals(2, $result[1][$key]);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    public function getDriver()
    {
        return 'Pdo_Sqlite';
    }

}
