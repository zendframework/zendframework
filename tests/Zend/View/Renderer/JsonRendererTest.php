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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Renderer;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Renderer\JsonRenderer,
    Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 */
class JsonRendererTest extends TestCase
{
    public function setUp()
    {
        $this->renderer = new JsonRenderer();
    }

    public function testRendersViewModelsWithoutChildren()
    {
        $model = new ViewModel(array('foo' => 'bar'));
        $test  = $this->renderer->render($model);
        $this->assertEquals(json_encode(array('foo' => 'bar')), $test);
    }

    public function testRendersViewModelsWithChildrenUsingCaptureToValue()
    {
        $root   = new ViewModel(array('foo' => 'bar'));
        $child1 = new ViewModel(array('foo' => 'bar'));
        $child2 = new ViewModel(array('foo' => 'bar'));
        $child1->setCaptureTo('child1');
        $child2->setCaptureTo('child2');
        $root->addChild($child1)
             ->addChild($child2);

        $expected = array(
            'foo' => 'bar',
            'child1' => array(
                'foo' => 'bar',
            ),
            'child2' => array(
                'foo' => 'bar',
            ),
        );
        $test  = $this->renderer->render($root);
        $this->assertEquals(json_encode($expected), $test);
    }

    public function testThrowsAwayChildModelsWithoutCaptureToValueByDefault()
    {
        $root   = new ViewModel(array('foo' => 'bar'));
        $child1 = new ViewModel(array('foo' => 'baz'));
        $child2 = new ViewModel(array('foo' => 'bar'));
        $child1->setCaptureTo(false);
        $child2->setCaptureTo('child2');
        $root->addChild($child1)
             ->addChild($child2);

        $expected = array(
            'foo' => 'bar',
            'child2' => array(
                'foo' => 'bar',
            ),
        );
        $test  = $this->renderer->render($root);
        $this->assertEquals(json_encode($expected), $test);
    }

    public function testCanMergeChildModelsWithoutCaptureToValues()
    {
        $this->renderer->setMergeUnnamedChildren(true);
        $root   = new ViewModel(array('foo' => 'bar'));
        $child1 = new ViewModel(array('foo' => 'baz'));
        $child2 = new ViewModel(array('foo' => 'bar'));
        $child1->setCaptureTo(false);
        $child2->setCaptureTo('child2');
        $root->addChild($child1)
             ->addChild($child2);

        $expected = array(
            'foo' => 'baz',
            'child2' => array(
                'foo' => 'bar',
            ),
        );
        $test  = $this->renderer->render($root);
        $this->assertEquals(json_encode($expected), $test);
    }

    public function testRendersNonObjectModelAsJson()
    {
        $this->markTestIncomplete();
    }

    public function testRendersJsonSerializableModelsAsJson()
    {
        $this->markTestIncomplete();
    }

    public function testRendersTraversableObjectsAsJsonObjects()
    {
        $this->markTestIncomplete();
    }

    public function testRendersNonTraversableNonJsonSerializableObjectsAsJsonObjects()
    {
        $this->markTestIncomplete();
    }

    public function testNonViewModelInitialArgumentWithValuesRaisesException()
    {
        $this->markTestIncomplete();
    }
}
