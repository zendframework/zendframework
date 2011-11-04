<?php
namespace ZendTest\Application\TestAsset\Resource;

use Zend\Application\Resource\AbstractResource;

class Zf38 extends AbstractResource
{
    protected $initTime;
    
    public function init()
    {
        if(empty($this->initTime))
            $this->initTime = \microtime(true);
        return $this;
    }
    
    public function getInitTime()
    {
        return $this->initTime;
    }
}
