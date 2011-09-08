<?php

namespace Zf2Module;

use PHPUnit_Framework_TestCase as TestCase;

class ModuleCollectionTest extends TestCase
{
    public function testDefaultModuleLoader()
    {
        $collection = new ModuleCollection;
        $this->assertInstanceOf('Zf2Module\ModuleLoader', $collection->getLoader());
    }
}
