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
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Layout;
use Zend\Controller,
    Zend\Layout,
    Zend\Config,
    Zend\View;

/**
 * Test class for Zend_Layout.
 *
 * @category   Zend
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Layout
 */
class LayoutTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        \Zend\Layout\Layout::resetMvcInstance();

        $front = Controller\Front::getInstance();
        $front->resetInstance();

        $broker = $front->getHelperBroker();
        if ($broker->hasPlugin('Layout')) {
            $broker->unregister('Layout');
        }
        if ($broker->hasPlugin('viewRenderer')) {
            $broker->unregister('viewRenderer');
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        Layout\Layout::resetMvcInstance();
    }

    public function testDefaultLayoutStatusAtInitialization()
    {
        $layout = new Layout\Layout();
        $this->assertEquals('layout', $layout->getLayout());
        $this->assertEquals('content', $layout->getContentKey());
        $this->assertTrue($layout->isEnabled());
        $this->assertTrue($layout->inflectorEnabled());
        $this->assertNull($layout->getLayoutPath());
        $this->assertFalse($layout->getMvcEnabled());
    }

    public function testDefaultLayoutStatusAtInitializationWhenInitMvcFlagPassed()
    {
        $layout = new Layout\Layout(null, true);
        $this->assertEquals('layout', $layout->getLayout());
        $this->assertEquals('content', $layout->getContentKey());
        $this->assertTrue($layout->isEnabled());
        $this->assertTrue($layout->inflectorEnabled());
        $this->assertNull($layout->getLayoutPath());
        $this->assertTrue($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testSetConfigModifiesAttributes()
    {
        $layout = new Layout\Layout();

        $config = new Config\Config(array(
            'layout'           => 'foo',
            'contentKey'       => 'foo',
            'layoutPath'       => __DIR__,
            'mvcEnabled'       => false,
        ));
        $layout->setConfig($config);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertEquals('foo', $layout->getContentKey());
        $this->assertEquals(__DIR__, $layout->getLayoutPath());
        $this->assertFalse($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testSetOptionsWithConfigObjectModifiesAttributes()
    {
        $layout = new Layout\Layout();

        $config = new Config\Config(array(
            'layout'           => 'foo',
            'contentKey'       => 'foo',
            'layoutPath'       => __DIR__,
            'mvcEnabled'       => false,
        ));
        $layout->setOptions($config);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertEquals('foo', $layout->getContentKey());
        $this->assertEquals(__DIR__, $layout->getLayoutPath());
        $this->assertFalse($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testLayoutAccessorsModifyAndRetrieveLayoutValue()
    {
        $layout = new Layout\Layout();
        $layout->setLayout('foo');
        $this->assertEquals('foo', $layout->getLayout());
    }

    /**
     * @return void
     */
    public function testSetLayoutEnablesLayouts()
    {
        $layout = new Layout\Layout();
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
        $layout->setLayout('foo');
        $this->assertTrue($layout->isEnabled());
    }

    /**
     * @return void
     */
    public function testDisableLayoutDisablesLayouts()
    {
        $layout = new Layout\Layout();
        $this->assertTrue($layout->isEnabled());
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
    }

    /**
     * @return void
     */
    public function testEnableLayoutEnablesLayouts()
    {
        $layout = new Layout\Layout();
        $this->assertTrue($layout->isEnabled());
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
        $layout->enableLayout();
        $this->assertTrue($layout->isEnabled());
    }

    /**
     * @return void
     */
    public function testLayoutPathAccessorsWork()
    {
        $layout = new Layout\Layout();
        $layout->setLayoutPath(__DIR__);
        $this->assertEquals(__DIR__, $layout->getLayoutPath());
    }

    /**
     * @return void
     */
    public function testContentKeyAccessorsWork()
    {
        $layout = new Layout\Layout();
        $layout->setContentKey('foo');
        $this->assertEquals('foo', $layout->getContentKey());
    }

    /**
     * @return void
     */
    public function testMvcEnabledFlagFalseAfterStandardInstantiation()
    {
        $layout = new Layout\Layout();
        $this->assertFalse($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testMvcEnabledFlagTrueWhenInstantiatedViaStartMvcMethod()
    {
        $layout = Layout\Layout::startMvc();
        $this->assertTrue($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testGetViewRetrievesViewWhenNoneSet()
    {
        $layout = new Layout\Layout();
        $view = $layout->getView();
        $this->assertTrue($view instanceof View\Renderer);
    }

    /**
     * @return void
     */
    public function testGetViewRetrievesViewFromViewRenderer()
    {
        $layout = new Layout\Layout();
        $view   = $layout->getView();
        $vr     = Controller\Front::getInstance()->getHelperBroker()->load('viewRenderer');
        $this->assertSame($vr->view, $view);
    }

    /**
     * @return void
     */
    public function testViewAccessorsAllowSettingView()
    {
        $layout = new Layout\Layout();
        $view   = new View\PhpRenderer();
        $layout->setView($view);
        $received = $layout->getView();
        $this->assertSame($view, $received);
    }

    /**
     * @return void
     */
    public function testInflectorAccessorsWork()
    {
        $layout = new Layout\Layout();
        $inflector = new \Zend\Filter\Inflector();
        $layout->setInflector($inflector);
        $this->assertSame($inflector, $layout->getInflector());
    }

    /**
     * @return void
     */
    public function testPluginClassAccessorsSetState()
    {
        $layout = new Layout\Layout();
        $layout->setPluginClass('Foo_Bar');
        $this->assertEquals('Foo_Bar', $layout->getPluginClass());
    }

    /**
     * @return void
     */
    public function testPluginClassPassedToStartMvcIsUsed()
    {
        $layout = Layout\Layout::startMvc(array('pluginClass' => 'ZendTest\Layout\TestAsset\MockControllerPlugin\Layout'));
        $this->assertTrue(Controller\Front::getInstance()->hasPlugin('ZendTest\Layout\TestAsset\MockControllerPlugin\Layout'));
    }

    /**
     * @return void
     */
    public function testHelperClassAccessorsSetState()
    {
        $layout = new Layout\Layout();
        $layout->setHelperClass('Foo_Bar');
        $this->assertEquals('Foo_Bar', $layout->getHelperClass());
    }

    /**
     * @return void
     */
    public function testHelperClassPassedToStartMvcIsUsed()
    {
        $layout = Layout\Layout::startMvc(array('helperClass' => 'ZendTest\Layout\TestAsset\MockActionHelper\Layout'));
        $front  = Controller\Front::getInstance();
        $broker = $front->getHelperBroker();
        $this->assertTrue($broker->hasPlugin('layout'));
        $helper = $broker->load('layout');
        $this->assertTrue($helper instanceof \ZendTest\Layout\TestAsset\MockActionHelper\Layout);
    }

    /**
     * @return void
     */
    public function testEnableInflector()
    {
        $layout = new Layout\Layout();
        $layout->disableInflector();
        $this->assertFalse($layout->inflectorEnabled());
        $layout->enableInflector();
        $this->assertTrue($layout->inflectorEnabled());
    }

    /**
     * @return void
     */
    public function testDisableInflector()
    {
        $layout = new Layout\Layout();
        $layout->disableInflector();
        $this->assertFalse($layout->inflectorEnabled());
    }

    /**
     * @return void
     */
    public function testOverloadingAccessorsWork()
    {
        $layout = new Layout\Layout();
        $layout->foo = 'bar';
        $this->assertTrue(isset($layout->foo));
        $this->assertEquals('bar', $layout->foo);
        unset($layout->foo);
        $this->assertFalse(isset($layout->foo));
    }

    /**
     * @return void
     */
    public function testAssignWithKeyValuePairPopulatesPropertyAccessibleViaOverloading()
    {
        $layout = new Layout\Layout();
        $layout->assign('foo', 'bar');
        $this->assertEquals('bar', $layout->foo);
    }

    /**
     * @return void
     */
    public function testAssignWithArrayPopulatesPropertiesAccessibleViaOverloading()
    {
        $layout = new Layout\Layout();
        $layout->assign(array(
            'foo' => 'bar',
            'bar' => 'baz'
        ));
        $this->assertEquals('bar', $layout->foo);
        $this->assertEquals('baz', $layout->bar);
    }

    /**
     * @return void
     */
    public function testRenderWithNoInflection()
    {
        $layout = new Layout\Layout();
        $view   = new View\PhpRenderer();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
               ->disableInflector()
               ->setLayout('layout.phtml')
               ->setView($view);
        $layout->message = 'Rendered layout';
        $received = $layout->render();
        $this->assertContains('Testing layouts:', $received);
        $this->assertContains($layout->message, $received);
    }

    public function testRenderWithDefaultInflection()
    {
        $layout = new Layout\Layout();
        $view   = new View\PhpRenderer();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
               ->setView($view);
        $layout->message = 'Rendered layout';
        $received = $layout->render();
        $this->assertContains('Testing layouts:', $received);
        $this->assertContains($layout->message, $received);
    }

    public function testRenderWithCustomInflection()
    {
        $layout = new Layout\Layout();
        $view   = new View\PhpRenderer();
        $layout->setLayoutPath(__DIR__ . '/_files/layouts')
               ->setView($view);
        $inflector = $layout->getInflector();
        $inflector->setTarget('test/:script.:suffix')
                  ->setStaticRule('suffix', 'php');
        $layout->message = 'Rendered layout';
        $received = $layout->render();
        $this->assertContains('Testing layouts with custom inflection:', $received);
        $this->assertContains($layout->message, $received);
    }

    public function testGetMvcInstanceReturnsNullWhenStartMvcHasNotBeenCalled()
    {
        $this->assertNull(Layout\Layout::getMvcInstance());
    }

    public function testGetMvcInstanceReturnsLayoutInstanceWhenStartMvcHasBeenCalled()
    {
        $layout = Layout\Layout::startMvc();
        $received = Layout\Layout::getMvcInstance();
        $this->assertSame($layout, $received);
    }

    public function testSubsequentCallsToStartMvcWithOptionsSetState()
    {
        $layout = Layout\Layout::startMvc();
        $this->assertTrue($layout->getMvcSuccessfulActionOnly());
        $this->assertEquals('content', $layout->getContentKey());

        Layout\Layout::startMvc(array(
            'mvcSuccessfulActionOnly' => false,
            'contentKey'              => 'foobar'
        ));
        $this->assertFalse($layout->getMvcSuccessfulActionOnly());
        $this->assertEquals('foobar', $layout->getContentKey());
    }

    public function testGetViewSuffixRetrievesDefaultValue()
    {
        $layout = new Layout\Layout();
        $this->assertEquals('phtml', $layout->getViewSuffix());
    }

    public function testViewSuffixAccessorsWork()
    {
        $layout = new Layout\Layout();
        $layout->setViewSuffix('php');
        $this->assertEquals('php', $layout->getViewSuffix());
    }

    public function testSettingViewSuffixChangesInflectorSuffix()
    {
        $layout = new Layout\Layout();
        $inflector = $layout->getInflector();
        $rules = $inflector->getRules();
        $this->assertTrue(isset($rules['suffix']));
        $this->assertEquals($layout->getViewSuffix(), $rules['suffix']);
        $layout->setViewSuffix('php');
        $this->assertEquals($layout->getViewSuffix(), $rules['suffix']);
    }

    public function testGetInflectorTargetRetrievesDefaultValue()
    {
        $layout = new Layout\Layout();
        $this->assertEquals(':script.:suffix', $layout->getInflectorTarget());
    }

    public function testInflectorTargetAccessorsWork()
    {
        $layout = new Layout\Layout();
        $layout->setInflectorTarget(':script-foo.:suffix');
        $this->assertEquals(':script-foo.:suffix', $layout->getInflectorTarget());
    }

    public function testSettingInflectorTargetChangesInflectorSuffix()
    {
        $layout = new Layout\Layout();
        $inflector = $layout->getInflector();
        $target = $inflector->getTarget();
        $this->assertEquals($layout->getInflectorTarget(), $inflector->getTarget());
        $layout->setInflectorTarget('php');
        $this->assertEquals($layout->getInflectorTarget(), $inflector->getTarget());
    }

    /**
     * Disabled for now; idea of base paths is under question for Zend\View
     *
     * @group disable
     */
    public function testLayoutWithViewBasePath()
    {
        $layout = new Layout\Layout(array(
            'viewBasePath' => __DIR__ . '/_files/layouts-basepath/')
            );
        $this->assertEquals('layout inside basePath', $layout->render());
        $layout->setLayout('layout2');
        $this->assertEquals('foobar-helper-output', $layout->render());
    }

    public function testResettingMvcInstanceUnregistersHelperAndPlugin()
    {
        $this->testGetMvcInstanceReturnsLayoutInstanceWhenStartMvcHasBeenCalled();
        Layout\Layout::resetMvcInstance();
        $front = Controller\Front::getInstance();
        $this->assertFalse($front->hasPlugin('Zend_Layout_Controller_Plugin_Layout'), 'Plugin not unregistered');
        $broker = $front->getHelperBroker();
        $this->assertFalse($broker->hasPlugin('Layout'), 'Helper not unregistered');
    }

    public function testResettingMvcInstanceRemovesMvcSingleton()
    {
        $this->testGetMvcInstanceReturnsLayoutInstanceWhenStartMvcHasBeenCalled();
        Layout\Layout::resetMvcInstance();
        $this->assertNull(Layout\Layout::getMvcInstance());
    }

    public function testMinimalViewObjectWorks()
    {
        $layout = new Layout\Layout(array(
            'view' => new \ZendTest\Layout\TestAsset\MinimalCustomView(),
            'ViewScriptPath' => 'some/path'
            ));
        $layout->render();
    }

    /**
     * @group ZF-5152
     */
    public function testCallingStartMvcTwiceDoesntGenerateAnyUnexpectedBehavior()
    {
        Layout\Layout::startMvc('/some/path');
        $this->assertEquals(Layout\Layout::getMvcInstance()->getLayoutPath(),'/some/path');
        Layout\Layout::startMvc('/some/other/path');
        $this->assertEquals(Layout\Layout::getMvcInstance()->getLayoutPath(),'/some/other/path');
        $this->assertTrue(Layout\Layout::getMvcInstance()->isEnabled());
    }

    /**
     * @group ZF-5891
     */
    public function testSetLayoutWithDisabledFlag()
    {
        $layout = new Layout\Layout();
        $layout->disableLayout();
        $layout->setLayout('foo', false);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertFalse($layout->isEnabled());
    }
}
