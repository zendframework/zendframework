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
 * @version    $Id$
 */


/**
 * @see Zend_Db_Select_TestCommon
 */
require_once 'Zend/Db/Select/TestCommon.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_Select_TestCommon extends Zend_Db_Select_TestCommon
{

    /**
     * @var array of Zend_Db_Table_Abstract
     */
    protected $_table = array();

    public function setUp()
    {
        parent::setUp();

        $this->_table['accounts']      = $this->_getTable('Zend_Db_Table_TableAccounts');
        $this->_table['bugs']          = $this->_getTable('Zend_Db_Table_TableBugs');
        $this->_table['bugs_products'] = $this->_getTable('Zend_Db_Table_TableBugsProducts');
        $this->_table['products']      = $this->_getTable('Zend_Db_Table_TableProducts');
    }

    protected function _getTable($tableClass, $options = array())
    {
        if (is_array($options) && !isset($options['db'])) {
            $options['db'] = $this->_db;
        }
        @Zend_Loader::loadClass($tableClass);
        $table = new $tableClass($options);
        return $table;
    }

    /**
     * Get a Zend_Db_Table to provide the base select()
     */
    protected function _getSelectTable($table)
    {
        if (!array_key_exists($table, $this->_table)) {
            throw new Zend_Exception('Non-existent table name');
        }

        return $this->_table[$table];
    }

    /**
     * Test adding a FOR UPDATE clause to a Zend_Db_Select object.
     */
    protected function _selectForReadOnly($fields)
    {
        $table = $this->_getSelectTable('products');

        $select = $table->select()
            ->from($table, $fields);
        return $select;
    }

    /**
     * Test adding the FOR UPDATE query modifier to a Zend_Db_Select object.
     *
     */
    public function testSelectForReadOnly()
    {
        $select = $this->_selectForReadOnly(array('count' => 'COUNT(*)'));
        $this->assertTrue($select->isReadOnly());

        $select = $this->_selectForReadOnly(array());
        $this->assertFalse($select->isReadOnly());

        $select = $this->_selectForReadOnly(array('*'));
        $this->assertFalse($select->isReadOnly());
    }

    /**
     * Test adding a JOIN to a Zend_Db_Select object.
     */
    protected function _selectForJoinZendDbTable()
    {
        $table = $this->_getSelectTable('products');

        $select = $table->select()
            ->join(array('p' => 'zfbugs_products'), 'p.product_id = zfproduct.id', 'p.bug_id');
        return $select;
    }

    /**
     * Test adding a join to the select object without setting integrity check to false.
     *
     */
    public function testSelectForJoinZendDbTable()
    {
        $select = $this->_selectForJoinZendDbTable();

        try {
            $query = $select->assemble();
            $this->fail('Expected to catch Zend_Db_Table_Select_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Select_Exception', $e);
            $this->assertEquals('Select query cannot join with another table', $e->getMessage());
        }
    }

    /**
     * Test adding a FOR UPDATE clause to a Zend_Db_Select object.
     */
    protected function _selectForToString1($tableName = null, $fields = array('*'), $useTable = true)
    {
        $table = $this->_getSelectTable($tableName);

        $select = $table->select();

        if ($useTable) {
            $select->from($table, $fields);
        }

        return $select;
    }

    /**
     * Test adding a FOR UPDATE clause to a Zend_Db_Select object.
     */
    protected function _selectForToString2($tableName, $fields = array('*'))
    {
        $select = $this->_db->select()
            ->from($tableName, $fields);
        return $select;
    }

    /**
     * Test string conversion to ensure Zend_Db_Table_Select is identical
     * to that of Zend_Db_Select.
     *
     */
    public function testSelectForToString()
    {
        // Test for all fields and no default table name on select
        $select1 = $this->_selectForToString1('products', null, false);
        $select2 = $this->_selectForToString2('zfproducts');
        $this->assertEquals($select1->assemble(), $select2->assemble());

        // Test for all fields by default
        $select1 = $this->_selectForToString1('products');
        $select2 = $this->_selectForToString2('zfproducts');
        $this->assertEquals($select1->assemble(), $select2->assemble());

        // Test for selected fields
        $select1 = $this->_selectForToString1('products', array('product_id', 'DISTINCT(product_name)'));
        $select2 = $this->_selectForToString2('zfproducts', array('product_id', 'DISTINCT(product_name)'));
        $this->assertEquals($select1->assemble(), $select2->assemble());
    }

    /**
     * Test to see if a Zend_Db_Table_Select object returns the table it's been
     * instantiated from.
     *
     */
    public function testDbSelectHasTableInstance()
    {
        $table = $this->_getSelectTable('products');
        $select = $table->select();

        $this->assertType('Zend_Db_Table_TableProducts', $select->getTable());
    }

    // ZF-3239
//    public function testFromPartIsAvailableRightAfterInstantiation()
//    {
//        $table = $this->_getSelectTable('products');
//        $select = $table->select();
//
//        $keys = array_keys($select->getPart(Zend_Db_Select::FROM));
//
//        $this->assertEquals('zfproducts', array_pop($keys));
//    }

    // ZF-3239 (from comments)
//    public function testColumnsMethodDoesntThrowExceptionRightAfterInstantiation()
//    {
//        $table = $this->_getSelectTable('products');
//
//        try {
//            $select = $table->select()->columns('product_id');
//
//            $this->assertType('Zend_Db_Table_Select', $select);
//        } catch (Zend_Db_Table_Select_Exception $e) {
//            $this->fail('Exception thrown: ' . $e->getMessage());
//        }
//    }

    // ZF-5424
//    public function testColumnsPartDoesntContainWildcardAfterSettingColumns()
//    {
//        $table = $this->_getSelectTable('products');
//
//        $select = $table->select()->columns('product_id');
//
//        $columns = $select->getPart(Zend_Db_Select::COLUMNS);
//
//        $this->assertEquals(1, count($columns));
//        $this->assertEquals('product_id', $columns[0][1]);
//    }
}