<?php

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_framework_TestCase as TestCase;
use Zend\Mvc\Controller\Plugin\CreateConsoleNotFoundModel;

class CreateConsoleNotFoundModelTest extends TestCase
{
    public function testCanReturnModelWithErrorMessageAndErrorLevel()
    {
        $plugin = new CreateConsoleNotFoundModel();

        $model = $plugin->__invoke();

        $this->assertInstanceOf('Zend\\View\\Model\\ConsoleModel', $model);
        $this->assertSame('Page not found', $model->getResult());
        $this->assertSame(1, $model->getErrorLevel());
    }
}
