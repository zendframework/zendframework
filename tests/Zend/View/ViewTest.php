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
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\View\Model\ViewModel,
    Zend\View\PhpRenderer,
    Zend\View\Renderer,
    Zend\View\Resolver,
    Zend\View\View;

class ViewTest extends TestCase
{
    public function setUp()
    {
        $this->request  = new Request;
        $this->response = new Response;
        $this->model    = new ViewModel;
        $this->view     = new View;

        $this->view->setRequest($this->request);
        $this->view->setResponse($this->response);
    }

    public function attachTestStrategies()
    {
        $this->view->addRenderingStrategy(function ($e) {
            return new TestAsset\Renderer\VarExportRenderer();
        });
        $this->result = $result = new stdClass;
        $this->view->addResponseStrategy(function ($e) use ($result) {
            $result->content = $e->getResult();
        });
    }

    public function testRendersViewModelWithNoChildren()
    {
        $this->attachTestStrategies();
        $variables = array(
            'foo' => 'bar',
            'bar' => 'baz',
        );
        $this->model->setVariables($variables);
        $this->view->render($this->model);

        $this->assertEquals(var_export($variables, true), $this->result->content);
    }

    public function testRendersViewModelWithChildren()
    {
        $this->attachTestStrategies();

        $child1 = new ViewModel(array('foo' => 'bar'));
        $child1->setCaptureTo('child1');

        $child2 = new ViewModel(array('bar' => 'baz'));
        $child2->setCaptureTo('child2');

        $this->model->setVariable('parent', 'node');
        $this->model->addChild($child1);
        $this->model->addChild($child2);

        $this->view->render($this->model);

        $expected = var_export(array(
            'parent' => 'node',
            'child1' => var_export(array('foo' => 'bar'), true),
            'child2' => var_export(array('bar' => 'baz'), true),
        ), true);
        $this->assertEquals($expected, $this->result->content);
    }

    public function testRendersTreeOfModels()
    {
        $this->attachTestStrategies();

        $child1 = new ViewModel(array('foo' => 'bar'));
        $child1->setCaptureTo('child1');

        $child2 = new ViewModel(array('bar' => 'baz'));
        $child2->setCaptureTo('child2');
        $child1->addChild($child2);

        $this->model->setVariable('parent', 'node');
        $this->model->addChild($child1);

        $this->view->render($this->model);

        $expected = var_export(array(
            'parent' => 'node',
            'child1' => var_export(array(
                'foo'    => 'bar',
                'child2' => var_export(array('bar' => 'baz'), true),
            ), true),
        ), true);
        $this->assertEquals($expected, $this->result->content);
    }

    public function testChildrenMayInvokeDifferentRenderingStrategiesThanParents()
    {
        $this->markTestIncomplete();
    }

    public function testTerminalChildRaisesException()
    {
        $this->markTestIncomplete();
    }

    public function testChildrenAreCapturedToParentVariables()
    {
        $this->markTestIncomplete();
    }

    public function testOmittingCaptureToValueInChildLeadsToOmissionInParent()
    {
        $this->markTestIncomplete();
    }

    public function testResponseStrategyIsTriggeredForParentModel()
    {
        $this->markTestIncomplete();
    }

    public function testResponseStrategyIsNotTriggeredForChildModel()
    {
        $this->markTestIncomplete();
    }
}
