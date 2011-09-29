<?php

namespace Zend\Mvc\PhpEnvironment;

class GetContainerTest extends AbstractContainerTest
{
    public function setUp()
    {
        $_GET = $this->originalValues;
        $this->container = new GetContainer();
    }

    public function testChangesInContainerPropagateToSuperGlobal()
    {
        $this->container['foo'] = 'bar';
        $this->assertArrayHasKey('foo', $_GET);
        $this->assertEquals('bar', $_GET['foo']);
    }
}
