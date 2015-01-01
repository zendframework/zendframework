<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller\Plugin;

use PHPUnit_framework_TestCase as TestCase;
use Zend\Mvc\Controller\Plugin\CreateConsoleNotFoundModel;

/**
 * Tests for {@see \Zend\Mvc\Controller\Plugin\CreateConsoleNotFoundModel}
 *
 * @covers \Zend\Mvc\Controller\Plugin\CreateConsoleNotFoundModel
 */
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
