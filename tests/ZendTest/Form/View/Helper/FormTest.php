<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper;

use Zend\Form\Element\Submit;
use ZendTest\Form\TestAsset\CityFieldset;
use Zend\Form\Form;
use Zend\Form\View\Helper\Form as FormHelper;
use Zend\View\Helper\Doctype;

class FormTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormHelper();
        parent::setUp();
    }

    public function testInvokeReturnsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCallingOpenTagWithoutProvidingFormResultsInEmptyActionAndGetMethod()
    {
        $markup = $this->helper->openTag();
        $this->assertContains('<form', $markup);
        $this->assertContains('action=""', $markup);
        $this->assertContains('method="get"', $markup);
    }

    public function testCallingCloseTagEmitsClosingFormTag()
    {
        $markup = $this->helper->closeTag();
        $this->assertEquals('</form>', $markup);
    }

    public function testCallingOpenTagWithFormUsesFormAttributes()
    {
        $form = new Form();
        $attributes = array(
            'method'  => 'post',
            'action'  => 'http://localhost/endpoint',
            'class'   => 'login',
            'id'      => 'form-login',
            'enctype' => 'application/x-www-form-urlencoded',
            'target'  => '_self',
        );
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);

        $escape = $this->renderer->plugin('escapehtmlattr');
        foreach ($attributes as $attribute => $value) {
            $this->assertContains(sprintf('%s="%s"', $attribute, $escape($value)), $markup);
        }
    }

    public function testOpenTagUsesNameAsIdIfNoIdAttributePresent()
    {
        $form = new Form();
        $attributes = array(
            'name'  => 'login-form',
        );
        $form->setAttributes($attributes);

        $markup = $this->helper->openTag($form);
        $this->assertContains('name="login-form"', $markup);
        $this->assertContains('id="login-form"', $markup);
    }

    public function testRender()
    {
        $form = new Form();
        $attributes = array('name'  => 'login-form');
        $form->setAttributes($attributes);
        $form->add(new CityFieldset());
        $form->add(new Submit('send'));

        $markup = $this->helper->__invoke($form);

        $this->assertContains('<form', $markup);
        $this->assertContains('id="login-form"', $markup);
        $this->assertContains('<label><span>Name of the city</span>', $markup);
        $this->assertContains('<fieldset><legend>Country</legend>', $markup);
        $this->assertContains('<input type="submit" name="send"', $markup);
        $this->assertContains('</form>', $markup);
    }

    public function testRenderPreparesForm()
    {
        $form = $this->getMock('Zend\\Form\\Form');
        $form->expects($this->once())->method('prepare');
        $form->expects($this->any())->method('getAttributes')->will($this->returnValue(array()));
        $form->expects($this->any())->method('getIterator')->will($this->returnValue(new \ArrayIterator(array())));

        $markup = $this->helper->__invoke($form);

        $this->assertContains('<form', $markup);
        $this->assertContains('</form>', $markup);
    }

    public function testHtml5DoesNotAddEmptyActionAttributeToFormTag()
    {
        $helper = new FormHelper();

        $helper->setDoctype(Doctype::HTML4_LOOSE);
        $html4Markup = $helper->openTag();
        $helper->setDoctype(Doctype::HTML5);
        $html5Markup = $helper->openTag();
        $helper->setDoctype(Doctype::XHTML5);
        $xhtml5Markup = $helper->openTag();

        $this->assertContains('action=""', $html4Markup);
        $this->assertNotContains('action=""', $html5Markup);
        $this->assertNotContains('action=""', $xhtml5Markup);
    }

    public function testHtml5DoesNotSetDefaultMethodAttributeInFormTag()
    {
        $helper = new FormHelper();

        $helper->setDoctype(Doctype::HTML4_LOOSE);
        $html4Markup = $helper->openTag();
        $helper->setDoctype(Doctype::HTML5);
        $html5Markup = $helper->openTag();
        $helper->setDoctype(Doctype::XHTML5);
        $xhtml5Markup = $helper->openTag();

        $this->assertContains('method="get"', $html4Markup);
        $this->assertNotContains('method="get"', $html5Markup);
        $this->assertNotContains('method="get"', $xhtml5Markup);
    }
}
