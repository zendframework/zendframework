<?php

namespace Zf2Module;

use PHPUnit_Framework_TestCase as TestCase;

class ModuleManagerTest extends TestCase
{
    public function testDefaultModuleLoader()
    {
        $collection = new ModuleManager;
        $this->assertInstanceOf('Zf2Module\ModuleLoader', $collection->getLoader());
    }
}
