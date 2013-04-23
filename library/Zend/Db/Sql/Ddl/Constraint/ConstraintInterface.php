<?php

namespace Zend\Db\Sql\Ddl\Constraint;

use Zend\Db\Sql\ExpressionInterface;

interface ConstraintInterface extends ExpressionInterface
{
    public function getColumns();
}
