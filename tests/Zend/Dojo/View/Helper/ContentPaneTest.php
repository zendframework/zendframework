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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_View_Helper_ContentPaneTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_ContentPaneTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_View_Helper_ContentPane */
require_once 'Zend/Dojo/View/Helper/ContentPane.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_View_Helper_ContentPane.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_ContentPaneTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_View_Helper_ContentPaneTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Zend_Registry::_unsetInstance();
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative();

        $this->view   = $this->getView();
        $this->helper = new Zend_Dojo_View_Helper_ContentPane();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getContainer()
    {
        return $this->view->contentPane('pane1', 'This is the pane content', array('title' => 'Pane 1'));
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getContainer();
        $this->assertRegexp('/<div[^>]*(dojoType="dijit.layout.ContentPane")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $html = $this->getContainer();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.layout.ContentPane")/', $html);
        $this->assertNotNull($this->view->dojo()->getDijit('pane1'));
    }

    /**
     * @group ZF-3877
     */
    public function testContentPaneMarkupShouldNotContainNameAttribute()
    {
        $html = $this->view->contentPane('pane1', 'This is the pane content', array('id' => 'pane', 'title' => 'Pane 1'));
        $this->assertNotContains('name="/', $html, $html);

        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $html = $this->view->contentPane('pane1', 'This is the pane content', array('id' => 'pane', 'title' => 'Pane 1'));
        $this->assertNotContains('name="/', $html, $html);
    }

    /**
     * @group ZF-4522
     */
    public function testCaptureStartShouldReturnVoid()
    {
        $test = $this->view->contentPane()->captureStart('pane1');
        $this->view->contentPane()->captureEnd('pane1');
        $this->assertNull($test);
    }
}

// Call Zend_Dojo_View_Helper_ContentPaneTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_ContentPaneTest::main") {
    Zend_Dojo_View_Helper_ContentPaneTest::main();
}
