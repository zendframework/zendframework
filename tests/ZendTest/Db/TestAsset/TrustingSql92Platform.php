<?php

namespace ZendTest\Db\TestAsset;

use Zend\Db\Adapter\Platform\Sql92;

class TrustingSql92Platform extends Sql92
{
    public function quoteValue($value)
    {
        return $this->quoteTrustedValue($value);
    }
}