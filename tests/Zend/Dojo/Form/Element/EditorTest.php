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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_Form_Element_EditorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Element_EditorTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_Form_Element_Editor */
require_once 'Zend/Dojo/Form/Element/Editor.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_Form_Element_Dijit.
 *
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Dojo_Form_Element_EditorTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Element_EditorTest");
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

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
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

    public function getElement()
    {
        $element = new Zend_Dojo_Form_Element_Editor(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'Editor',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testShouldRenderEditorDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.Editor"', $html, $html);
    }

    public function testShouldNotHaveCaptureEventsByDefault()
    {
        $events = $this->element->getCaptureEvents();
        $this->assertTrue(empty($events));
    }

    public function testCaptureEventAccessorsShouldProxyToDijitParams()
    {
        $this->element->setCaptureEvents(array('foo.bar', 'bar.baz', 'baz.bat'));
        $this->assertTrue($this->element->hasDijitParam('captureEvents'));
        $this->assertTrue($this->element->hasCaptureEvent('bar.baz'));
        $this->assertEquals($this->element->getDijitParam('captureEvents'), $this->element->getCaptureEvents());

        $this->element->removeCaptureEvent('bar.baz');
        $this->assertFalse($this->element->hasCaptureEvent('bar.baz'), var_export($this->element->getCaptureEvents(), 1));
        $events = $this->element->getDijitParam('captureEvents');
        $this->assertNotContains('bar.baz', $events, var_export($events, 1));
    }

    public function testShouldNotHaveEventsByDefault()
    {
        $events = $this->element->getEvents();
        $this->assertTrue(empty($events));
    }

    public function testEventAccessorsShouldProxyToDijitParams()
    {
        $this->element->setEvents(array('onClick', 'onKeyUp', 'onKeyDown'));
        $this->assertTrue($this->element->hasDijitParam('events'));
        $this->assertTrue($this->element->hasEvent('onKeyUp'));
        $this->assertEquals($this->element->getDijitParam('events'), $this->element->getEvents());

        $this->element->removeEvent('onKeyUp');
        $this->assertFalse($this->element->hasEvent('onKeyUp'), var_export($this->element->getEvents(), 1));
        $events = $this->element->getDijitParam('events');
        $this->assertNotContains('onKeyUp', $events, var_export($events, 1));
    }

    public function testShouldNotHavePluginsByDefault()
    {
        $plugins = $this->element->getPlugins();
        $this->assertTrue(empty($plugins));
    }

    public function testPluginAccessorsShouldProxyToDijitParams()
    {
        $this->element->setPlugins(array('undo', 'bold', 'italic'));
        $this->assertTrue($this->element->hasDijitParam('plugins'));
        $this->assertTrue($this->element->hasPlugin('bold'));
        $this->assertEquals($this->element->getDijitParam('plugins'), $this->element->getPlugins());

        $this->element->removePlugin('bold');
        $this->assertFalse($this->element->hasPlugin('bold'), var_export($this->element->getPlugins(), 1));
        $plugins = $this->element->getDijitParam('plugins');
        $this->assertNotContains('bold', $plugins, var_export($plugins, 1));
    }

    public function testEditActionIntervalShouldDefaultToThree()
    {
        $this->assertEquals(3, $this->element->getEditActionInterval());
    }

    public function testEditActionIntervalAccessorsShouldProxyToDijitParams()
    {
        $this->element->setEditActionInterval(60);
        $this->assertEquals($this->element->getDijitParam('editActionInterval'), $this->element->getEditActionInterval());
        $this->assertEquals(60, $this->element->getEditActionInterval());
    }

    public function testFocusOnLoadShouldBeFalseByDefault()
    {
        $this->assertFalse($this->element->getFocusOnLoad());
    }

    public function testFocusOnLoadAccessorsShouldProxyToDijitParams()
    {
        $this->element->setFocusOnLoad(true);
        $this->assertEquals($this->element->getDijitParam('focusOnLoad'), $this->element->getFocusOnLoad());
        $this->assertTrue($this->element->getFocusOnLoad());
    }

    public function testHeightShouldHaveDefaultValue()
    {
        $this->assertEquals('300px', $this->element->getHeight());
    }

    public function testHeightAccessorsShouldProxyToDijitParams()
    {
        $this->element->setHeight('25em');
        $this->assertEquals($this->element->getDijitParam('height'), $this->element->getHeight());
        $this->assertEquals('25em', $this->element->getHeight());
    }

    public function testInheritWidthShouldBeFalseByDefault()
    {
        $this->assertFalse($this->element->getInheritWidth());
    }

    public function testInheritWidthAccessorsShouldProxyToDijitParams()
    {
        $this->element->setInheritWidth(true);
        $this->assertEquals($this->element->getDijitParam('inheritWidth'), $this->element->getInheritWidth());
        $this->assertTrue($this->element->getInheritWidth());
    }

    public function testMinHeightShouldHaveDefaultValue()
    {
        $this->assertEquals('1em', $this->element->getMinHeight());
    }

    public function testMinHeightAccessorsShouldProxyToDijitParams()
    {
        $this->element->setMinHeight('25em');
        $this->assertEquals($this->element->getDijitParam('minHeight'), $this->element->getMinHeight());
        $this->assertEquals('25em', $this->element->getMinHeight());
    }

    public function testShouldNotHaveStyleSheetsByDefault()
    {
        $styleSheets = $this->element->getStyleSheets();
        $this->assertTrue(empty($styleSheets));
    }

    public function testStyleSheetAccessorsShouldProxyToDijitParams()
    {
        $this->element->setStyleSheets(array('/js/dojo/resources/dojo.css', '/js/custom/styles.css', '/js/dijit/themes/tundra/tundra.css'));
        $this->assertTrue($this->element->hasDijitParam('styleSheets'));
        $this->assertTrue($this->element->hasStyleSheet('/js/custom/styles.css'));
        $this->assertEquals($this->element->getDijitParam('styleSheets'), $this->element->getStyleSheets());

        $this->element->removeStyleSheet('/js/custom/styles.css');
        $this->assertFalse($this->element->hasStyleSheet('/js/custom/styles.css'), var_export($this->element->getStyleSheets(), 1));
        $styleSheets = $this->element->getDijitParam('styleSheets');
        $this->assertNotContains('/js/custom/styles.css', $styleSheets, var_export($styleSheets, 1));
    }

    public function testUpdateIntervalShouldHaveDefaultValue()
    {
        $this->assertEquals(200, $this->element->getUpdateInterval());
    }

    public function testUpdateIntervalAccessorsShouldProxyToDijitParams()
    {
        $this->element->setUpdateInterval(300);
        $this->assertEquals($this->element->getDijitParam('updateInterval'), $this->element->getUpdateInterval());
        $this->assertEquals(300, $this->element->getUpdateInterval());
    }
}

// Call Zend_Dojo_Form_Element_EditorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Element_EditorTest::main") {
    Zend_Dojo_Form_Element_EditorTest::main();
}
