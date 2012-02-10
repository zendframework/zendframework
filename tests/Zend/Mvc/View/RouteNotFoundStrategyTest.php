<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc\View;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\View\RouteNotFoundStrategy,
    Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RouteNotFoundStrategyTest extends TestCase
{
    public function setUp()
    {
        $this->strategy = new RouteNotFoundStrategy();
    }

    public function test404ErrorsInject404ResponseStatusCode()
    {
        $response = new Response();
        $event    = new MvcEvent();
        $errors   = array(
            'not-found' => Application::ERROR_CONTROLLER_NOT_FOUND,
            'invalid'   => Application::ERROR_CONTROLLER_INVALID,
        );
        $event->setResponse($response);
        foreach ($errors as $key => $error) {
            $response->setStatusCode(200);
            $event->setError($error);
            $this->strategy->detectNotFoundError($event);
            $this->assertTrue($response->isNotFound(), 'Failed asserting against ' . $key);
        }
    }

    public function testNon404ErrorsInjectNoStatusCode()
    {
        $response = new Response();
        $event    = new MvcEvent();
        $errors   = array(
            Application::ERROR_EXCEPTION,
            'custom-error',
            null,
        );
        foreach ($errors as $error) {
            $response->setStatusCode(200);
            $event->setError($error);
            $this->strategy->detectNotFoundError($event);
            $this->assertFalse($response->isNotFound());
        }
    }

    public function testResponseAsResultDoesNotPrepare404ViewModel()
    {
        $response = new Response();
        $event    = new MvcEvent();
        $event->setResponse($response)
              ->setResult($response);

        $this->strategy->prepareNotFoundViewModel($event);
        $model = $event->getResult();
        if ($model instanceof ViewModel) {
            $this->assertNotEquals($this->strategy->getNotFoundTemplate(), $model->getTemplate());
            $variables = $model->getVariables();
            $this->assertArrayNotHasKey('message', $variables);
        }
    }

    public function testNon404ResponseDoesNotPrepare404ViewModel()
    {
        $response = new Response();
        $event    = new MvcEvent();
        $response->setStatusCode(200);
        $event->setResponse($response);

        $this->strategy->prepareNotFoundViewModel($event);
        $model = $event->getResult();
        if ($model instanceof ViewModel) {
            $this->assertNotEquals($this->strategy->getNotFoundTemplate(), $model->getTemplate());
            $variables = $model->getVariables();
            $this->assertArrayNotHasKey('message', $variables);
        }
    }

    public function test404ResponsePrepares404ViewModelWithTemplateFromStrategy()
    {
        $response = new Response();
        $event    = new MvcEvent();
        $response->setStatusCode(404);
        $event->setResponse($response);

        $this->strategy->prepareNotFoundViewModel($event);
        $model = $event->getResult();
        $this->assertInstanceOf('Zend\View\Model', $model);
        $this->assertEquals($this->strategy->getNotFoundTemplate(), $model->getTemplate());
        $variables = $model->getVariables();
        $this->assertArrayHasKey('message', $variables);
    }
}
