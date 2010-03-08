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

// Call Zend_Dojo_View_Helper_EditorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_EditorTest::main");
}


/** Zend_Dojo_View_Helper_Editor */

/** Zend_View */

/** Zend_Registry */

/** Zend_Dojo_View_Helper_Dojo */

/**
 * Test class for Zend_Dojo_View_Helper_Editor.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_EditorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_View_Helper_EditorTest");
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
        $this->helper = new Zend_Dojo_View_Helper_Editor();
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
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function testHelperShouldRenderAlteredId()
    {
        $html = $this->helper->editor('foo');
        $this->assertContains('id="foo-Editor"', $html, $html);
    }

    public function testHelperShouldRenderHiddenElementWithGivenIdentifier()
    {
        $html = $this->helper->editor('foo');
        if (!preg_match('#(<input[^>]*(?:type="hidden")[^>]*>)#', $html, $matches)) {
            $this->fail('No hidden element generated');
        }
        $this->assertContains('id="foo"', $matches[1]);
    }

    public function testHelperShouldRenderDojoTypeWhenUsedDeclaratively()
    {
        $html = $this->helper->editor('foo');
        $this->assertContains('dojoType="dijit.Editor"', $html);
    }

    public function testHelperShouldRegisterDijitModule()
    {
        $html = $this->helper->editor('foo');
        $modules = $this->view->dojo()->getModules();
        $this->assertContains('dijit.Editor', $modules);
    }

    public function testHelperShouldNormalizeArrayId()
    {
        $html = $this->helper->editor('foo[]');
        $this->assertContains('id="foo-Editor"', $html, $html);

        $html = $this->helper->editor('foo[bar]');
        $this->assertContains('id="foo-bar-Editor"', $html, $html);
    }

    public function testHelperShouldJsonifyPlugins()
    {
        $plugins = array('copy', 'cut', 'paste');
        $html = $this->helper->editor('foo', '', array('plugins' => $plugins));
        $pluginsString = Zend_Json::encode($plugins);
        $pluginsString = str_replace('"', "'", $pluginsString);
        $this->assertContains('plugins="' . $pluginsString . '"', $html);
    }

    public function testHelperShouldCreateJavascriptToConnectEditorToHiddenValue()
    {
        $this->helper->editor('foo');
        $onLoadActions = $this->view->dojo()->getOnLoadActions();
        $found = false;
        foreach ($onLoadActions as $action) {
            if (strstr($action, "dojo.byId('foo').value = dijit.byId('foo-Editor').getValue(false);")) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, var_export($onLoadActions, 1));
    }

    public function testHelperShouldCreateJavascriptToFindParentForm()
    {
        $this->helper->editor('foo');
        $javascript = $this->view->dojo()->getJavascript();
        $found = false;
        foreach ($javascript as $action) {
            if (strstr($action, "zend.findParentForm = function")) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, var_export($javascript, 1));
    }

    public function testHelperShouldNotRegisterDojoStylesheet()
    {
        $this->helper->editor('foo');
        $this->assertFalse($this->view->dojo()->registerDojoStylesheet());
    }

    /**
     * @group ZF-4461
     */
    public function testHelperShouldRegisterPluginModulesWithDojo()
    {
        $plugins = array(
            'createLink' => 'LinkDialog',
            'fontName' => 'FontChoice',
        );
        $html = $this->helper->editor('foo', '', array('plugins' => array_keys($plugins)));

        $dojo = $this->view->dojo()->__toString();
        foreach (array_values($plugins) as $plugin) {
            $this->assertContains('dojo.require("dijit._editor.plugins.' . $plugin . '")', $dojo, $dojo);
        }
    }

    /**
     * @group ZF-6753
     * @group ZF-8127
     */
    public function testHelperShouldUseDivByDefault()
    {
        $html = $this->helper->editor('foo');
        $this->assertRegexp('#</?div[^>]*>#', $html, $html);
    }

    /**
     * @group ZF-6753
     * @group ZF-8127
     */
    public function testHelperShouldOnlyUseTextareaInNoscriptTag()
    {
        $html = $this->helper->editor('foo');
        $this->assertRegexp('#<noscript><textarea[^>]*>#', $html, $html);
    }
}

// Call Zend_Dojo_View_Helper_EditorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_EditorTest::main") {
    Zend_Dojo_View_Helper_EditorTest::main();
}
