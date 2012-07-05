<?php

namespace ZendTest\Cache\Storage\TestAsset;

use Zend\Cache\Storage\OptimizableInterface;

class OptimizableMockAdapter extends MockAdapter implements OptimizableInterface
{
    public function optimize()
    {
    }
}
