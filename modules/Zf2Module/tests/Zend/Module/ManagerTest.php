<?php

namespace ZendTest\Module;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Module\Manager,
    Zend\Module\ManagerOptions;

class ManagerTest extends TestCase
{
    public function testDefaultManagerOptions()
    {
        $moduleManager = new Manager(array());
        $this->assertInstanceOf('Zend\Module\ManagerOptions', $moduleManager->getOptions());
    }
}
