<?php

namespace Zend\Db\Sql\Platform;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Sql\SqlInterface,
    Zend\Db\Adapter\Driver\StatementInterface;

class Platform extends AbstractPlatform
{

    protected $adapter = null;

    /**
     * @var PlatformInterface
     */
    protected $actualPlatform = null;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $platform = $adapter->getPlatform();
        switch (strtolower($platform->getName())) {
            case 'sqlserver':
                $platform = new SqlServer\SqlServer();
                $this->decorators = $platform->decorators;
                break;
            default:
        }
    }

}
