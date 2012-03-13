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

use Zend\Form\Decorator\Fieldset as FieldsetDecorator,
    Zend\Form\Element,
    Zend\Form\Form,
    Zend\Form\SubForm,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Decorator_Fieldset
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class FieldsetTest extends \PHPUnit_Framework_TestCase
{
 
    /**
     * @var FieldsetDecorator
     */
    protected $decorator;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new FieldsetDecorator();
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testPlacementInitiallyNull()
    {
        $this->assertNull($this->decorator->getPlacement());
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

    public function testLegendInitiallyNull()
    {
        $this->assertNull($this->decorator->getLegend());
    }

    public function testUsesLegendOptionWhenSetAndNoLegendInElement()
    {
        $this->testLegendInitiallyNull();
        $element = new Element('foo');
        $this->decorator->setElement($element)
                        ->setOption('legend', 'this is a legend');
        $this->assertEquals('this is a legend', $this->decorator->getLegend());
    }

    public function testUsesElementLegendWhenPresent()
    {
        $this->testLegendInitiallyNull();
        $element = new Form();
        $element->setLegend('this is a legend');
        $this->decorator->setElement($element);
        $this->assertEquals('this is a legend', $this->decorator->getLegend());
    }

    public function testCanSetLegend()
    {
        $this->testLegendInitiallyNull();
        $this->decorator->setLegend('this is a legend');
        $this->assertEquals('this is a legend', $this->decorator->getLegend());
    }

    /**
     * @group ZF-7054
     */
    public function testCustomIdSupersedesElementId()
    {
        $form = new SubForm();
        $form->setName('bar')
             ->setView($this->getView());
        $html = $this->decorator->setElement($form)
                                ->setOption('id', 'foo-id')
                                ->render('content');
        $this->assertContains('foo-id', $html);
    }

    /**
     * @group ZF-2981
     */
    public function testActionAndMethodAttributesShouldNotBePresentInFieldsetTag()
    {
        $form = new Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertContains('<fieldset', $test, $test);
        $this->assertNotContains('action="', $test);
        $this->assertNotContains('method="', $test);
    }

    /**
     * @group ZF-3731
     */
    public function testIdShouldBePrefixedWithFieldset()
    {
        $form = new Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setName('foobar')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertContains('id="fieldset-foobar"', $test);
    }

    /**
     * @group ZF-3731
     */
    public function testElementWithNoIdShouldNotCreateFieldsetId()
    {
        $form = new Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertNotContains('id="', $test);
    }

    /**
     * @group ZF-3728
     */
    public function testEnctypeAttributeShouldNotBePresentInFieldsetTag()
    {
        $form = new Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setAttrib('enctype', 'dojo/method')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertContains('<fieldset', $test, $test);
        $this->assertNotContains('enctype="', $test);
    }

    /**
     * @group ZF-3499
     */
    public function testHelperAttributeShouldNotBePresentInFieldsetTag()
    {
        $form = new Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setAttrib('helper', 'form')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertContains('<fieldset', $test, $test);
        $this->assertNotContains('helper="', $test);
    }
    
    /**
     * @group ZF2-104
     */
    public function testFieldSetOptionsShouldOverrideElementAttribs()
    {
        $form = new Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setView($this->getView());
        
        $this->decorator->setElement($form);
        $form->setAttrib('class', 'someclass');
        
        $this->assertEquals('someclass', $this->decorator->getOption('class'));
        
        $this->decorator->setOption('class', 'otherclass');
        
        $this->assertEquals('otherclass', $this->decorator->getOption('class'));
    }
}
