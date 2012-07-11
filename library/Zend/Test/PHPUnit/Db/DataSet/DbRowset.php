<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace Zend\Test\PHPUnit\Db\DataSet;

/**
 * Use a Zend_Db Rowset as a datatable for assertions with other PHPUnit Database extension tables.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
class DbRowset extends \PHPUnit_Extensions_Database_DataSet_AbstractTable
{
    /**
     * Construct Table object from a Zend_Db_Table_Rowset
     *
     * @param \Zend\Db\Table\AbstractRowset $rowset
     * @param string $tableName
     */
    public function __construct(\Zend\Db\Table\AbstractRowset $rowset, $tableName = null)
    {
        if($tableName == null) {
            $table = $rowset->getTable();
            if($table !== null) {
                $tableName = $table->info('name');
            } else {
                throw new \Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException(
                    'No table name was given to Rowset Table and table name cannot be infered from the table, '.
                    'because the rowset is disconnected from database.'
                );
            }
        }

        $this->data = $rowset->toArray();

        $columns = array();
        if(isset($this->data[0]) > 0) {
            $columns = array_keys($this->data[0]);
        } else if($rowset->getTable() != null) {
            $columns = $rowset->getTable()->info('cols');
        }

        $this->tableName = $tableName;
        $this->tableMetaData = new \PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($this->tableName, $columns);
    }
}
