<?php

namespace ZendTest\Application\TestAsset\Resource;

class Zf38Modules extends \Zend\Application\Resource\Modules
{
    protected $initTime;
    
    public function init()
    {
        $return = parent::init();
        if(empty($this->initTime))
            $this->initTime = \microtime(true);
        return $return;
    }
    
    public function getInitTime()
    {
        return $this->initTime;
    }
}