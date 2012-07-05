<?php

namespace ZendTest\Cache\Storage\TestAsset;

use Zend\Cache\Storage\ClearExpiredInterface;

class ClearExpiredMockAdapter extends MockAdapter implements ClearExpiredInterface
{
    public function clearExpired()
    {
    }
}
