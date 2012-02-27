<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface;

interface SqlInterface
{
    public function getSqlString(PlatformInterface $platform = null);
}