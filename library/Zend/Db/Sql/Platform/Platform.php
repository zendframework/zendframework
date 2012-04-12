<?php

namespace Zend\Db\Sql\Platform;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Sql\PreparableSqlInterface,
    Zend\Db\Sql\SqlInterface;

class Platform implements PlatformInterface
{

    protected $adapter = null;

    /**
     * @var PlatformInterface
     */
    protected $platform = null;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $platform = $adapter->getPlatform();
        switch (strtolower($platform->getName())) {
            case 'mysql':
                $this->platform = new Platform\Mysql\Mysql;
                break;
            default:
        }
    }

    public function canPrepareSqlObject(PreparableSqlInterface $sqlObject)
    {
        if ($this->platform) {
            return $this->platform->canPrepareSqlObject($sqlObject);
        }

        return true;
    }

    public function prepareSqlObject(PreparableSqlInterface $sqlObject)
    {
        if ($this->platform) {
            return $this->prepareSqlObject($sqlObject);
        }

        $statement = $this->adapter->createStatement();
        $sqlObject->prepareStatement($this->adapter, $statement);
        return $statement;
    }
}