<?php

namespace Zend\Db\Sql\Predicate;

class Not extends \Zend\Db\Sql\PreparableStatement
{
    protected $keywordPredicate = null;
    public function __construct(KeywordPredicate $keywordPredicate)
    {
        $this->keywordPredicate = $keywordPredicate;
    }
}