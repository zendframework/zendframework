<?php

namespace ZendTest\Acl\TestAsset\UseCase1;

use Zend\Acl\Resource;

class BlogPost implements Resource\ResourceInterface
{
    public $owner = null;
    public function getResourceId()
    {
        return 'blogPost';
    }
}
