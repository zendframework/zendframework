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
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Dojo\View\Helper;

use Zend\Dojo\View\Helper\CustomDijit as CustomDijitHelper,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_CustomDijit
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class CustomDijitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::_unsetInstance();
        DojoHelper::setUseDeclarative();

        $this->view   = $this->getView();
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function testHelperShouldRaiseExceptionIfNoDojoTypePassed()
    {
        $this->setExpectedException('Zend\Dojo\View\Exception\InvalidArgumentException', 'No dojoType specified; cannot create dijit');
        $this->view->plugin('customDijit')->__invoke('foo');
    }

    public function testHelperInDeclarativeModeShouldGenerateDivWithPassedDojoType()
    {
        $content = $this->view->plugin('customDijit')->__invoke('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $this->assertContains('dojoType="custom.Dijit"', $content);
    }

    public function testHelperInDeclarativeModeShouldRegisterDojoTypeAsModule()
    {
        $content = $this->view->plugin('customDijit')->__invoke('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $dojo    = $this->view->plugin('dojo');
        $modules = $dojo->getModules();
        $this->assertContains('custom.Dijit', $modules);
    }

    public function testHelperInProgrammaticModeShouldRegisterDojoTypeAsModule()
    {
        DojoHelper::setUseProgrammatic();
        $content = $this->view->plugin('customDijit')->__invoke('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $dojo    = $this->view->plugin('dojo');
        $modules = $dojo->getModules();
        $this->assertContains('custom.Dijit', $modules);
    }

    public function testHelperInProgrammaticModeShouldGenerateDivWithoutPassedDojoType()
    {
        DojoHelper::setUseProgrammatic();
        $content = $this->view->plugin('customDijit')->__invoke('foo', 'content', array('dojoType' => 'custom.Dijit'));
        $this->assertNotContains('dojoType="custom.Dijit"', $content);
    }

    public function testHelperShouldAllowCapturingContent()
    {
        $this->view->plugin('customDijit')->captureStart('foo', array('dojoType' => 'custom.Dijit'));
        echo "Captured content started\n";
        $content = $this->view->plugin('customDijit')->captureEnd('foo');
        $this->assertContains(">Captured content started\n<", $content);
    }

    public function testUsesDefaultDojoTypeWhenPresent()
    {
        $helper = new TestAsset\FooContentPane();
        $helper->setView($this->view);
        $content = $helper->__invoke('foo');
        $this->assertContains('dojoType="foo.ContentPane"', $content);
    }

    public function testCapturingUsesDefaultDojoTypeWhenPresent()
    {
        $helper = new TestAsset\FooContentPane();
        $helper->setView($this->view);
        $helper->__invoke()->captureStart('foo');
        echo "Captured content started\n";
        $content = $helper->__invoke()->captureEnd('foo');
        $this->assertContains(">Captured content started\n<", $content);
        $this->assertContains('dojoType="foo.ContentPane"', $content);
    }

    /**
     * @group ZF-7890
     */
    public function testHelperShouldAllowSpecifyingRootNode()
    {
        $content = $this->view->plugin('customDijit')->__invoke('foo', 'content', array(
            'dojoType' => 'custom.Dijit',
            'rootNode' => 'select',
        ));
        $this->assertContains('<select', $content);
    }
}
