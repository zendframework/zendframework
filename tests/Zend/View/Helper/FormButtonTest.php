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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

/**
 * Test class for Zend_View_Helper_FormButton.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class FormButtonTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->view = new \Zend\View\Renderer\PhpRenderer();
        $this->helper = new \Zend\View\Helper\FormButton();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testFormButtonRendersButtonXhtml()
    {
        $button = $this->helper->__invoke('foo', 'bar');
        $this->assertRegexp('/<button[^>]*?value="bar"/', $button);
        $this->assertRegexp('/<button[^>]*?name="foo"/', $button);
        $this->assertRegexp('/<button[^>]*?id="foo"/', $button);
        $this->assertContains('</button>', $button);
    }

    public function testCanPassContentViaContentAttribKey()
    {
        $button = $this->helper->__invoke('foo', 'bar', array('content' => 'Display this'));
        $this->assertContains('>Display this<', $button);
        $this->assertContains('<button', $button);
        $this->assertContains('</button>', $button);
    }

    public function testCanDisableContentEscaping()
    {
        $button = $this->helper->__invoke('foo', 'bar', array('content' => '<b>Display this</b>', 'escape' => false));
        $this->assertContains('><b>Display this</b><', $button);

        $button = $this->helper->__invoke(array('name' => 'foo', 'value' => 'bar', 'attribs' => array('content' => '<b>Display this</b>', 'escape' => false)));
        $this->assertContains('><b>Display this</b><', $button);

        $button = $this->helper->__invoke(array('name' => 'foo', 'value' => 'bar', 'escape' => false, 'attribs' => array('content' => '<b>Display this</b>')));
        $this->assertContains('><b>Display this</b><', $button);
        $this->assertContains('<button', $button);
        $this->assertContains('</button>', $button);
    }

    public function testValueUsedForContentWhenNoContentProvided()
    {
        $button = $this->helper->__invoke(array('name' => 'foo', 'value' => 'bar'));
        $this->assertRegexp('#<button[^>]*?value="bar"[^>]*>bar</button>#', $button);
    }

    public function testButtonTypeIsButtonByDefault()
    {
        $button = $this->helper->__invoke(array('name' => 'foo', 'value' => 'bar'));
        $this->assertContains('type="button"', $button);
    }

    public function testButtonTypeMayOnlyBeValidXhtmlButtonType()
    {
        $button = $this->helper->__invoke(array('name' => 'foo', 'value' => 'bar', 'attribs' => array('type' => 'submit')));
        $this->assertContains('type="submit"', $button);
        $button = $this->helper->__invoke(array('name' => 'foo', 'value' => 'bar', 'attribs' => array('type' => 'reset')));
        $this->assertContains('type="reset"', $button);
        $button = $this->helper->__invoke(array('name' => 'foo', 'value' => 'bar', 'attribs' => array('type' => 'button')));
        $this->assertContains('type="button"', $button);
        $button = $this->helper->__invoke(array('name' => 'foo', 'value' => 'bar', 'attribs' => array('type' => 'bogus')));
        $this->assertContains('type="button"', $button);
    }
}

