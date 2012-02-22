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

use Zend\Form\Decorator\Errors as ErrorsDecorator,
    Zend\Form\Element,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Decorator_Errors
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class ErrorsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new ErrorsDecorator();
    }

    public function testRenderReturnsInitialContentIfNoViewPresentInElement()
    {
        $element = new Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function setupElement()
    {
        $element = new Element('foo');
        $element->addValidator('Alnum')
                ->addValidator('Alpha')
                ->setView($this->getView());
        $element->isValid('abc-123');
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function testRenderRendersAllErrorMessages()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content, $test);
        foreach ($this->element->getMessages() as $message) {
            $this->assertContains($message, $test);
        }
    }

    public function testRenderAppendsMessagesToContentByDefault()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertRegexp('#' . $content . '.*?<ul#s', $test, $test);
    }

    public function testRenderPrependsMessagesToContentWhenRequested()
    {
        $this->decorator->setOptions(array('placement' => 'PREPEND'));
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertRegexp('#</ul>.*?' . $content . '#s', $test);
    }

    public function testRenderSeparatesContentAndErrorsWithPhpEolByDefault()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content . PHP_EOL . '<ul', $test);
    }

    public function testRenderSeparatesContentAndErrorsWithCustomSeparatorWhenRequested()
    {
        $this->decorator->setOptions(array('separator' => '<br />'));
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content . $this->decorator->getSeparator() . '<ul', $test, $test);
    }
}
