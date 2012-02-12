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

use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement,
    Zend\Form\Element\Multi as MultiElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\Form\Decorator,
    Zend\Form\Form,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_MultiCheckbox
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class MultiCheckboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->element = new MultiCheckboxElement('foo');
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testMultiCheckboxElementSubclassesMultiElement()
    {
        $this->assertTrue($this->element instanceof MultiElement);
    }

    public function testMultiCheckboxElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof XhtmlElement);
    }

    public function testMultiCheckboxElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Element);
    }

    public function testMultiCheckboxElementIsAnArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    public function testHelperAttributeSetToFormMultiCheckboxByDefault()
    {
        $this->assertEquals('formMultiCheckbox', $this->element->getAttrib('helper'));
    }

    public function testMultiCheckboxElementUsesMultiCheckboxHelperInViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Decorator\ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formMultiCheckbox', $helper);
    }

    public function testCanDisableIndividualMultiCheckboxOptions()
    {
        $this->element->setMultiOptions(array(
                'foo'  => 'Foo',
                'bar'  => 'Bar',
                'baz'  => 'Baz',
                'bat'  => 'Bat',
                'test' => 'Test',
            ))
            ->setAttrib('disable', array('baz', 'test'));
        $html = $this->element->render($this->getView());
        foreach (array('baz', 'test') as $test) {
            if (!preg_match('/(<input[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching disabled option for ' . $test);
            }
            $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $m[1]);
        }
        foreach (array('foo', 'bar', 'bat') as $test) {
            if (!preg_match('/(<input[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching option for ' . $test);
            }
            $this->assertNotRegexp('/<input[^>]*?(disabled="disabled")/', $m[1], var_export($m, 1));
        }
    }

    public function testSpecifiedSeparatorIsUsedWhenRendering()
    {
        $this->element->setMultiOptions(array(
                'foo'  => 'Foo',
                'bar'  => 'Bar',
                'baz'  => 'Baz',
                'bat'  => 'Bat',
                'test' => 'Test',
            ))
            ->setSeparator('--FooBarFunSep--');
        $html = $this->element->render($this->getView());
        $this->assertContains($this->element->getSeparator(), $html);
        $count = substr_count($html, $this->element->getSeparator());
        $this->assertEquals(4, $count);
    }

    /**
     * @group ZF-2830
     */
    public function testRenderingMulticheckboxCreatesCorrectArrayNotation()
    {
        $this->element->addMultiOption(1, 'A');
        $this->element->addMultiOption(2, 'B');
        $html = $this->element->render($this->getView());
        $this->assertContains('name="foo[]"', $html, $html);
        $count = substr_count($html, 'name="foo[]"');
        $this->assertEquals(2, $count);
    }

    /**
     * @group ZF-2828
     */
    public function testCanPopulateCheckboxOptionsFromPostedData()
    {
        $form = new Form(array(
            'elements' => array(
                '100_1' => array('MultiCheckbox', array(
                    'multiOptions' => array(
                        '100_1_1'  => 'Agriculture',
                        '100_1_2'  => 'Automotive',
                        '100_1_12' => 'Chemical',
                        '100_1_13' => 'Communications',
                    ),
                    'required' => true,
                )),
            ),
        ));
        $data = array(
            '100_1' => array(
                '100_1_1',
                '100_1_2',
                '100_1_12',
                '100_1_13'
            ),
        );
        $form->populate($data);
        $html = $form->render($this->getView());
        foreach ($form->getElement('100_1')->getMultiOptions() as $key => $value) {
            if (!preg_match('#(<input[^>]*' . $key . '[^>]*>)#', $html, $m)) {
                $this->fail('Missing input for a given multi option: ' . $html);
            }
            $this->assertContains('checked="checked"', $m[1]);
        }
    }

    /**
     * @group ZF-3286
     */
    public function testShouldRegisterInArrayValidatorByDefault()
    {
        $this->assertTrue($this->element->registerInArrayValidator());
    }

    /**
     * @group ZF-3286
     */
    public function testShouldAllowSpecifyingWhetherOrNotToUseInArrayValidator()
    {
        $this->testShouldRegisterInArrayValidatorByDefault();
        $this->element->setRegisterInArrayValidator(false);
        $this->assertFalse($this->element->registerInArrayValidator());
        $this->element->setRegisterInArrayValidator(true);
        $this->assertTrue($this->element->registerInArrayValidator());
    }

    /**
     * @group ZF-3286
     */
    public function testInArrayValidatorShouldBeRegisteredAfterValidation()
    {
        $options = array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        );
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->element->isValid('test');
        $validator = $this->element->getValidator('InArray');
        $this->assertTrue($validator instanceof \Zend\Validator\InArray);
    }

    /**
     * @group ZF-3286
     */
    public function testShouldNotValidateIfValueIsNotInArray()
    {
        $options = array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        );
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->assertFalse($this->element->isValid('test'));
    }

    /**
     * No assertion; just making sure no error occurs
     *
     * @group ZF-4915
     */
    public function testRetrievingErrorMessagesShouldNotResultInError()
    {
        $this->element->addMultiOptions(array(
                          'foo' => 'Foo',
                          'bar' => 'Bar',
                          'baz' => 'Baz',
                      ))
                      ->addErrorMessage('%value% is invalid');
        $this->element->isValid(array('foo', 'bogus'));
        $html = $this->element->render($this->getView());
    }
 
    /**
     * @group ZF-11402
    */
    public function testValidateShouldNotAcceptEmptyArray()
    {
        $this->element->addMultiOptions(array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ));
        $this->element->setRegisterInArrayValidator(true);

        $this->assertTrue($this->element->isValid(array('foo')));
        $this->assertTrue($this->element->isValid(array('foo','baz')));

        $this->element->setAllowEmpty(true);
        $this->assertTrue($this->element->isValid(array()));
 
        // Empty value + AllowEmpty=true = no error messages
        $messages = $this->element->getMessages();
        $this->assertEquals(0, count($messages), 'Received unexpected error message(s)');

        $this->element->setAllowEmpty(false);
        $this->assertFalse($this->element->isValid(array()));

        // Empty value + AllowEmpty=false = notInArray error message
        $messages = $this->element->getMessages();
        $this->assertTrue(is_array($messages), 'Expected error message');
        $this->assertArrayHasKey('notInArray', $messages, 'Expected \'notInArray\' error message');

        $this->element->setRequired(true)->setAllowEmpty(false);
        $this->assertFalse($this->element->isValid(array()));

        // Empty value + Required=true + AllowEmpty=false = isEmpty error message
        $messages = $this->element->getMessages();
        $this->assertTrue(is_array($messages), 'Expected error message');
        $this->assertArrayHasKey('isEmpty', $messages, 'Expected \'isEmpty\' error message');
    }
}
