<?php

namespace ZendTest\Code\Scanner\TestAsset\MapperExample;

class Mapper
{

    protected $dbAdapter = null;

    public function __construct()
    {
    }

    public function setDbAdapter(DbAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function __toString()
    {
        return 'I am a ' . get_class($this) . ' object (hash ' . spl_object_hash($this) . '), using this dbAdapter ' . PHP_EOL . '    ' . $this->dbAdapter;
    }

}
