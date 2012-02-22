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

use Zend\Dojo\View\Helper\AccordionContainer as AccordionContainerHelper,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_AccordionContainer.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class AccordionContainerTest extends \PHPUnit_Framework_TestCase
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
        $this->helper = new AccordionContainerHelper();
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
        $html = '';
        for ($i = 1; $i < 6; ++$i) {
            $id      = 'pane' . $i;
            $title   = 'Pane ' . $i;
            $content = 'This is the content of pane ' . $i;
            $html   .= $this->view->plugin('accordionPane')->__invoke($id, $content, array('title' => $title));
        }
        return $this->helper->__invoke('container', $html, array(), array('style' => 'height: 200px; width: 100px;'));
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getContainer();
        $this->assertRegexp('/<div[^>]*(dojoType="dijit.layout.AccordionContainer")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getContainer();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.layout.AccordionContainer")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('container'));
    }

    public function testShouldAllowCapturingNestedContent()
    {
        $this->helper->captureStart('foo', array(), array('style' => 'height: 200px; width: 100px;'));
        $this->view->plugin('accordionPane')->captureStart('bar', array('title' => 'Captured Pane'));
        echo "Captured content started\n";
        $this->view->plugin('accordionPane')->captureStart('baz', array('title' => 'Nested Pane'));
        echo 'Nested Content';
        echo $this->view->plugin('accordionPane')->captureEnd('baz');
        echo "Captured content ended\n";
        echo $this->view->plugin('accordionPane')->captureEnd('bar');
        $html = $this->helper->captureEnd('foo');
        $this->assertRegexp('/<div[^>]*(id="bar")/', $html);
        $this->assertRegexp('/<div[^>]*(id="baz")/', $html);
        $this->assertRegexp('/<div[^>]*(id="foo")/', $html);
        $this->assertEquals(2, substr_count($html, 'dijit.layout.AccordionPane'));
        $this->assertEquals(1, substr_count($html, 'dijit.layout.AccordionContainer'));
        $this->assertContains('started', $html);
        $this->assertContains('ended', $html);
        $this->assertContains('Nested Content', $html);
    }

    public function testCapturingShouldRaiseErrorWhenDuplicateIdDiscovered()
    {
        $this->helper->captureStart('foo', array(), array('style' => 'height: 200px; width: 100px;'));
        $this->view->plugin('accordionPane')->captureStart('bar', array('title' => 'Captured Pane'));
        
        $this->setExpectedException('Zend\Dojo\View\Exception\RuntimeException', 'Lock already exists for id ');
        $this->view->plugin('accordionPane')->captureStart('bar', array('title' => 'Captured Pane'));
    }

    public function testCapturingShouldRaiseErrorWhenNonexistentIdPassedToEnd()
    {
        $this->helper->captureStart('foo', array(), array('style' => 'height: 200px; width: 100px;'));
        
        $this->setExpectedException('Zend\Dojo\View\Exception\RuntimeException', 'No capture lock exists for id ');
        $html = $this->helper->captureEnd('bar');
    }
}
