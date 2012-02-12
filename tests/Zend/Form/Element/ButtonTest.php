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

namespace ZendTest\Form\Element;

use Zend\Form\Element\Button as ButtonElement,
    Zend\Form\Element\Submit as SubmitElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\Form\Decorator,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_Button
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class ButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->element = new ButtonElement('foo');
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testButtonElementSubclassesSubmitElement()
    {
        $this->assertTrue($this->element instanceof SubmitElement);
    }

    public function testButtonElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof XhtmlElement);
    }

    public function testButtonElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Element);
    }

    public function testHelperAttributeSetToFormButtonByDefault()
    {
        $this->assertEquals('formButton', $this->element->getAttrib('helper'));
    }

    public function testButtonElementUsesButtonHelperInViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Decorator\ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formButton', $helper);
    }

    public function testGetLabelReturnsTranslatedLabelIfTranslatorIsRegistered()
    {
        $translations = include __DIR__ . '/../TestAsset/locale/array.php';
        $translate = new Translator('ArrayAdapter', $translations, 'en');
        $this->element->setTranslator($translate)
                      ->setLabel('submit');
        $test = $this->element->getLabel();
        $this->assertEquals($translations['submit'], $test);
    }

    public function testTranslatedLabelIsRendered()
    {
        $this->testGetLabelReturnsTranslatedLabelIfTranslatorIsRegistered();
        $this->element->setView($this->getView());
        $decorator = $this->element->getDecorator('ViewHelper');
        $decorator->setElement($this->element);
        $html = $decorator->render('');
        $this->assertRegexp('/<(input|button)[^>]*?>Submit Button/', $html, $html);
    }

    /**
     * @group ZF-3961
     */
    public function testValuePropertyShouldNotBeRendered()
    {
        $this->element->setLabel('Button Label')
                      ->setView($this->getView());
        $html = $this->element->render();
        $this->assertContains('Button Label', $html, $html);
        $this->assertNotContains('value="', $html);
    }
    
    public function testSetDefaultIgnoredToTrueWhenNotDefined()
    {
        $this->assertTrue($this->element->getIgnore());
    }
}
