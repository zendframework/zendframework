<?php

namespace Zend\Db\Sql\Platform;

use Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Sql\SqlInterface;

interface PlatformInterface
{
    public function supportsSqlObject(PreparableSqlInterface $sqlObject);
    public function prepareStatementFromSqlObject(PreparableSqlInterface $sqlObject);
}
