<?php

namespace Zend\Db\Sql\Platform;

use Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Sql\SqlInterface;

interface PlatformInterface
{
    public function canPrepareSqlObject(PreparableSqlInterface $sqlObject);
    public function prepareSqlObject(PreparableSqlInterface $sqlObject);
}
