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

    public function testRendersViewModelWithNoChildren()
    {
        $this->markTestIncomplete();
    }

    public function testRendersViewModelWithChildren()
    {
        $this->markTestIncomplete();
    }

    public function testRendersTreeOfModels()
    {
        $this->markTestIncomplete();
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
