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

use Zend\Form\Decorator\HtmlTag as HtmlTagDecorator,
    Zend\Form\Element,
    Zend\View\View;

/**
 * Test class for Zend_Form_Decorator_HtmlTag
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class HtmlTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new HtmlTagDecorator();
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testNormalizeTagStripsNonAlphanumericCharactersAndLowersCase()
    {
        $tag = 'ab1-cd0EFG';
        $received = $this->decorator->normalizeTag($tag);
        $this->assertEquals('ab1cd0efg', $received);
    }

    public function testRendersOptionsAsHtmlAttribsByDefault()
    {
        $element = new Element('foo');
        $options = array('tag' => 'div', 'class' => 'foobar', 'id' => 'foo');
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertContains('<' . $value, $html);
                $this->assertContains('</' . $value . '>', $html);
            } else {
                $this->assertContains($key . '="' . $value . '"', $html);
            }
        }
    }

    public function testDoesNotRenderAttribsWhenNoAttribsOptionSet()
    {
        $element = new Element('foo');
        $options = array('tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'noAttribs' => true);
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertContains('<' . $value, $html);
                $this->assertContains('</' . $value . '>', $html);
            } else {
                $this->assertNotContains($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testCanRenderOnlyOpeningTag()
    {
        $element = new Element('foo');
        $options = array('tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'openOnly' => true);
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertContains('<' . $value, $html);
                $this->assertNotContains('</' . $value . '>', $html);
            } elseif ('openOnly' == $key) {
                $this->assertNotContains($key, $html);
            } else {
                $this->assertContains($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testCanRenderOnlyClosingTag()
    {
        $element = new Element('foo');
        $options = array('tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'closeOnly' => true);
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertNotContains('<' . $value, $html);
                $this->assertContains('</' . $value . '>', $html);
            } else {
                $this->assertNotContains($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testArrayAttributesAreRenderedAsSpaceSeparatedLists()
    {
        $element = new Element('foo');
        $options = array('tag' => 'div', 'class' => array('foobar', 'bazbat'), 'id' => 'foo');
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        $this->assertContains('class="foobar bazbat"', $html);
    }

    public function testAppendPlacementWithCloseOnlyRendersClosingTagFollowingContent()
    {
        $options = array(
            'closeOnly' => true,
            'tag'       => 'div',
            'placement' => 'append'
        );
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertRegexp('#(content).*?(</div>)#', $html, $html);
    }

    public function testAppendPlacementWithOpenOnlyRendersOpeningTagFollowingContent()
    {
        $options = array(
            'openOnly'  => true,
            'tag'       => 'div',
            'placement' => 'append'
        );
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertRegexp('#(content).*?(<div>)#', $html, $html);
    }

    public function testPrependPlacementWithCloseOnlyRendersClosingTagBeforeContent()
    {
        $options = array(
            'closeOnly' => true,
            'tag'       => 'div',
            'placement' => 'prepend'
        );
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertRegexp('#(</div>).*?(content)#', $html, $html);
    }

    public function testPrependPlacementWithOpenOnlyRendersOpeningTagBeforeContent()
    {
        $options = array(
            'openOnly'  => true,
            'tag'       => 'div',
            'placement' => 'prepend'
        );
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertRegexp('#(<div>).*?(content)#', $html, $html);
    }

    public function testTagIsInitiallyDiv()
    {
        $this->assertEquals('div', $this->decorator->getTag());
    }

    public function testCanSetTag()
    {
        $this->testTagIsInitiallyDiv();
        $this->decorator->setTag('dl');
        $this->assertEquals('dl', $this->decorator->getTag());
    }

    public function testCanSetTagViaOption()
    {
        $this->decorator->setOption('tag', 'dl');
        $this->assertEquals('dl', $this->decorator->getTag());
    }
}
