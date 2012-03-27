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

namespace ZendTest\Dojo\Form\Element;

use Zend\Dojo\Form\Element\Editor as EditorElement,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_Editor.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class EditorTest extends \PHPUnit_Framework_TestCase
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

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        $element = new EditorElement(
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

    public function testCanAddMultipleSeparatorsToEditor()
    {
        $this->element->setPlugins(array('undo', '|', 'bold', '|', 'italic'));
        
        $plugins = $this->element->getPlugins();
        $this->assertEquals(5, count($plugins));
    }
    
    public function testMinHeightCanBeSetToPixels()
    {
        $this->element->setMinHeight('250px');
        $this->assertEquals($this->element->getDijitParam('minHeight'), $this->element->getMinHeight());
        $this->assertEquals('250px', $this->element->getMinHeight());
    }
    
    public function testMinHeightCanBeSetToPercentage()
    {
        $this->element->setMinHeight('50%');
        $this->assertEquals($this->element->getDijitParam('minHeight'), $this->element->getMinHeight());
        $this->assertEquals('50%', $this->element->getMinHeight());
    }
    
    public function testMinHeightDefaultMeasurementIsEm()
    {
        $this->element->setMinHeight('10');
        $this->assertEquals($this->element->getDijitParam('minHeight'), $this->element->getMinHeight());
        $this->assertEquals('10em', $this->element->getMinHeight());
    }
    
    public function testShouldNotHaveExtraPluginsByDefault()
    {
        $extraPlugins = $this->element->getExtraPlugins();
        $this->assertTrue(empty($extraPlugins));
    }

    public function testExtraPluginAccessorsShouldProxyToDijitParams()
    {
        $this->element->setExtraPlugins(array('undo', 'bold', 'italic'));
        $this->assertTrue($this->element->hasDijitParam('extraPlugins'));
        $this->assertTrue($this->element->hasExtraPlugin('bold'));
        $this->assertEquals($this->element->getDijitParam('extraPlugins'), $this->element->getExtraPlugins());

        $this->element->removeExtraPlugin('bold');
        $this->assertFalse($this->element->hasExtraPlugin('bold'), var_export($this->element->getExtraPlugins(), 1));
        $extraPlugins = $this->element->getDijitParam('extraPlugins');
        $this->assertNotContains('bold', $extraPlugins, var_export($extraPlugins, 1));
    }
}
