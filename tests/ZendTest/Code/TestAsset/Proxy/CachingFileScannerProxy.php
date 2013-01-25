<?php

namespace ZendTest\Code\Scanner\TestAsset\Proxy;

use Zend\Code\Scanner\CachingFileScanner;

class CachingFileScannerProxy extends CachingFileScanner
{
    /**
     * @var CachingFileScanner
     */
    protected $cfs;

    public function __construct(CachingFileScanner $cfs)
    {
        $this->cfs = $cfs;
    }

    public function getCache()
    {
    }
}
