<?php

namespace Zend\Db\Sql\Platform;

use Zend\Db\Adapter\Platform\PlatformInterface as AdapterPlatformInterface;

class Platform implements PlatformInterface
{

    public function __construct(AdapterPlatformInterface $adapterPlatform)
    {

    }

    public function prepareSqlObject($sqlObject)
    {

    }
}