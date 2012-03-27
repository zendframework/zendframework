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
 * Zend_View_Helper_FormPasswordTest
 *
 * Tests formPassword helper
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class FormPasswordTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        if (\Zend\Registry::isRegistered('Zend_View_Helper_Doctype')) {
            $registry = \Zend\Registry::getInstance();
            unset($registry['Zend_View_Helper_Doctype']);
        }
        $this->view = new \Zend\View\Renderer\PhpRenderer();
        $this->helper = new \Zend\View\Helper\FormPassword();
        $this->helper->setView($this->view);
    }

    /**
     * @group ZF-1666
     */
    public function testCanDisableElement()
    {
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'attribs' => array('disable' => true)
        ));

        $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $html);
    }

    /**
     * @group ZF-1666
     */
    public function testDisablingElementDoesNotRenderHiddenElements()
    {
        $html = $this->helper->__invoke(array(
            'name'    => 'foo',
            'value'   => 'bar',
            'attribs' => array('disable' => true)
        ));

        $this->assertNotRegexp('/<input[^>]*?(type="hidden")/', $html);
    }

    public function testShouldRenderAsHtmlByDefault()
    {
        $test = $this->helper->__invoke('foo', 'bar');
        $this->assertNotContains(' />', $test);
    }

    public function testShouldAllowRenderingAsXhtml()
    {
        $this->view->plugin('doctype')->__invoke('XHTML1_STRICT');
        $test = $this->helper->__invoke('foo', 'bar');
        $this->assertContains(' />', $test);
    }

    public function testShouldNotRenderValueByDefault()
    {
        $test = $this->helper->__invoke('foo', 'bar');
        $this->assertNotContains('bar', $test);
    }

    /**
     * @group ZF-2860
     */
    public function testShouldRenderValueWhenRenderPasswordFlagPresentAndTrue()
    {
        $test = $this->helper->__invoke('foo', 'bar', array('renderPassword' => true));
        $this->assertContains('value="bar"', $test);
    }

    /**
     * @group ZF-2860
     */
    public function testRenderPasswordAttribShouldNeverBeRendered()
    {
        $test = $this->helper->__invoke('foo', 'bar', array('renderPassword' => true));
        $this->assertNotContains('renderPassword', $test);
        $test = $this->helper->__invoke('foo', 'bar', array('renderPassword' => false));
        $this->assertNotContains('renderPassword', $test);
    }
}
