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

namespace ZendTest\View\Model;

use ArrayObject,
    stdClass,
    PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Model\ViewModel,
    Zend\View\Variables as ViewVariables;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewModelTest extends TestCase
{
    public function testAllowsEmptyConstructor()
    {
        $model = new ViewModel();
        $this->assertInstanceOf('Zend\View\Variables', $model->getVariables());
        $this->assertEquals(array(), $model->getOptions());
    }

    public function testAllowsEmptyOptionsArgumentToConstructor()
    {
        $model = new ViewModel(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $model->getVariables());
        $this->assertEquals(array(), $model->getOptions());
    }

    public function testAllowsPassingBothVariablesAndOptionsArgumentsToConstructor()
    {
        $model = new ViewModel(array('foo' => 'bar'), array('template' => 'foo/bar'));
        $this->assertEquals(array('foo' => 'bar'), $model->getVariables());
        $this->assertEquals(array('template' => 'foo/bar'), $model->getOptions());
    }

    public function testAllowsPassingTraversableArgumentsToVariablesAndOptionsInConstructor()
    {
        $vars    = new ArrayObject;
        $options = new ArrayObject;
        $model = new ViewModel($vars, $options);
        $this->assertSame($vars, $model->getVariables());
        $this->assertSame(iterator_to_array($options), $model->getOptions());
    }

    public function testCanSetVariablesSingly()
    {
        $model = new ViewModel(array('foo' => 'bar'));
        $model->setVariable('bar', 'baz');
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $model->getVariables());
    }

    public function testCanOverwriteVariablesSingly()
    {
        $model = new ViewModel(array('foo' => 'bar'));
        $model->setVariable('foo', 'baz');
        $this->assertEquals(array('foo' => 'baz'), $model->getVariables());
    }

    public function testSetVariablesOverwritesAllPreviouslyStored()
    {
        $model = new ViewModel(array('foo' => 'bar', 'bar' => 'baz'));
        $model->setVariables(array('bar' => 'BAZBAT'));
        $this->assertEquals(array('bar' => 'BAZBAT'), $model->getVariables());
    }

    public function testCanSetOptionsSingly()
    {
        $model = new ViewModel(array(), array('foo' => 'bar'));
        $model->setOption('bar', 'baz');
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $model->getOptions());
    }

    public function testCanOverwriteOptionsSingly()
    {
        $model = new ViewModel(array(), array('foo' => 'bar'));
        $model->setOption('foo', 'baz');
        $this->assertEquals(array('foo' => 'baz'), $model->getOptions());
    }

    public function testSetOptionsOverwritesAllPreviouslyStored()
    {
        $model = new ViewModel(array(), array('foo' => 'bar', 'bar' => 'baz'));
        $model->setOptions(array('bar' => 'BAZBAT'));
        $this->assertEquals(array('bar' => 'BAZBAT'), $model->getOptions());
    }

    public function testOptionsAreInternallyConvertedToAnArrayFromTraversables()
    {
        $options = new ArrayObject(array('foo' => 'bar'));
        $model = new ViewModel();
        $model->setOptions($options);
        $this->assertEquals($options->getArrayCopy(), $model->getOptions());
    }

    public function testPassingAnInvalidArgumentToSetVariablesRaisesAnException()
    {
        $model = new ViewModel();
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects an array');
        $model->setVariables(new stdClass);
    }

    public function testPassingAnInvalidArgumentToSetOptionsRaisesAnException()
    {
        $model = new ViewModel();
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects an array');
        $model->setOptions(new stdClass);
    }

    public function testCaptureToDefaultsToContent()
    {
        $model = new ViewModel();
        $this->assertEquals('content', $model->captureTo());
    }

    public function testCaptureToValueIsMutable()
    {
        $model = new ViewModel();
        $model->setCaptureTo('foo');
        $this->assertEquals('foo', $model->captureTo());
    }

    public function testHasNoChildrenByDefault()
    {
        $model = new ViewModel();
        $this->assertFalse($model->hasChildren());
    }

    public function testWhenNoChildrenCountIsZero()
    {
        $model = new ViewModel();
        $this->assertEquals(0, count($model));
    }

    public function testCanAddChildren()
    {
        $model = new ViewModel();
        $child = new ViewModel();
        $model->addChild($child);
        $this->assertTrue($model->hasChildren());
    }

    public function testCanCountChildren()
    {
        $model = new ViewModel();
        $child = new ViewModel();
        $model->addChild($child);
        $this->assertEquals(1, count($model));
        $model->addChild($child);
        $this->assertEquals(2, count($model));
    }

    public function testCanIterateChildren()
    {
        $model = new ViewModel();
        $child = new ViewModel();
        $model->addChild($child);
        $model->addChild($child);
        $model->addChild($child);

        $count = 0;
        foreach ($model as $childModel) {
            $this->assertSame($child, $childModel);
            $count++;
        }
        $this->assertEquals(3, $count);
    }

    public function testTemplateIsEmptyByDefault()
    {
        $model    = new ViewModel();
        $template = $model->getTemplate();
        $this->assertTrue(empty($template));
    }

    public function testTemplateIsMutable()
    {
        $model = new ViewModel();
        $model->setTemplate('foo');
        $this->assertEquals('foo', $model->getTemplate());
    }

    public function testIsNotTerminatedByDefault()
    {
        $model = new ViewModel();
        $this->assertFalse($model->terminate());
    }

    public function testTerminationFlagIsMutable()
    {
        $model = new ViewModel();
        $model->setTerminal(true);
        $this->assertTrue($model->terminate());
    }

    public function testAddChildAllowsSpecifyingCaptureToValue()
    {
        $model = new ViewModel();
        $child = new ViewModel();
        $model->addChild($child, 'foo');
        $this->assertTrue($model->hasChildren());
        $this->assertEquals('foo', $child->captureTo());
    }

    public function testAllowsPassingViewVariablesContainerAsVariables()
    {
        $variables = new ViewVariables();
        $model     = new ViewModel();
        $model->setVariables($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testPropertyOverloadingGivesAccessToProperties()
    {
        $model      = new ViewModel();
        $variables  = $model->getVariables();
        $model->foo = 'bar';
        $this->assertTrue(isset($model->foo));
        $this->assertEquals('bar', $variables['foo']);
        $this->assertEquals('bar', $model->foo);

        unset($model->foo);
        $this->assertFalse(isset($model->foo));
        $this->assertFalse(isset($variables['foo']));
    }
}
