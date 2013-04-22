<?php

namespace Zend\Db\Sql\Ddl\Column;

use Zend\Db\Sql\ExpressionInterface;

interface ColumnInterface extends ExpressionInterface
{
    public function getName();
    public function isNullable();
}