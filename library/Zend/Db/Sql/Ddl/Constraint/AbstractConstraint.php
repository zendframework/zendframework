<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Constraint;

abstract class AbstractConstraint implements ConstraintInterface
{
    protected $columns = array();

    public function __construct($columns = null)
    {
        (!$columns) ?: $this->setColumns($columns);
    }

    public function setColumns($columns)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }

        $this->columns = $columns;
    }

    public function addColumn($column)
    {
        $this->columns[] = $column;
    }

    public function getColumns()
    {
        return $this->columns;
    }

}