<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class RenderChildModelTest extends TestCase
{
    public function setUp()
    {
        $this->resolver = new TemplateMapResolver(array(
            'layout'  => __DIR__ . '/../_templates/nested-view-model-layout.phtml',
            'child1'  => __DIR__ . '/../_templates/nested-view-model-content.phtml',
            'child2'  => __DIR__ . '/../_templates/nested-view-model-child2.phtml',
            'complex' => __DIR__ . '/../_templates/nested-view-model-complexlayout.phtml',
        ));
        $this->renderer = $renderer = new PhpRenderer();
        $renderer->setCanRenderTrees(true);
        $renderer->setResolver($this->resolver);

        $this->viewModelHelper = $renderer->plugin('view_model');
        $this->helper          = $renderer->plugin('render_child_model');

        $this->parent = new ViewModel();
        $this->parent->setTemplate('layout');
        $this->viewModelHelper->setRoot($this->parent);
        $this->viewModelHelper->setCurrent($this->parent);
    }

    public function testRendersEmptyStringWhenUnableToResolveChildModel()
    {
        $result = $this->helper->render('child1');
        $this->assertSame('', $result);
    }

    public function setupFirstChild()
    {
        $child1 = new ViewModel();
        $child1->setTemplate('child1');
        $child1->setCaptureTo('child1');
        $this->parent->addChild($child1);
        return $child1;
    }

    public function testRendersChildTemplateWhenAbleToResolveChildModelByCaptureToValue()
    {
        $this->setupFirstChild();
        $result = $this->helper->render('child1');
        $this->assertContains('Content for layout', $result, $result);
    }

    public function setupSecondChild()
    {
        $child2 = new ViewModel();
        $child2->setTemplate('child2');
        $child2->setCaptureTo('child2');
        $this->parent->addChild($child2);
        return $child2;
    }


    public function testRendersSiblingChildrenWhenCalledInSequence()
    {
        $this->setupFirstChild();
        $this->setupSecondChild();
        $result = $this->helper->render('child1');
        $this->assertContains('Content for layout', $result, $result);
        $result = $this->helper->render('child2');
        $this->assertContains('Second child', $result, $result);
    }

    public function testRendersNestedChildren()
    {
        $child1 = $this->setupFirstChild();
        $child1->setTemplate('layout');
        $child2 = new ViewModel();
        $child2->setTemplate('child1');
        $child2->setCaptureTo('content');
        $child1->addChild($child2);

        $result = $this->helper->render('child1');
        $this->assertContains('Layout start', $result, $result);
        $this->assertContains('Content for layout', $result, $result);
        $this->assertContains('Layout end', $result, $result);
    }

    public function testRendersSequentialChildrenWithNestedChildren()
    {
        $this->parent->setTemplate('complex');
        $child1 = $this->setupFirstChild();
        $child1->setTemplate('layout');
        $child1->setCaptureTo('content');

        $child2 = $this->setupSecondChild();
        $child2->setCaptureTo('sidebar');

        $nested = new ViewModel();
        $nested->setTemplate('child1');
        $nested->setCaptureTo('content');
        $child1->addChild($nested);

        $result = $this->renderer->render($this->parent);
        $this->assertRegExp('/Content:\s+Layout start\s+Content for layout\s+Layout end\s+Sidebar:\s+Second child/s', $result, $result);
    }

    public function testAttemptingToRenderWithNoCurrentModelRaisesException()
    {
        $renderer = new PhpRenderer();
        $renderer->setResolver($this->resolver);
        $this->setExpectedException('Zend\View\Exception\RuntimeException', 'no view model');
        $renderer->render('layout');
    }
}
