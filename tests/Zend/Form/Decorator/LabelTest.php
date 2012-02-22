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

use Zend\Form\Decorator\Label as LabelDecorator,
    Zend\Form\Decorator\AbstractDecorator,
    Zend\Form\Element,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Decorator_Label
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class LabelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new LabelDecorator();
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testUsesPrependPlacementByDefault()
    {
        $this->assertEquals(AbstractDecorator::PREPEND, $this->decorator->getPlacement());
    }

    public function testRenderReturnsOriginalContentWhenNoViewPresentInElement()
    {
        $element = new Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testRenderReturnsOriginalContentWhenNoLabelPresentInElement()
    {
        $element = new Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testRenderUsesElementIdIfSet()
    {
        $element = new Element('foo');
        $element->setAttrib('id', 'foobar')
                ->setView($this->getView())
                ->setLabel('My Label');
        $this->decorator->setElement($element);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains('for="foobar"', $test);
    }

    public function testRenderAddsOptionalClassForNonRequiredElements()
    {
        $element = new Element('foo');
        $element->setView($this->getView())
                ->setLabel('My Label');
        $this->decorator->setElement($element);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertRegexp('/<label[^>]*?class="[^"]*optional/', $test, $test);

        $element->class = "bar";
        $this->decorator->setOption('class', 'foo');
        $test = $this->decorator->render($content);
        $this->assertNotRegexp('/<label[^>]*?class="[^"]*bar/', $test, $test);
        $this->assertRegexp('/<label[^>]*?class="[^"]*foo/', $test, $test);
        $this->assertRegexp('/<label[^>]*?class="[^"]*optional/', $test, $test);
    }

    public function testRenderAddsRequiredClassForRequiredElements()
    {
        $element = new Element('foo');
        $element->setRequired(true)
                ->setView($this->getView())
                ->setLabel('My Label');
        $this->decorator->setElement($element);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertRegexp('/<label[^>]*?class="[^"]*required/', $test, $test);

        $element->class = "bar";
        $this->decorator->setOption('class', 'foo');
        $test = $this->decorator->render($content);
        $this->assertNotRegexp('/<label[^>]*?class="[^"]*bar/', $test, $test);
        $this->assertRegexp('/<label[^>]*?class="[^"]*foo/', $test, $test);
        $this->assertRegexp('/<label[^>]*?class="[^"]*required/', $test, $test);
    }

    public function testRenderAppendsRequiredClassToClassProvidedInRequiredElement()
    {
        $element = new Element('foo');
        $element->setRequired(true)
                ->setView($this->getView())
                ->setLabel('My Label')
                ->setAttrib('class', 'bazbat');
        $this->decorator->setElement($element);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertRegexp('/<label[^>]*?class="[^"]*required/', $test, $test);
        $this->assertNotRegexp('/<label[^>]*?class="[^"]*bazbat/', $test, $test);
    }

    public function testRenderUtilizesOptionalSuffixesAndPrefixesWhenRequested()
    {
        $element = new Element('foo');
        $element->setAttribs(array(
                    'optionalPrefix' => '-opt-prefix-',
                    'optionalSuffix' => '-opt-suffix-',
                    'requiredPrefix' => '-req-prefix-',
                    'requiredSuffix' => '-req-suffix-',
                  ))
                ->setView($this->getView())
                ->setLabel('My Label');
        $this->decorator->setElement($element);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertNotContains('-req-prefix-', $test, $test);
        $this->assertNotContains('-req-suffix-', $test, $test);
        $this->assertContains('-opt-prefix-', $test, $test);
        $this->assertContains('-opt-suffix-', $test, $test);
        $this->assertRegexp('/-opt-prefix-[^-]*?My Label[^-]*-opt-suffix-/s', $test, $test);
    }

    public function testRenderUtilizesRequiredSuffixesAndPrefixesWhenRequested()
    {
        $element = new Element('foo');
        $element->setAttribs(array(
                    'optionalPrefix' => '-opt-prefix-',
                    'optionalSuffix' => '-opt-suffix-',
                    'requiredPrefix' => '-req-prefix-',
                    'requiredSuffix' => '-req-suffix-',
                  ))
                ->setRequired(true)
                ->setView($this->getView())
                ->setLabel('My Label');
        $this->decorator->setElement($element);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertNotContains('-opt-prefix-', $test, $test);
        $this->assertNotContains('-opt-suffix-', $test, $test);
        $this->assertContains('-req-prefix-', $test, $test);
        $this->assertContains('-req-suffix-', $test, $test);
        $this->assertRegexp('/-req-prefix-[^-]*?My Label[^-]*-req-suffix-/s', $test, $test);
    }

    /**
     * @group ZF-3538
     */
    public function testRenderShouldNotUtilizeElementClass()
    {
        $element = new Element('foo');
        $element->setView($this->getView())
                ->setLabel('My Label')
                ->setAttrib('class', 'foobar');
        $this->decorator->setElement($element);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertNotRegexp('#<label[^>]*(class="[^"]*foobar)[^"]*"#', $test, $test);
    }

    public function testRenderRendersLabel()
    {
        $element = new Element('foo');
        $element->setView($this->getView())
                ->setLabel('My Label');
        $this->decorator->setElement($element);
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content, $test);
        $this->assertContains($element->getLabel(), $test);
        $this->assertContains('<label for=', $test);
        $this->assertContains('</label>', $test);
    }

    public function testRenderAppendsOnRequest()
    {
        $element = new Element('foo');
        $element->setView($this->getView())
                ->setLabel('My Label');
        $this->decorator->setElement($element)
                        ->setOptions(array('placement' => 'APPEND'));
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertRegexp('#' . $content . '.*?<label#s', $test);
    }

    public function testCanChooseNotToEscapeLabel()
    {
        $element = new Element('foo');
        $element->setView($this->getView())
                ->setLabel('<b>My Label</b>');
        $this->decorator->setElement($element)
                        ->setOptions(array('escape' => false));
        $test = $this->decorator->render('');
        $this->assertContains($element->getLabel(), $test);
    }

    public function testRetrievingLabelRetrievesLabelWithTranslationAndPrefixAndSuffix()
    {
        $translate = new Translator('ArrayAdapter', array('My Label' => 'Translation'), 'en');
        $translate->setLocale('en');

        $element = new Element('foo');
        $element->setView($this->getView())
                ->setLabel('My Label')
                ->setTranslator($translate);
        $this->decorator->setElement($element)
                        ->setOptions(array(
                            'optionalPrefix' => '> ',
                            'optionalSuffix' => ':',
                            'requiredPrefix' => '! ',
                            'requiredSuffix' => '*:',
                        ));
        $label = $this->decorator->getLabel();
        $this->assertEquals('> Translation:', $label);

        $element->setRequired(true);
        $label = $this->decorator->getLabel();
        $this->assertEquals('! Translation*:', $label);
    }

    public function testSettingTagToEmptyValueShouldDisableTag()
    {
        $element = new Element\Text('foo', array('label' => 'Foo'));
        $this->decorator->setElement($element)
                        ->setTag('');
        $content = $this->decorator->render('');
        $this->assertTrue(empty($content), $content);
    }

    /**
     * @group ZF-4841
     */
    public function testSettingTagToEmptyValueShouldSetTagToNull()
    {
        $element = new Element\Text('foo', array('label' => 'Foo'));
        $this->decorator->setElement($element)
                        ->setOptions(array('tag' => 'dt'));
        $this->decorator->setTag('');
        $tag = $this->decorator->getTag();
        $this->assertNull($tag);
    }
}
