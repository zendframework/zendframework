<?php
namespace ZendTest\Db\Adapter\Driver\TestAsset;

/**
 * @todo Use a real mock object
 */
class PdoMock extends \PDO
{
    public function __construct()
    {}

    public function beginTransaction()
    {
        return true;
    }

    public function commit()
    {
        return true;
    }

    public function getAttribute($attribute)
    {}

    public function rollBack()
    {
        return true;
    }
}
