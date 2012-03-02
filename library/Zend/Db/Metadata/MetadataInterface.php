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
 * @subpackage Metadata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Metadata;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
