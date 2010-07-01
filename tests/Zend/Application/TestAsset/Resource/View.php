<?php
namespace ZendTest\Application\TestAsset\Resource;

use Zend\Application\Resource\AbstractResource;

class View extends AbstractResource
{
    public function init()
    {
        return $this;
    }
}
