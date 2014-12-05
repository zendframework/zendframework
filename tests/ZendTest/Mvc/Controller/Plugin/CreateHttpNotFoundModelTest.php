<?php

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
