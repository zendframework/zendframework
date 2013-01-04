<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Model;

use ArrayObject;
use stdClass;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Model\ViewModel;
use Zend\View\Variables as ViewVariables;
use ZendTest\View\Model\TestAsset\Variable;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 */
class ViewModelTest extends TestCase
{
    public function testImplementsModelInterface()
    {
        $model = new ViewModel();
        $this->assertInstanceOf('Zend\View\Model\ModelInterface', $model);
    }

    public function testImplementsClearableModelInterface()
    {
        $model = new ViewModel();
        $this->assertInstanceOf('Zend\View\Model\ClearableModelInterface', $model);
    }

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

    public function testAllowsPassingNonArrayAccessObjectsAsArrayInConstructor()
    {
        $vars  = array('foo' => new Variable);
        $model = new ViewModel($vars);
        $this->assertSame($vars, $model->getVariables());
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

    public function testSetVariablesMergesWithPreviouslyStoredVariables()
    {
        $model = new ViewModel(array('foo' => 'bar', 'bar' => 'baz'));
        $model->setVariables(array('bar' => 'BAZBAT'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'BAZBAT'), $model->getVariables());
        return $model;
    }

    public function testCanUnsetVariable()
    {
        $model = new ViewModel(array('foo' => 'bar'));
        $model->__unset('foo');
        $this->assertEquals(array(), $model->getVariables());
    }

    /**
     * @depends testSetVariablesMergesWithPreviouslyStoredVariables
     */
    public function testCanClearAllVariables(ViewModel $model)
    {
        $model->clearVariables();
        $vars = $model->getVariables();
        $this->assertEquals(0, count($vars));
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
        return $model;
    }

    public function testOptionsAreInternallyConvertedToAnArrayFromTraversables()
    {
        $options = new ArrayObject(array('foo' => 'bar'));
        $model = new ViewModel();
        $model->setOptions($options);
        $this->assertEquals($options->getArrayCopy(), $model->getOptions());
    }

    /**
     * @depends testSetOptionsOverwritesAllPreviouslyStored
     */
    public function testCanClearOptions(ViewModel $model)
    {
        $model->clearOptions();
        $this->assertEquals(array(), $model->getOptions());
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
        return $model;
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

    /**
     * @depends testCanCountChildren
     */
    public function testCanClearChildren(ViewModel $model)
    {
        $model->clearChildren();
        $this->assertEquals(0, count($model));
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

    public function testAllowsPassingViewVariablesContainerAsVariablesToConstructor()
    {
        $variables = new ViewVariables();
        $model     = new ViewModel($variables);
        $this->assertSame($variables, $model->getVariables());
    }

    public function testPassingOverwriteFlagWhenSettingVariablesOverwritesContainer()
    {
        $variables = new ViewVariables(array('foo' => 'bar'));
        $model     = new ViewModel($variables);
        $overwrite = new ViewVariables(array('foo' => 'baz'));
        $model->setVariables($overwrite, true);
        $this->assertSame($overwrite, $model->getVariables());
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

    public function testPropertyOverloadingAllowsWritingPropertiesAfterSetVariablesHasBeenCalled()
    {
        $model = new ViewModel();
        $model->setVariables(array('foo' => 'bar'));
        $model->bar = 'baz';

        $this->assertTrue(isset($model->bar));
        $this->assertEquals('baz', $model->bar);
        $variables = $model->getVariables();
        $this->assertTrue(isset($variables['bar']));
        $this->assertEquals('baz', $variables['bar']);
    }
}
