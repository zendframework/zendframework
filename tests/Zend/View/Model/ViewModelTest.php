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
    Zend\View\Model\ViewModel;

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
        $this->assertEquals(array(), $model->getVariables());
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
}
