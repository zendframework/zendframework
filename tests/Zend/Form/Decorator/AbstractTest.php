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
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Decorator;

use Zend\Form\Decorator,
    Zend\Form\DisplayGroup,
    Zend\Form\Element,
    Zend\Form\Form,
    Zend\Config\Config,
    Zend\Loader\PrefixPathLoader;

/**
 * Test class for Zend_Form_Decorator_Abstract
 *
 * Uses Zend_Form_Decorator_Errors as a concrete implementation
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new Decorator\Errors();
    }

    public function getOptions()
    {
        $options = array(
            'foo' => 'fooval',
            'bar' => 'barval',
            'baz' => 'bazval'
        );
        return $options;
    }

    public function testCanSetOptions()
    {
        $options = $this->getOptions();
        $this->decorator->setOptions($options);
        $this->assertEquals($options, $this->decorator->getOptions());
    }

    public function testCanSetOptionsFromConfigObject()
    {
        $config = new Config($this->getOptions());
        $this->decorator->setConfig($config);
        $this->assertEquals($config->toArray(), $this->decorator->getOptions());
    }

    public function testSetElementAllowsFormElements()
    {
        $element = new Element('foo');
        $this->decorator->setElement($element);
        $this->assertSame($element, $this->decorator->getElement());
    }

    public function testSetElementAllowsForms()
    {
        $form = new Form();
        $this->decorator->setElement($form);
        $this->assertSame($form, $this->decorator->getElement());
    }

    public function testSetElementAllowsDisplayGroups()
    {
        $loader = new PrefixPathLoader(array('Zend\Form\Decorator' => 'Zend/Form/Decorator'));
        $group  = new DisplayGroup('foo', $loader);
        $this->decorator->setElement($group);
        $this->assertSame($group, $this->decorator->getElement());
    }

    public function testSetElementThrowsExceptionWithInvalidElementTypes()
    {
        $config = new Config(array());
        $this->setExpectedException('Zend\Form\Decorator\Exception\InvalidArgumentException', 'Invalid element');
        $this->decorator->setElement($config);
    }

    public function testPlacementDefaultsToAppend()
    {
        $this->assertEquals(Decorator\AbstractDecorator::APPEND, $this->decorator->getPlacement());
    }

    public function testCanSetPlacementViaPlacementOption()
    {
        $this->testPlacementDefaultsToAppend();
        $this->decorator->setOptions(array('placement' => 'PREPEND'));
        $this->assertEquals(Decorator\AbstractDecorator::PREPEND, $this->decorator->getPlacement());
    }

    public function testSeparatorDefaultsToPhpEol()
    {
        $this->assertEquals(PHP_EOL, $this->decorator->getSeparator());
    }

    public function testCanSetSeparatorViaSeparatorOption()
    {
        $this->testSeparatorDefaultsToPhpEol();
        $this->decorator->setOptions(array('separator' => '<br />'));
        $this->assertEquals('<br />', $this->decorator->getSeparator());
    }

    public function testCanSetIndividualOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->decorator->setOption('foo', 'bar');
        $this->assertEquals('bar', $this->decorator->getOption('foo'));
    }

    public function testCanRemoveIndividualOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->decorator->setOption('foo', 'bar');
        $this->assertEquals('bar', $this->decorator->getOption('foo'));
        $this->decorator->removeOption('foo');
        $this->assertNull($this->decorator->getOption('foo'));
    }

    public function testCanClearAllOptions()
    {
        $this->assertNull($this->decorator->getOption('foo'));
        $this->assertNull($this->decorator->getOption('bar'));
        $this->assertNull($this->decorator->getOption('baz'));
        $options = array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat');
        $this->decorator->setOptions($options);
        $received = $this->decorator->getOptions();
        $this->assertEquals($options, $received);
        $this->decorator->clearOptions();
        $this->assertEquals(array(), $this->decorator->getOptions());
    }
}
