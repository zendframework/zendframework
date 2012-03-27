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

use Zend\Form\Decorator\ViewScript as ViewScriptDecorator,
    Zend\Form\Element\Text as TextElement,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Decorator_ViewScript
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class ViewScriptTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new ViewScriptDecorator();
    }

    public function getView()
    {
        $view = new View();
        $view->resolver()->addPath(__DIR__ . '/../TestAsset/views/');
        return $view;
    }

    public function getElement()
    {
        $element = new TextElement('foo');
        $element->setView($this->getView());
        $this->decorator->setElement($element);
        return $element;
    }

    public function testRenderRaisesExceptionIfNoViewScriptRegistered()
    {
        $this->setExpectedException('Zend\Form\Decorator\Exception\UnexpectedValueException', 'script');
        $this->getElement();
        $this->decorator->render('');
    }

    public function testViewScriptNullByDefault()
    {
        $this->assertNull($this->decorator->getViewScript());
    }

    public function testCanSetViewScript()
    {
        $this->testViewScriptNullByDefault();
        $this->decorator->setViewScript('decorator.phtml');
        $this->assertEquals('decorator.phtml', $this->decorator->getViewScript());
    }

    public function testCanSetViewScriptViaOption()
    {
        $this->testViewScriptNullByDefault();
        $this->decorator->setOption('viewScript', 'decorator.phtml');
        $this->assertEquals('decorator.phtml', $this->decorator->getViewScript());
    }

    public function testCanSetViewScriptViaElementAttribute()
    {
        $this->testViewScriptNullByDefault();
        $this->getElement()->setAttrib('viewScript', 'decorator.phtml');
        $this->assertEquals('decorator.phtml', $this->decorator->getViewScript());
    }

    public function testRenderingRendersViewScript()
    {
        $this->testCanSetViewScriptViaElementAttribute();
        $test = $this->decorator->render('');
        $this->assertContains('This is content from the view script', $test);
    }

    public function testOptionsArePassedToPartialAsVariables()
    {
        $this->decorator->setOptions(array(
            'foo'        => 'Foo Value',
            'bar'        => 'Bar Value',
            'baz'        => 'Baz Value',
            'bat'        => 'Bat Value',
            'viewScript' => 'decorator.phtml',
        ));
        $this->getElement();
        $test = $this->decorator->render('');
        foreach ($this->decorator->getOptions() as $key => $value) {
            $this->assertContains("$key: $value", $test);
        }
    }

    public function testCanReplaceContentBySpecifyingFalsePlacement()
    {
        $this->decorator->setViewScript('replacingDecorator.phtml')
             ->setOption('placement', false)
             ->setElement($this->getElement());
        $test = $this->decorator->render('content to decorate');
        $this->assertNotContains('content to decorate', $test, $test);
        $this->assertContains('This is content from the view script', $test);
    }

    public function testContentCanBeRenderedWithinViewScript()
    {
        $this->decorator->setViewScript('contentWrappingDecorator.phtml')
             ->setOption('placement', false)
             ->setElement($this->getElement());

        $test = $this->decorator->render('content to decorate');
        $this->assertContains('content to decorate', $test, $test);
        $this->assertContains('This text prefixes the content', $test);
        $this->assertContains('This text appends the content', $test);
    }

    public function testDecoratorCanControlPlacementFromWithinViewScript()
    {
        $this->decorator->setViewScript('decoratorCausesReplacement.phtml')
             ->setElement($this->getElement());

        $test = $this->decorator->render('content to decorate');
        $this->assertContains('content to decorate', $test, $test);

        $count = substr_count($test, 'content to decorate');
        $this->assertEquals(1, $count);

        $this->assertContains('This text prefixes the content', $test);
        $this->assertContains('This text appends the content', $test);
    }
}
