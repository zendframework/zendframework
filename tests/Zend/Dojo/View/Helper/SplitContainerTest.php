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

use Zend\Dojo\View\Helper\SplitContainer as SplitContainerHelper,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_SplitContainer.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class SplitContainerTest extends \PHPUnit_Framework_TestCase
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
        $this->helper = new SplitContainerHelper();
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
        foreach (array('top', 'bottom', 'center', 'left', 'right') as $pane) {
            $id      = $pane . 'Pane';
            $content = 'This is the content of pane ' . $pane;
            $html   .= $this->view->plugin('contentPane')->__invoke($id, $content, array('region' => $pane));
        }
        return $this->helper->__invoke('container', $html, array('design' => 'headline'));
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getContainer();
        $this->assertRegexp('/<div[^>]*(dojoType="dijit.layout.SplitContainer")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getContainer();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.layout.SplitContainer")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('container'));
    }
}
