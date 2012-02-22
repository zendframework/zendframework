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

use Zend\Dojo\Form\Element\DateTextBox as DateTextBoxElement,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_DateTextBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class DateTextBoxTest extends \PHPUnit_Framework_TestCase
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
        $element = new DateTextBoxElement(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'DateTextBox',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testAmPmAccessorsShouldProxyToConstraints()
    {
        $this->assertFalse($this->element->getAmPm());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('am,pm', $constraints));
        $this->element->setAmPm(true);
        $this->assertTrue($this->element->getAmPm());
        $constraints = $this->element->getConstraints();
        $this->assertTrue(array_key_exists('am,pm', $constraints));
        $this->assertEquals('true', $this->element->dijitParams['constraints']['am,pm']);
    }

    public function testStrictAccessorsShouldProxyToConstraints()
    {
        $this->assertFalse($this->element->getStrict());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('strict', $constraints));
        $this->element->setStrict(true);
        $this->assertTrue($this->element->getStrict());
        $constraints = $this->element->getConstraints();
        $this->assertTrue(array_key_exists('strict', $constraints));
        $this->assertEquals('true', $this->element->dijitParams['constraints']['strict']);
    }

    public function testLocaleAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getLocale());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('locale', $constraints));
        $this->element->setLocale('en-US');
        $this->assertEquals('en-US', $this->element->getLocale());
        $constraints = $this->element->getConstraints();
        $this->assertEquals('en-US', $this->element->dijitParams['constraints']['locale']);
    }

    public function testFormatLengthAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getFormatLength());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('formatLength', $constraints));
        $this->element->setFormatLength('long');
        $this->assertEquals('long', $this->element->getFormatLength());
        $constraints = $this->element->getConstraints();
        $this->assertEquals('long', $this->element->dijitParams['constraints']['formatLength']);
    }

    public function testFormatLengthMutatorShouldThrowExceptionWithInvalidFormatLength()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException');
        $this->element->setFormatLength('foobar');
    }

    public function testSelectorAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getSelector());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('selector', $constraints));
        $this->element->setSelector('time');
        $this->assertEquals('time', $this->element->getSelector());
        $constraints = $this->element->getConstraints();
        $this->assertEquals('time', $this->element->dijitParams['constraints']['selector']);
    }

    public function testSelectorMutatorShouldThrowExceptionWithInvalidSelector()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException');
        $this->element->setSelector('foobar');
    }

    public function testDatePatternAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getDatePattern());
        $this->assertFalse($this->element->hasConstraint('datePattern'));
        $this->element->setDatePattern('EEE, MMM d, Y');
        $this->assertEquals('EEE, MMM d, Y', $this->element->getDatePattern());
        $this->assertEquals('EEE, MMM d, Y', $this->element->dijitParams['constraints']['datePattern']);
    }

    public function testShouldRenderDateTextBoxDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.DateTextBox"', $html);
    }

    /**
     * @group ZF-7813
     */
    public function testCanSetValue()
    {
        $this->element->setValue('2011-05-10');
        $html = $this->element->render();
        
        $this->assertSame('2011-05-10', $this->element->getValue());
        $this->assertContains('value="2011-05-10"', $html);
    }
}
