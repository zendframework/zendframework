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
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Test\PHPUnit\Db\DataSet;

/**
 * Use a Zend_Db_Table for assertions with other PHPUnit Database Extension table types.
 *
 * @uses       PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData
 * @uses       PHPUnit_Extensions_Database_DataSet_QueryTable
 * @uses       \Zend\Db\Table\AbstractTable
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DbTable extends \PHPUnit_Extensions_Database_DataSet_QueryTable
{
    /**
     * Zend_Db_Table object
     *
     * @var \Zend\Db\Table\AbstractTable
     */
    protected $_table = null;

    /**
     * @var array
     */
    protected $_columns = array();

    /**
     * @var string
     */
    protected $_where = null;

    /**
     * @var string
     */
    protected $_orderBy = null;

    /**
     * @var string
     */
    protected $_count = null;

    /**
     * @var int
     */
    protected $_offset = null;

    /**
     * Construct Dataset Table from Zend_Db_Table object
     *
     * @param \Zend\Db\Table\AbstractTable        $table
     * @param string|\Zend\Db\Select|null    $where
     * @param string|null                   $order
     * @param int                           $count
     * @param int                           $offset
     */
    public function __construct(\Zend\Db\Table\AbstractTable $table, $where=null, $order=null, $count=null, $offset=null)
    {
        $this->tableName = $table->info('name');
        $this->_columns = $table->info('cols');

        $this->_table = $table;
        $this->_where = $where;
        $this->_order = $order;
        $this->_count = $count;
        $this->_offset = $offset;
    }

    /**
     * Lazy load data via table fetchAll() method.
     *
     * @return void
     */
    protected function loadData()
    {
        if ($this->data === null) {
            $this->data = $this->_table->fetchAll(
                $this->_where, $this->_order, $this->_count, $this->_offset
            );
            if($this->data instanceof \Zend\Db\Table\AbstractRowset) {
                $this->data = $this->data->toArray();
            }
        }
    }

    /**
     * Create Table Metadata object
     */
    protected function createTableMetaData()
    {
        if ($this->tableMetaData === NULL) {
            $this->loadData();
            $this->tableMetaData = new \PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($this->tableName, $this->_columns);
        }
    }
}
