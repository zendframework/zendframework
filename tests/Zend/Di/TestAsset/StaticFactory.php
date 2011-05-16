<?php
namespace ZendTest\Di\TestAsset;

class StaticFactory
{
    public static function factory(Struct $struct, array $params = array())
    {
        $params = array_merge((array) $struct, $params);
        return new DummyParams($params);
    }
}
