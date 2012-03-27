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
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Dojo\Form\Element;

use Zend\Dojo\Form\Element\TimeTextBox as TimeTextBoxElement,
    Zend\Dojo\Form\Element\DateTextBox as DateTextBoxElement,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_TimeTextBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class TimeTextBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::_unsetInstance();
        DojoHelper::setUseDeclarative();

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        $element = new TimeTextBoxElement(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'TimeTextBox',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testShouldExtendDateTextBox()
    {
        $this->assertTrue($this->element instanceof DateTextBoxElement);
    }

    public function testTimePatternAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getTimePattern());
        $this->assertFalse($this->element->hasConstraint('timePattern'));
        $this->element->setTimePattern('h:mm a');
        $this->assertEquals('h:mm a', $this->element->getTimePattern());
        $this->assertTrue($this->element->hasConstraint('timePattern'));
        $this->assertEquals('h:mm a', $this->element->dijitParams['constraints']['timePattern']);
    }

    public function testClickableIncrementAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getClickableIncrement());
        $this->assertFalse($this->element->hasConstraint('clickableIncrement'));
        $this->element->setClickableIncrement('T00:15:00');
        $this->assertEquals('T00:15:00', $this->element->getClickableIncrement());
        $this->assertTrue($this->element->hasConstraint('clickableIncrement'));
        $this->assertEquals('T00:15:00', $this->element->dijitParams['constraints']['clickableIncrement']);
    }

    public function testClickableIncrementMutatorShouldRaiseExceptionOnInvalidFormat()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException');
        $this->element->setClickableIncrement('en-US');
    }

    public function testVisibleIncrementAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getVisibleIncrement());
        $this->assertFalse($this->element->hasConstraint('visibleIncrement'));
        $this->element->setVisibleIncrement('T00:15:00');
        $this->assertEquals('T00:15:00', $this->element->getVisibleIncrement());
        $this->assertTrue($this->element->hasConstraint('visibleIncrement'));
        $this->assertEquals('T00:15:00', $this->element->dijitParams['constraints']['visibleIncrement']);
    }

    public function testVisibleIncrementMutatorShouldRaiseExceptionOnInvalidFormat()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException');
        $this->element->setVisibleIncrement('en-US');
    }

    public function testVisibleRangeAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getVisibleRange());
        $this->assertFalse($this->element->hasConstraint('visibleRange'));
        $this->element->setVisibleRange('T00:15:00');
        $this->assertEquals('T00:15:00', $this->element->getVisibleRange());
        $this->assertTrue($this->element->hasConstraint('visibleRange'));
        $this->assertEquals('T00:15:00', $this->element->dijitParams['constraints']['visibleRange']);
    }

    public function testVisibleRangeMutatorShouldRaiseExceptionOnInvalidFormat()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException');
        $this->element->setVisibleRange('en-US');
    }

    public function testShouldRenderTimeTextBoxDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.TimeTextBox"', $html);
    }

    /**
     * @group ZF-7813
     */
    public function testCanSetValue()
    {
        $this->element->setValue('T08:00');
        $html = $this->element->render();
        
        $this->assertSame('T08:00', $this->element->getValue());
        $this->assertContains('value="T08:00"', $html);
    }
}
