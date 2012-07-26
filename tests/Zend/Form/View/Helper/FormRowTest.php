<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element;
use Zend\Form\View\HelperConfig;
use Zend\Form\View\Helper\FormRow as FormRowHelper;
use Zend\View\Renderer\PhpRenderer;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
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
        $config  = new HelperConfig();
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

    public function testCanRenderRowLabelAttributes()
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
        $this->assertRegexp('/<input name="foo" type="text"[^\/>]*\/?>/', $markup);
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
