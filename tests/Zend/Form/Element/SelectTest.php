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

use Zend\Form\Element\Select as SelectElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\Form\Decorator,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_Select
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->element = new SelectElement('foo');
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testSelectElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof XhtmlElement);
    }

    public function testSelectElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Element);
    }

    public function testSelectElementIsNotAnArrayByDefault()
    {
        $this->assertFalse($this->element->isArray());
    }

    public function testSelectElementUsesSelectHelperInViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Decorator\ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formSelect', $helper);
    }

    public function testCanDisableIndividualSelectOptions()
    {
        $this->element->setMultiOptions(array(
                'foo' => 'foo',
                'bar' => array(
                    'baz' => 'Baz',
                    'bat' => 'Bat'
                ),
                'test' => 'Test',
            ))
            ->setAttrib('disable', array('baz', 'test'));
        $html = $this->element->render($this->getView());
        $this->assertNotRegexp('/<select[^>]*?(disabled="disabled")/', $html, $html);
        foreach (array('baz', 'test') as $test) {
            if (!preg_match('/(<option[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching disabled option for ' . $test);
            }
            $this->assertRegexp('/<option[^>]*?(disabled="disabled")/', $m[1]);
        }
        foreach (array('foo', 'bat') as $test) {
            if (!preg_match('/(<option[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching option for ' . $test);
            }
            $this->assertNotRegexp('/<option[^>]*?(disabled="disabled")/', $m[1], var_export($m, 1));
        }
    }

    /**
     * No explicit assertions; just checking for error conditions
     *
     * @group ZF-2847
     */
    public function testTranslationShouldNotRaiseWarningsWithNestedGroups()
    {
        $translate = new Translator('ArrayAdapter', array('Select Test', 'Select Test Translated'), 'en');
        $this->element
             ->setLabel('Select Test')
             ->setMultiOptions(array(
                 'Group 1' => array(
                     '1-1' => 'Hi 1-1',
                     '1-2' => 'Hi 1-2',
                 ),
                 'Group 2' => array(
                     '2-1' => 'Hi 2-1',
                     '2-2' => 'Hi 2-2',
                 ),
             ))
             ->setTranslator($translate)
             ->setView(new View());
        $html = $this->element->render();
    }

    /**
     * @group ZF-3953
     */
    public function testUsingZeroAsValueShouldSelectAppropriateOption()
    {
        $this->element->setMultiOptions(array(
            array('key' => '1', 'value' => 'Yes'),
            array('key' => '0', 'value' => 'No'),
            array('key' => 'somewhat', 'value' => 'Somewhat'),
        ));
        $this->element->setValue(0);
        $html = $this->element->render($this->getView());

        if (!preg_match('#(<option[^>]*(?:value="somewhat")[^>]*>)#s', $html, $matches)) {
            $this->fail('Could not find option: ' . $html);
        }
        $this->assertNotContains('selected', $matches[1]);
    }

    /**
     * @group ZF-4390
     */
    public function testEmptyOptionsShouldNotBeTranslated()
    {
        $translate = new Translator('ArrayAdapter', array('unused', 'foo' => 'bar'), 'en');
        $this->element->setTranslator($translate);
        $this->element->setMultiOptions(array(
            array('key' => '', 'value' => ''),
            array('key' => 'foo', 'value' => 'foo'),
        ));
        $this->element->setView($this->getView());
        $html = $this->element->render();
        $this->assertNotContains('unused', $html, $html);
        $this->assertContains('bar', $html, $html);
    }

    /**
     * Test isValid() on select elements without optgroups. This
     * ensures fixing ZF-3985 doesn't break existing functionality.
     *
     * @group ZF-3985
     */
    public function testIsValidWithPlainOptions()
    {
        // test both syntaxes for setting plain options
        $this->element->setMultiOptions(array(
            array('key' => '1', 'value' => 'Web Developer'),
            '2' => 'Software Engineer',
        ));

        $this->assertTrue($this->element->isValid('1'));
        $this->assertTrue($this->element->isValid('2'));
        $this->assertFalse($this->element->isValid('3'));
        $this->assertFalse($this->element->isValid('Web Developer'));
    }

    /**
     * @group ZF-3985
     */
    public function testIsValidWithOptionGroups()
    {
        // test optgroup and both syntaxes for setting plain options
        $this->element->setMultiOptions(array(
            'Technology' => array(
                '1' => 'Web Developer',
                '2' => 'Software Engineer',
            ),
            array('key' => '3', 'value' => 'Trainee'),
            '4' => 'Intern',
        ));

        $this->assertTrue($this->element->isValid('1'));
        $this->assertTrue($this->element->isValid('3'));
        $this->assertTrue($this->element->isValid('4'));
        $this->assertFalse($this->element->isValid('5'));
        $this->assertFalse($this->element->isValid('Technology'));
        $this->assertFalse($this->element->isValid('Web Developer'));
    }

    /**
     * @group ZF-8342
     */
    public function testUsingPoundSymbolInOptionLabelShouldRenderCorrectly()
    {
        $this->element->addMultiOption('1', '£' . number_format(1));
        $html = $this->element->render($this->getView());
        $this->assertContains('>£', $html);
    }
}
