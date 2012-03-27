<?php

namespace Zend\Db\Sql\Predicate;

interface PredicateInterface
{
    const TYPE_IDENTIFIER = 'identifier';
    const TYPE_VALUE = 'value';

    /**
     * @abstract
     * @return array
     */
    public function getWhereParts();
}