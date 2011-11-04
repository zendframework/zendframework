<?php
namespace ZendTest\Application\TestAsset\Resource;

use Zend\Application\Resource\AbstractResource;

class Zf30 extends AbstractResource
{
    protected $_initialized = 0;
    
    public function init()
    {
        $this->_initialized++;
        return $this;
    }
    
    public function getInitCount()
    {
        return $this->_initialized;
    }
}
