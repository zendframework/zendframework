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

use Zend\Form\Decorator\ViewHelper as ViewHelperDecorator,
    Zend\Form\Element,
    Zend\Form\Element\Select as SelectElement,
    Zend\Form\Element\Text as TextElement,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Decorator_ViewHelper
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class ViewHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new ViewHelperDecorator();
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function getElement()
    {
        $element = new TextElement('foo');
        $this->decorator->setElement($element);
        return $element;
    }

    public function testGetHelperWillUseElementHelperAttributeInAbsenceOfHelper()
    {
        $element = new Element('foo');
        $element->helper = 'formTextarea';
        $this->decorator->setElement($element);
        $this->assertEquals('formTextarea', $this->decorator->getHelper());
    }

    public function testGetHelperWillUseElementTypeInAbsenceOfHelper()
    {
        $element = new \ZendTest\Form\TestAsset\Element\Textarea('foo');
        $this->decorator->setElement($element);
        $this->assertEquals('formTextarea', $this->decorator->getHelper());
    }

    public function testGetHelperWillUseHelperProvidedInOptions()
    {
        $this->decorator->setOptions(array('helper' => 'formSubmit'));
        $this->assertEquals('formSubmit', $this->decorator->getHelper());
    }

    public function testGetHelperReturnsNullByDefault()
    {
        $this->assertNull($this->decorator->getHelper());
    }

    public function testCanSetHelper()
    {
        $this->decorator->setHelper('formSubmit');
        $this->assertEquals('formSubmit', $this->decorator->getHelper());
    }

    public function testAppendsBracketsIfElementIsAnArray()
    {
        $element = $this->getElement();
        $element->setIsArray(true);
        $name = $this->decorator->getName();
        $expect = $element->getName() . '[]';
        $this->assertEquals($expect, $name);
    }

    /**
     * This test is obsolete, as a view is always lazy loaded now
     * @group disable
     */
    public function testRenderThrowsExceptionIfNoViewSetInElement()
    {
        $element = $this->getElement();
        $content = 'test content';
        $this->setExpectedException('Zend\Form\Decorator\Exception\UnexpectedValueException', 'ViewHelper decorator cannot render');
        $test = $this->decorator->render($content);
    }

    public function testRenderRendersElementWithSpecifiedHelper()
    {
        $element = $this->getElement();
        $element->setView($this->getView());
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content, $test);
        $this->assertRegexp('#<input.*?name="foo"#s', $test);
    }

    public function testMultiOptionsPassedToViewHelperAreTranslated()
    {
        $element = new SelectElement('foo');
        $options = array(
            'foo' => 'This Foo Will Not Be Displayed',
            'bar' => 'This Bar Will Not Be Displayed',
            'baz' => 'This Baz Will Not Be Displayed',
        );
        $element->setMultiOptions($options);

        $translations = array(
            'This Foo Will Not Be Displayed' => 'This is the Foo Value',
            'This Bar Will Not Be Displayed' => 'This is the Bar Value',
            'This Baz Will Not Be Displayed' => 'This is the Baz Value',
        );
        $translate = new Translator('ArrayAdapter', $translations, 'en');
        $translate->setLocale('en');

        $element->setTranslator($translate);
        $test = $element->render($this->getView());
        foreach ($options as $key => $value) {
            $this->assertNotContains($value, $test);
            $this->assertContains($translations[$value], $test);
        }
    }
}
