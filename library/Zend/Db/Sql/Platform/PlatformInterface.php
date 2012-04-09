<?php

namespace Zend\Db\Sql\Platform;

interface PlatformInterface
{
    public function prepareSqlObject($sqlObject);
}
