<?php

namespace ZendTest\Cache\Storage\TestAsset;
use Zend\Cache\Storage\Adapter\AbstractAdapter;

class MockAdapter extends AbstractAdapter
{

    protected function internalGetItem(& $normalizedKey, array & $normalizedOptions, & $success = null, & $casToken = null)
    {
    }

    protected function internalSetItem(& $normalizedKey, & $value, array & $normalizedOptions)
    {
    }

    protected function internalRemoveItem(& $normalizedKey, array & $normalizedOptions)
    {
    }

    protected function internalGetCapacity(array & $normalizedOptions)
    {
    }

}
