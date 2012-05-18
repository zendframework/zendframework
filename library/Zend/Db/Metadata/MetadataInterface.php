<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Metadata;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
interface MetadataInterface
{
    public function getSchemas();

    public function getTableNames($schema = null, $database = null);
    public function getTables($schema = null, $database = null);
    public function getTable($tableName, $schema = null, $database = null);

    public function getViewNames($schema = null, $database = null);
    public function getViews($schema = null, $database = null);
    public function getView($viewName, $schema = null, $database = null);

    public function getColumnNames($table, $schema = null, $database = null);
    public function getColumns($table, $schema = null, $database = null);
    public function getColumn($columnName, $table, $schema = null, $database = null);

    public function getConstraints($table, $schema = null, $database = null);
    public function getConstraint($constraintName, $table, $schema = null, $database = null);
    public function getConstraintKeys($constraint, $table, $schema = null, $database = null);

    public function getTriggerNames($schema = null, $database = null);
    public function getTriggers($schema = null, $database = null);
    public function getTrigger($triggerName, $schema = null, $database = null);

}
