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

use Zend\Form\Element\Password as PasswordElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\Form\Decorator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_Password
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class PasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->errors = array();
        $this->element = new PasswordElement('foo');
    }

    public function testPasswordElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof XhtmlElement);
    }

    public function testPasswordElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Element);
    }

    public function testHelperAttributeSetToFormPasswordByDefault()
    {
        $this->assertEquals('formPassword', $this->element->getAttrib('helper'));
    }

    public function testPasswordElementUsesPasswordHelperInViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Decorator\ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formPassword', $helper);
    }

    public function testPasswordValueMaskedByGetMessages()
    {
        $this->element->addValidators(array(
            'Alpha',
            'Alnum'
        ));
        $value  = 'abc-123';
        $expect = '*******';
        $this->assertFalse($this->element->isValid($value));
        foreach ($this->element->getMessages() as $message) {
            $this->assertNotContains($value, $message);
            $this->assertContains($expect, $message, $message);
        }
    }

    public function handleErrors($errno, $errmsg, $errfile, $errline, $errcontext)
    {
        if (!isset($this->errors)) {
            $this->errors = array();
        }
        $this->errors[] = $errmsg;
    }

    /**
     * @group ZF-2656
     */
    public function testGetMessagesReturnsEmptyArrayWhenNoMessagesRegistered()
    {
        set_error_handler(array($this, 'handleErrors'));
        $messages = $this->element->getMessages();
        restore_error_handler();
        $this->assertSame(array(), $messages);
        $this->assertTrue(empty($this->errors));
    }

    public function testRenderPasswordAttributeShouldDefaultToFalse()
    {
        $this->assertFalse($this->element->renderPassword());
    }

    public function testShouldAllowSettingRenderPasswordFlag()
    {
        $this->testRenderPasswordAttributeShouldDefaultToFalse();
        $this->element->setRenderPassword(true);
        $this->assertTrue($this->element->renderPassword());
        $this->element->setRenderPassword(false);
        $this->assertFalse($this->element->renderPassword());
    }

    public function testShouldPassRenderPasswordAttributeToViewHelper()
    {
        $this->element->setValue('foobar')
                      ->setView(new View());
        $test = $this->element->render();
        $this->assertContains('value=""', $test);

        $this->element->setRenderPassword(true);
        $test = $this->element->render();
        $this->assertContains('value="foobar"', $test);
    }
}
