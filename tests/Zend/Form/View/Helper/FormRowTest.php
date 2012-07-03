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
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element;
use Zend\Form\View\HelperConfiguration;
use Zend\Form\View\Helper\FormRow as FormRowHelper;
use Zend\View\Renderer\PhpRenderer;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormRowTest extends TestCase
{
    protected $helper;
    protected $renderer;

    public function setUp()
    {
        $this->helper = new FormRowHelper();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfiguration();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
    }

    public function testCanGenerateLabel()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $markup = $this->helper->render($element);
        $this->assertContains('>The value for foo:<', $markup);
        $this->assertContains('<label', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testCanCreateLabelValueBeforeInput()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $this->helper->setLabelPosition('prepend');
        $markup = $this->helper->render($element);
        $this->assertContains('<label>The value for foo:<', $markup);
        $this->assertContains('</label>', $markup);
    }

    public function testCanCreateLabelValueAfterInput()
    {
        $element = new Element('foo');
        $element->setOptions(array(
            'label' => 'The value for foo:',
        ));
        $this->helper->setLabelPosition('append');
        $markup = $this->helper->render($element);
        $this->assertContains('<label><input', $markup);
        $this->assertContains('</label>', $markup);
    }

    function testCanRenderRowLabelAttributes()
    {
        $element = new Element('foo');
        $element->setLabel('The value for foo:');
        $element->setLabelAttributes(array('class' => 'bar'));
        $this->helper->setLabelPosition('append');
        $markup = $this->helper->render($element);
        $this->assertContains("<label class=\"bar\">", $markup);
    }

    public function testCanCreateMarkupWithoutLabel()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'text');
        $markup = $this->helper->render($element);
        $this->assertRegexp('/<input name="foo" type="text"(\s*\/)?>/', $markup);
    }

    public function testCanHandleMultiCheckboxesCorrectly()
    {
        $options = array(
            'This is the first label' => 'value1',
            'This is the second label' => 'value2',
            'This is the third label' => 'value3',
        );

        $element = new Element\MultiCheckbox('foo');
        $element->setAttribute('type', 'multi_checkbox');
        $element->setAttribute('options', $options);
        $element->setLabel('This is a multi-checkbox');
        $markup = $this->helper->render($element);
        $this->assertContains("<fieldset>", $markup);
        $this->assertContains("<legend>", $markup);
        $this->assertContains("<label>", $markup);
    }

    public function testCanRenderErrors()
    {
        $element  = new Element('foo');
        $element->setMessages(array(
            'First error message',
            'Second error message',
            'Third error message',
        ));

        $markup = $this->helper->render($element);
        $this->assertRegexp('#<ul>\s*<li>First error message</li>\s*<li>Second error message</li>\s*<li>Third error message</li>\s*</ul>#s', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
