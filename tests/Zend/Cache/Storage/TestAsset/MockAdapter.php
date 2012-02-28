<?php

namespace ZendTest\Cache\Storage\TestAsset;
use Zend\Cache\Storage\Adapter\AbstractAdapter;

class MockAdapter extends AbstractAdapter
{

    protected function internalGetItem(& $normalizedKey, array & $normalizedOptions)
    {
    }

    protected function internalSetItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
    }

    public function removeItem($key = null, array $options = array())
    {
    }

    public function getCapacity(array $options = array())
    {
    }

}
