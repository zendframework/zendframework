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

use Zend\Form\Element\Image as ImageElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\Form\Decorator,
    Zend\Translator\Adapter\ArrayAdapter as ArrayTranslator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_Image
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->element = new ImageElement('foo');
    }

    public function testImageElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof XhtmlElement);
    }

    public function testImageElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Element);
    }

    public function testImageElementUsesImageDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('Image');
        $this->assertTrue($decorator instanceof Decorator\Image);
    }

    /**
     * ZF-2717
     */
    public function testImageShouldSetHelperPropertyToFormImageByDefault()
    {
        $this->assertEquals('formImage', $this->element->helper);
    }

    public function testImageSourceValueNullByDefault()
    {
        $this->assertNull($this->element->getImage());
        $this->assertNull($this->element->src);
    }

    public function testCanSetImageSourceViaAccessors()
    {
        $this->element->setImage('foo.gif');
        $this->assertEquals('foo.gif', $this->element->getImage());
        $this->assertEquals('foo.gif', $this->element->src);
    }

    public function testImageSourceUsedWhenRenderingImage()
    {
        $this->testCanSetImageSourceViaAccessors();
        $html = $this->element->render(new View());
        $this->assertContains('src="foo.gif"', $html);
    }

    public function testHelperAttributeNotRenderedWhenRenderingImage()
    {
        $this->testCanSetImageSourceViaAccessors();
        $html = $this->element->render(new View());
        $this->assertNotContains('helper="', $html);
    }

    public function testValueEmptyWhenRenderingImageByDefault()
    {
        $this->testCanSetImageSourceViaAccessors();
        $html = $this->element->render(new View());
        if (!strstr($html, 'value="')) {
            return;
        }
        $this->assertContains('value=""', $html);
    }

    public function testLabelUsedAsAltAttribute()
    {
        $this->element->setLabel('Foo Bar');
        $html = $this->element->render(new View());
        $this->assertRegexp('#<input[^>]*alt="Foo Bar"#', $html);
    }

    public function testImageValueRenderedAsElementValue()
    {
        $this->element->setImageValue('foo')
             ->setImage('foo.gif');
        $html = $this->element->render(new View());
        $this->assertRegexp('#<input[^>]*value="foo"#', $html, $html);
    }

    public function testIsCheckedReturnsSetValueMatchesImageValue()
    {
        $this->assertFalse($this->element->isChecked());
        $this->element->setImageValue('foo');
        $this->assertFalse($this->element->isChecked());
        $this->element->setValue('foo');
        $this->assertTrue($this->element->isChecked());
        $this->element->setValue('bar');
        $this->assertFalse($this->element->isChecked());
    }

    /*
     * Tests if title attribute (tooltip) is translated if the default decorators are loaded.
     * These decorators should load the Tooltip decorator as the first decorator.
     * @group ZF-6151
     */
    public function testTitleAttributeGetsTranslated()
    {
        $this->element->setAttrib('title', 'bar');
        $translator = new ArrayTranslator(array("bar" => "baz"), 'de');
        $this->element->setTranslator($translator);
        $html = $this->element->render(new View());
        $this->assertContains('title', $html);
        $this->assertContains('baz', $html);
        $this->assertNotContains('bar', $html);
    }

    public function testTitleAttributeDoesNotGetTranslatedIfTranslatorIsDisabled()
    {
        $this->element->setAttrib('title', 'bar');
        $translator = new ArrayTranslator(array("bar" => "baz"), 'de');
        $this->element->setTranslator($translator);
        // now disable translator and see if that works
        $this->element->setDisableTranslator(true);
        $html = $this->element->render(new View());
        $this->assertContains('title', $html);
        $this->assertContains('bar', $html);
        $this->assertNotContains('baz', $html);
    }

    /**
     * Prove the fluent interface on Zend_Form_Element_Image::loadDefaultDecorators
     *
     * @group ZF-9913
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->element, $this->element->loadDefaultDecorators());
    }
}
