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

use ArrayObject,
    PHPUnit_Framework_TestCase as TestCase,
    stdClass,
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

    public function getNonObjectModels()
    {
        return array(
            array('string'),
            array(1),
            array(1.0),
            array(array('foo', 'bar')),
            array(array('foo' => 'bar')),
        );
    }

    /**
     * @dataProvider getNonObjectModels
     */
    public function testRendersNonObjectModelAsJson($model)
    {
        $expected = json_encode($model);
        $test     = $this->renderer->render($model);
        $this->assertEquals($expected, $test);
    }

    public function testRendersJsonSerializableModelsAsJson()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Can only test JsonSerializable models in PHP 5.4.0 and up');
        }
        $model        = new TestAsset\JsonModel;
        $model->value = array('foo' => 'bar');
        $expected     = json_encode($model->value);
        $test         = $this->renderer->render($model);
        $this->assertEquals($expected, $test);
    }

    public function testRendersTraversableObjectsAsJsonObjects()
    {
        $model = new ArrayObject(array(
            'foo' => 'bar',
            'bar' => 'baz',
        ));
        $expected     = json_encode($model->getArrayCopy());
        $test         = $this->renderer->render($model);
        $this->assertEquals($expected, $test);
    }

    public function testRendersNonTraversableNonJsonSerializableObjectsAsJsonObjects()
    {
        $model      = new stdClass;
        $model->foo = 'bar';
        $model->bar = 'baz';
        $expected   = json_encode(get_object_vars($model));
        $test       = $this->renderer->render($model);
        $this->assertEquals($expected, $test);
    }

    public function testNonViewModelInitialArgumentWithValuesRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\DomainException');
        $this->renderer->render('foo', array('bar' => 'baz'));
    }

    public function testRendersTreesOfViewModelsByDefault()
    {
        $this->assertTrue($this->renderer->canRenderTrees());
    }
}
