<?php

namespace ZendTest\Db\Adapter\Driver\Pdo\TestAsset;

class SqliteMemoryPdo extends \Pdo
{
    protected $mockStatement;

    public function __construct()
    {
        parent::__construct('sqlite::memory:');
    }

}
