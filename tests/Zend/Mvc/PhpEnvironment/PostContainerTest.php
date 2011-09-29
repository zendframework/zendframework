<?php

namespace ZendTest\Mvc\PhpEnvironment;

use Zend\Mvc\PhpEnvironment\PostContainer;

class PostContainerTest extends AbstractContainerTest
{
    public function setUp()
    {
        $_POST = $this->originalValues;
        $this->container = new PostContainer();
    }

    public function testChangesInContainerPropagateToSuperGlobal()
    {
        $this->container['foo'] = 'bar';
        $this->assertArrayHasKey('foo', $_POST);
        $this->assertEquals('bar', $_POST['foo']);
    }
}
