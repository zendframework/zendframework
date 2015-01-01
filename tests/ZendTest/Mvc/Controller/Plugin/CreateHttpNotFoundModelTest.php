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
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\CreateHttpNotFoundModel;

/**
 * Tests for {@see \Zend\Mvc\Controller\Plugin\CreateHttpNotFoundModel}
 *
 * @covers \Zend\Mvc\Controller\Plugin\CreateHttpNotFoundModel
 */
class CreateHttpNotFoundModelTest extends TestCase
{
    public function testBuildsModelWithErrorMessageAndSetsResponseStatusCode()
    {
        $response = new Response();
        $plugin   = new CreateHttpNotFoundModel();

        $model    = $plugin->__invoke($response);

        $this->assertInstanceOf('Zend\\View\\Model\\ViewModel', $model);
        $this->assertSame('Page not found', $model->getVariable('content'));
        $this->assertSame(404, $response->getStatusCode());
    }
}
