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
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Paginator\Adapter\DbSelect;

use Zend\Paginator\Adapter,
    Zend\Db\Statement\Oracle,
    Zend\Db\Statement\OracleException;

require_once 'Zend/Paginator/Adapter/DbSelectTest.php';
require_once __DIR__ . '/../../_files/TestTable.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Paginator
 */
class OracleTest extends \ZendTest\Paginator\Adapter\DbSelectTest
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        $this->markTestSkipped('Will skip until Zend\Db is refactored.');
        
        if (! extension_loaded('oci8')) {
            $this->markTestSkipped('Oci8 extension is not loaded');
        }

        if (! TESTS_ZEND_Db_ADAPTER_ORACLE_ENABLED) {
            $this->markTestSkipped('Oracle is required');
        }

        $this->_db = new \Zend\Db\Adapter\Oracle(
                array('host' => TESTS_ZEND_Db_ADAPTER_ORACLE_HOSTNAME ,
                        'username' => TESTS_ZEND_Db_ADAPTER_ORACLE_USERNAME ,
                        'password' => TESTS_ZEND_Db_ADAPTER_ORACLE_PASSWORD ,
                        'dbname' => TESTS_ZEND_Db_ADAPTER_ORACLE_SID));

        $this->_dropTable();
        $this->_createTable();
        $this->_populateTable();

        $this->_table = new \TestTable($this->_db);

        $this->_query = $this->_db->select()
                                  ->from('test')
                                  ->order('number ASC') // ZF-3740
                                  ->limit(1000, 0);     // ZF-3727

        $this->_adapter = new Adapter\DbSelect($this->_query);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown ()
    {
        if (! TESTS_ZEND_Db_ADAPTER_ORACLE_ENABLED) {
            return;
        }

        $this->_dropTable();
        $this->_db = null;
        $this->_adapter = null;
    }

    protected function _createTable ()
    {
        $this->_db->query(
                'create table "test" (
                               "number"      NUMBER(5),
                               "testgroup"   NUMBER(3),
                               constraint "pk_test" primary key ("number")
                           )');
        $this->_db->query(
                'create table "test_empty" (
                               "number"      NUMBER(5),
                               "testgroup"   NUMBER(3),
                               constraint "pk_test_empty" primary key ("number")
                           )');
    }

    protected function _populateTable ()
    {
        for ($i = 1; $i < 251; $i ++) {
            $this->_db->query('insert into "test" values (' . $i . ', 1)');
            $this->_db->query('insert into "test" values (' . ($i + 250) . ', 2)');
        }
    }

    protected function _dropTable ()
    {
        try {
            $this->_db->query('drop table "test"');
        } catch (OracleException $e) {}
        try {
            $this->_db->query('drop table "test_empty"');
        } catch (OracleException $e) {}
    }

    public function testGroupByQueryOnEmptyTableReturnsRowCountZero()
    {
        $query = $this->_db->select()
                           ->from('test_empty')
                           ->order('number ASC')
                           ->limit(1000, 0);
        $adapter = new Adapter\DbSelect($query);

        $this->assertEquals(0, $adapter->count());
    }
}
