<?php

namespace ZendTest\Cache\Storage\TestAsset;
use Zend\Cache\Storage\Adapter\AbstractAdapter;

class MockAdapter extends AbstractAdapter
{

    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
    }

    protected function internalSetItem(& $normalizedKey, & $value)
    {
    }

    protected function internalRemoveItem(& $normalizedKey)
    {
    }
}
