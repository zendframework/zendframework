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

use Zend\Form\Decorator\Image as ImageDecorator,
    Zend\Form\Decorator\AbstractDecorator,
    Zend\Form\Element,
    Zend\Form\Element\Image as ImageElement,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Decorator_Image
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
        $this->decorator = new ImageDecorator();
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testPlacementInitiallyAppends()
    {
        $this->assertEquals(AbstractDecorator::APPEND, $this->decorator->getPlacement());
    }

    /**
     * Test is obsolete as view is now lazy-loaded
     * @group disable
     */
    public function testRenderReturnsOriginalContentWhenNoViewPresentInElement()
    {
        $element = new Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testTagInitiallyNull()
    {
        $this->assertNull($this->decorator->getTag());
    }

    public function testCanSetTag()
    {
        $this->testTagInitiallyNull();
        $this->decorator->setTag('div');
        $this->assertEquals('div', $this->decorator->getTag());
    }

    public function testCanSetTagViaOptions()
    {
        $this->testTagInitiallyNull();
        $this->decorator->setOption('tag', 'div');
        $this->assertEquals('div', $this->decorator->getTag());
    }

    public function testRendersXhtmlImageTag()
    {
        $element = new ImageElement('foo');
        $element->setImage('foobar')
                ->setView($this->getView());
        $this->decorator->setElement($element);

        $image = $this->decorator->render('');
        $this->assertContains('<input', $image, $image);
        $this->assertContains('src="foobar"', $image);
        $this->assertContains('name="foo"', $image);
        $this->assertContains('type="image"', $image);
    }

    public function testCanRenderImageWithinAdditionalTag()
    {
        $element = new ImageElement('foo');
        $element->setValue('foobar')
                ->setView($this->getView());
        $this->decorator->setElement($element)
                        ->setOption('tag', 'div');

        $image = $this->decorator->render('');
        $this->assertRegexp('#<div>.*?<input[^>]*>.*?</div>#s', $image, $image);
    }

    public function testCanPrependImageToContent()
    {
        $element = new ImageElement('foo');
        $element->setValue('foobar')
                ->setView($this->getView());
        $this->decorator->setElement($element)
                        ->setOption('placement', 'prepend');

        $image = $this->decorator->render('content');
        $this->assertRegexp('#<input[^>]*>.*?(content)#s', $image, $image);
    }

    /**
     * @group ZF-2714
     */
    public function testImageElementAttributesPassedWithDecoratorOptionsToViewHelper()
    {
        $element = new ImageElement('foo');
        $element->setValue('foobar')
                ->setAttrib('onClick', 'foo()')
                ->setAttrib('id', 'foo-element')
                ->setView($this->getView());
        $this->decorator->setElement($element)
                        ->setOption('class', 'imageclass');

        $image = $this->decorator->render('');
        $this->assertContains('class="imageclass"', $image);
        $this->assertContains('onClick="foo()"', $image);
        $this->assertContains('id="foo-element"', $image);
    }
}
