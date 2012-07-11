<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\View\Helper;

use Zend\Dojo\View\Helper\ContentPane as ContentPaneHelper,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_ContentPane.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class ContentPaneTest extends \PHPUnit_Framework_TestCase
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
        $this->helper = new ContentPaneHelper();
        $this->helper->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getContainer()
    {
        return $this->view->plugin('contentPane')->__invoke('pane1', 'This is the pane content', array('title' => 'Pane 1'));
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getContainer();
        $this->assertRegexp('/<div[^>]*(dojoType="dijit.layout.ContentPane")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getContainer();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.layout.ContentPane")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('pane1'));
    }

    /**
     * @group ZF-3877
     */
    public function testContentPaneMarkupShouldNotContainNameAttribute()
    {
        $html = $this->view->plugin('contentPane')->__invoke('pane1', 'This is the pane content', array('id' => 'pane', 'title' => 'Pane 1'));
        $this->assertNotContains('name="/', $html, $html);

        DojoHelper::setUseProgrammatic();
        $html = $this->view->plugin('contentPane')->__invoke('pane1', 'This is the pane content', array('id' => 'pane', 'title' => 'Pane 1'));
        $this->assertNotContains('name="/', $html, $html);
    }

    /**
     * @group ZF-4522
     */
    public function testCaptureStartShouldReturnVoid()
    {
        $test = $this->view->plugin('contentPane')->captureStart('pane1');
        $this->view->plugin('contentPane')->captureEnd('pane1');
        $this->assertNull($test);
    }
}
