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

use Zend\Form\Decorator\Callback as CallbackDecorator,
    Zend\Form\Element;

/**
 * Test class for Zend_Form_Decorator_Callback
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new CallbackDecorator();
    }

    public function testCanSetCallback()
    {
        $callback = 'ZendTest\Form\Decorator\TestCallback';
        $this->decorator->setCallback($callback);
        $this->assertSame($callback, $this->decorator->getCallback());

        $callback = array('ZendTest\Form\TestAsset\TestCallbackClass', 'direct');
        $this->decorator->setCallback($callback);
        $this->assertSame($callback, $this->decorator->getCallback());
    }

    public function testCanSetCallbackViaOptions()
    {
        $callback = 'ZendTest\Form\Decorator\TestCallback';
        $this->decorator->setOptions(array('callback' => $callback));
        $this->assertSame($callback, $this->decorator->getCallback());
    }

    public function testInvalidCallbackRaisesException()
    {
        try {
            $this->decorator->setCallback(true);
            $this->fail('Only string or array callbacks should be allowed');
        } catch (\Zend\Form\Decorator\Exception\InvalidArgumentException $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $o = new \stdClass;
            $this->decorator->setCallback($o);
            $this->fail('Only string or array callbacks should be allowed');
        } catch (\Zend\Form\Decorator\Exception\InvalidArgumentException $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->decorator->setCallback(array('foo', 'bar', 'baz'));
            $this->fail('Only arrays of two elements should be allowed as callbacks');
        } catch (\Zend\Form\Decorator\Exception\InvalidArgumentexception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->decorator->setCallback(array('foo'));
            $this->fail('Only arrays of two elements should be allowed as callbacks');
        } catch (\Zend\Form\Decorator\Exception\InvalidArgumentException $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
    }

    public function testRenderCallsFunctionCallback()
    {
        $callback = 'ZendTest\Form\Decorator\TestCallback';
        $element  = new Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(array('callback' => $callback))
                        ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertContains('foo bar', $content);
        $this->assertContains($element->getName(), $content);
        $this->assertContains($element->getLabel(), $content);
    }

    public function testRenderCallsMethodCallback()
    {
        $callback = array('ZendTest\Form\TestAsset\TestCallbackClass', 'direct');
        $element  = new Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(array('callback' => $callback))
                        ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertContains('foo bar', $content);
        $this->assertContains($element->getName(), $content);
        $this->assertContains($element->getLabel(), $content);
        $this->assertContains('Item ', $content);
    }

    public function testRenderCanPrepend()
    {
        $callback = 'ZendTest\Form\Decorator\TestCallback';
        $element  = new Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(array('callback' => $callback, 'placement' => 'prepend'))
                        ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertContains('foo bar', $content);
        $this->assertContains($element->getName(), $content);
        $this->assertContains($element->getLabel(), $content);
        $this->assertRegexp('/foo bar$/s', $content);
    }

    public function testRenderCanReplaceContent()
    {
        $callback = 'ZendTest\Form\Decorator\TestCallback';
        $element  = new Element('foobar');
        $element->setLabel('Label Me');

        $this->decorator->setOptions(array('callback' => $callback, 'placement' => false))
                        ->setElement($element);

        $content = $this->decorator->render('foo bar');
        $this->assertNotContains('foo bar', $content, $content);
        $this->assertContains($element->getName(), $content);
        $this->assertContains($element->getLabel(), $content);
    }
}

function TestCallback($content, $element, array $options)
{
    $name  = $element->getName();
    $label = '';
    if (method_exists($element, 'getLabel')) {
        $label = $element->getLabel();
    }
    $html =<<<EOH
$label: $name

EOH;
    return $html;
}
