<?php

namespace Zend\Db\Sql;

interface ExpressionInterface
{
    const TYPE_IDENTIFIER = 'identifier';
    const TYPE_VALUE = 'value';

    /**
     * @abstract
     *
     * @return array should return an array in the format:
     *
     * array (
     *    string $specification, // a sprintf formatted string
     *    array $values, // the values for the above sprintf formatted string
     *    array $types, // an array of equal length of the $values array, with either TYPE_IDENTIFIER or TYPE_VALUE for each value
     * )
     *
     */
    public function getExpressionData();
}

