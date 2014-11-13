<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\View\HelperConfig;
use Zend\Form\View\Helper\FormCollection as FormCollectionHelper;
use Zend\View\Helper\Doctype;
use Zend\View\Renderer\PhpRenderer;
use ZendTest\Form\TestAsset\FormCollection;
use ZendTest\Form\TestAsset\CustomViewHelper;
use ZendTest\Form\TestAsset\CustomFieldsetHelper;

class FormCollectionTest extends TestCase
{
    public $helper;
    public $form;
    public $renderer;

    public function setUp()
    {
        $this->helper = new FormCollectionHelper();

        Doctype::unsetDoctypeRegistry();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfig();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
    }

    public function getForm()
    {
        $form = new FormCollection();
        $form->prepare();

        return $form;
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCanGenerateTemplate()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);

        $markup = $this->helper->render($collection);
        $this->assertContains('<span data-template', $markup);
        $this->assertContains($collection->getTemplatePlaceholder(), $markup);
    }

    public function testDoesNotGenerateTemplateByDefault()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(false);

        $markup = $this->helper->render($collection);
        $this->assertNotContains('<span data-template', $markup);
    }

    public function testCorrectlyIndexElementsInCollection()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');

        $markup = $this->helper->render($collection);
        $this->assertContains('name="colors&#x5B;0&#x5D;"', $markup);
        $this->assertContains('name="colors&#x5B;1&#x5D;"', $markup);
    }

    public function testCorrectlyIndexNestedElementsInCollection()
    {
        $form = $this->getForm();
        $collection = $form->get('fieldsets');

        $markup = $this->helper->render($collection);
        $this->assertContains('fieldsets&#x5B;0&#x5D;&#x5B;field&#x5D;', $markup);
        $this->assertContains('fieldsets&#x5B;1&#x5D;&#x5B;field&#x5D;', $markup);
        $this->assertContains('fieldsets&#x5B;1&#x5D;&#x5B;nested_fieldset&#x5D;&#x5B;anotherField&#x5D;', $markup);
    }

    public function testRenderWithCustomHelper()
    {
        $form = $this->getForm();

        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(false);

        $elementHelper = new CustomViewHelper();
        $elementHelper->setView($this->renderer);

        $markup = $this->helper->setElementHelper($elementHelper)->render($collection);

        $this->assertContains('id="customcolors0"', $markup);
        $this->assertContains('id="customcolors1"', $markup);
    }

    public function testRenderWithCustomFieldsetHelper()
    {
        $form = $this->getForm();

        $fieldsetHelper = new CustomFieldsetHelper();
        $fieldsetHelper->setView($this->renderer);

        $markup = $this->helper->setFieldsetHelper($fieldsetHelper)->render($form);

        $this->assertContains('id="customFieldsetcolors"', $markup);
        $this->assertContains('id="customFieldsetfieldsets"', $markup);
    }

    public function testShouldWrapReturnsDefaultTrue()
    {
        $this->assertTrue($this->helper->shouldWrap());
    }

    public function testSetShouldWrapReturnsFalse()
    {
        $this->helper->setShouldWrap(false);
        $this->assertFalse($this->helper->shouldWrap());
    }

    public function testGetDefaultElementHelperReturnsFormrow()
    {
        $defaultElement = $this->helper->getDefaultElementHelper();
        $this->assertSame('formrow', $defaultElement);
    }

    public function testSetDefaultElementHelperToFoo()
    {
        $this->helper->setDefaultElementHelper('foo');
        $defaultElement = $this->helper->getDefaultElementHelper();
        $this->assertSame('foo', $defaultElement);
    }

    public function testCanRenderTemplateAlone()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);

        $markup = $this->helper->renderTemplate($collection);
        $this->assertContains('<span data-template', $markup);
        $this->assertContains($collection->getTemplatePlaceholder(), $markup);
    }

    public function testCanTranslateLegend()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('untranslated legend');
        $this->helper->setShouldWrap(true);

        $mockTranslator = $this->getMock('Zend\I18n\Translator\Translator');
        $mockTranslator->expects($this->exactly(1))
                       ->method('translate')
                       ->will($this->returnValue('translated legend'));

        $this->helper->setTranslator($mockTranslator);
        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->render($collection);

        $this->assertContains('>translated legend<', $markup);
    }

    public function testShouldWrapWithoutLabel()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);
        $this->assertContains('<fieldset>', $markup);
    }

    public function testRenderCollectionAttributes()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('label');
        $this->helper->setShouldWrap(true);
        $collection->setAttribute('id', 'some-identifier');

        $markup = $this->helper->render($collection);
        $this->assertContains(' id="some-identifier"', $markup);
    }

    public function testCanRenderFieldsetWithoutAttributes()
    {
        $form = $this->getForm();
        $html = $this->helper->render($form);
        $this->assertContains('<fieldset>', $html);
    }

    public function testCanRenderFieldsetWithAttributes()
    {
        $form = $this->getForm();
        $form->setAttributes(array(
            'id'    => 'foo-id',
            'class' => 'foo',
        ));
        $html = $this->helper->render($form);
        $this->assertRegexp('#<fieldset( [a-zA-Z]+\="[^"]+")+>#', $html);
        $this->assertContains('id="foo-id"', $html);
        $this->assertContains('class="foo"', $html);
    }

    public function testCanRenderWithoutLegend()
    {
        $form = $this->getForm();
        $html = $this->helper->render($form);
        $this->assertNotContains('<legend', $html);
        $this->assertNotContains('</legend>', $html);
    }

    public function testRendersLabelAsLegend()
    {
        $form = $this->getForm();
        $form->setLabel('Foo');
        $html = $this->helper->render($form);
        $this->assertRegExp('#<legend[^>]*>Foo#', $html);
        $this->assertContains('</legend>', $html);
    }

    public function testCollectionIsWrappedByFieldsetWithoutLegend()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);

        $this->assertNotContains('<legend>', $markup);
        $this->assertStringStartsWith('<fieldset>', $markup);
        $this->assertStringEndsWith('</fieldset>', $markup);
    }

    public function testCollectionIsWrappedByFieldsetWithLabel()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('foo');
        $this->helper->setShouldWrap(true);

        $markup = $this->helper->render($collection);

        $this->assertContains('<legend>foo</legend>', $markup);
        $this->assertStringStartsWith('<fieldset>', $markup);
        $this->assertStringEndsWith('</fieldset>', $markup);
    }

    public function testCollectionIsWrappedByCustomElement()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%2$s%1$s%3$s</div>');

        $markup = $this->helper->render($collection);

        $this->assertNotContains('<legend>', $markup);
        $this->assertStringStartsWith('<div>', $markup);
        $this->assertStringEndsWith('</div>', $markup);

    }

    public function testCollectionContainsTemplateAtPos3()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%3$s%2$s%1$s</div>');

        $markup = $this->helper->render($collection);

        $this->assertNotContains('<legend>', $markup);
        $this->assertStringStartsWith('<div><span', $markup);
        $this->assertStringEndsWith('</div>', $markup);
    }

    public function testCollectionRendersLabelCorrectly()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('foo');
        $this->helper->setShouldWrap(true);
        $this->helper->setLabelWrapper('<h1>%s</h1>');

        $markup = $this->helper->render($collection);

        $this->assertContains('<h1>foo</h1>', $markup);
        $this->assertStringStartsWith('<fieldset><h1>foo</h1>', $markup);
    }

    public function testCollectionCollectionRendersTemplateCorrectly()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        $this->assertNotContains('<legend>', $markup);
        $this->assertRegExp('/\<div class="foo">.*?<\/div>/', $markup);

    }

    public function testCollectionRendersTemplateWithoutWrapper()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(false);
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        $this->assertNotContains('<fieldset>', $markup);
        $this->assertRegExp('/\<div class="foo">.*?<\/div>/', $markup);
    }

    public function testCollectionRendersFieldsetCorrectly()
    {
        $form = $this->getForm();
        $collection = $form->get('fieldsets');
        $collection->setShouldCreateTemplate(true);
        $this->helper->setShouldWrap(true);
        $this->helper->setWrapper('<div>%2$s%1$s%3$s</div>');
        $this->helper->setTemplateWrapper('<div class="foo">%s</div>');

        $markup = $this->helper->render($collection);

        $this->assertNotContains('<fieldset>', $markup);
        $this->assertRegExp('/\<div class="foo">.*?<\/div>/', $markup);
    }

    public function testGetterAndSetter()
    {
        $this->assertSame($this->helper, $this->helper->setWrapper('foo'));
        $this->assertAttributeEquals('foo', 'wrapper', $this->helper);
        $this->assertEquals('foo', $this->helper->getWrapper());
        $this->assertSame($this->helper, $this->helper->setLabelWrapper('foo'));
        $this->assertAttributeEquals('foo', 'labelWrapper', $this->helper);
        $this->assertEquals('foo', $this->helper->getLabelWrapper());
        $this->assertSame($this->helper, $this->helper->setTemplateWrapper('foo'));
        $this->assertAttributeEquals('foo', 'templateWrapper', $this->helper);
        $this->assertEquals('foo', $this->helper->getTemplateWrapper());
    }

    public function testLabelIsEscapedByDefault()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('<strong>Some label</strong>');
        $markup = $this->helper->render($collection);
        $this->assertRegexp('#<fieldset(.*?)><legend>&lt;strong&gt;Some label&lt;/strong&gt;<\/legend>(.*?)<\/fieldset>#', $markup);
    }

    public function testCanDisableLabelHtmlEscape()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setLabel('<strong>Some label</strong>');
        $collection->setLabelOptions(array('disable_html_escape' => true));
        $markup = $this->helper->render($collection);
        $this->assertRegexp('#<fieldset(.*?)><legend><strong>Some label</strong><\/legend>(.*?)<\/fieldset>#', $markup);
    }
}
