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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test class for Zend_Layout.
 *
 * @category   Zend
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Layout
 */
class Zend_Layout_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Layout_LayoutTest");
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
        Zend_Layout_LayoutTest_Override::resetMvcInstance();

        Zend_Controller_Front::getInstance()->resetInstance();
        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
            Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        }
        if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
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
        Zend_Layout::resetMvcInstance();
    }

    public function testDefaultLayoutStatusAtInitialization()
    {
        $layout = new Zend_Layout();
        $this->assertEquals('layout', $layout->getLayout());
        $this->assertEquals('content', $layout->getContentKey());
        $this->assertTrue($layout->isEnabled());
        $this->assertTrue($layout->inflectorEnabled());
        $this->assertNull($layout->getLayoutPath());
        $this->assertFalse($layout->getMvcEnabled());
    }

    public function testDefaultLayoutStatusAtInitializationWhenInitMvcFlagPassed()
    {
        $layout = new Zend_Layout(null, true);
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
        $layout = new Zend_Layout();

        $config = new Zend_Config(array(
            'layout'           => 'foo',
            'contentKey'       => 'foo',
            'layoutPath'       => dirname(__FILE__),
            'mvcEnabled'       => false,
        ));
        $layout->setConfig($config);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertEquals('foo', $layout->getContentKey());
        $this->assertEquals(dirname(__FILE__), $layout->getLayoutPath());
        $this->assertFalse($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testSetOptionsWithConfigObjectModifiesAttributes()
    {
        $layout = new Zend_Layout();

        $config = new Zend_Config(array(
            'layout'           => 'foo',
            'contentKey'       => 'foo',
            'layoutPath'       => dirname(__FILE__),
            'mvcEnabled'       => false,
        ));
        $layout->setOptions($config);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertEquals('foo', $layout->getContentKey());
        $this->assertEquals(dirname(__FILE__), $layout->getLayoutPath());
        $this->assertFalse($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testLayoutAccessorsModifyAndRetrieveLayoutValue()
    {
        $layout = new Zend_Layout();
        $layout->setLayout('foo');
        $this->assertEquals('foo', $layout->getLayout());
    }

    /**
     * @return void
     */
    public function testSetLayoutEnablesLayouts()
    {
        $layout = new Zend_Layout();
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
        $layout = new Zend_Layout();
        $this->assertTrue($layout->isEnabled());
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
    }

    /**
     * @return void
     */
    public function testEnableLayoutEnablesLayouts()
    {
        $layout = new Zend_Layout();
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
        $layout = new Zend_Layout();
        $layout->setLayoutPath(dirname(__FILE__));
        $this->assertEquals(dirname(__FILE__), $layout->getLayoutPath());
    }

    /**
     * @return void
     */
    public function testContentKeyAccessorsWork()
    {
        $layout = new Zend_Layout();
        $layout->setContentKey('foo');
        $this->assertEquals('foo', $layout->getContentKey());
    }

    /**
     * @return void
     */
    public function testMvcEnabledFlagFalseAfterStandardInstantiation()
    {
        $layout = new Zend_Layout();
        $this->assertFalse($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testMvcEnabledFlagTrueWhenInstantiatedViaStartMvcMethod()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->getMvcEnabled());
    }

    /**
     * @return void
     */
    public function testGetViewRetrievesViewWhenNoneSet()
    {
        $layout = new Zend_Layout();
        $view = $layout->getView();
        $this->assertTrue($view instanceof Zend_View_Interface);
    }

    /**
     * @return void
     */
    public function testGetViewRetrievesViewFromViewRenderer()
    {
        $layout = new Zend_Layout();
        $view = $layout->getView();
        $vr = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->assertSame($vr->view, $view);
    }

    /**
     * @return void
     */
    public function testViewAccessorsAllowSettingView()
    {
        $layout = new Zend_Layout();
        $view   = new Zend_View();
        $layout->setView($view);
        $received = $layout->getView();
        $this->assertSame($view, $received);
    }

    /**
     * @return void
     */
    public function testInflectorAccessorsWork()
    {
        $layout = new Zend_Layout();
        $inflector = new Zend_Filter_Inflector();
        $layout->setInflector($inflector);
        $this->assertSame($inflector, $layout->getInflector());
    }

    /**
     * @return void
     */
    public function testPluginClassAccessorsSetState()
    {
        $layout = new Zend_Layout();
        $layout->setPluginClass('Foo_Bar');
        $this->assertEquals('Foo_Bar', $layout->getPluginClass());
    }

    /**
     * @return void
     */
    public function testPluginClassPassedToStartMvcIsUsed()
    {
        $layout = Zend_Layout::startMvc(array('pluginClass' => 'Zend_Layout_LayoutTest_Controller_Plugin_Layout'));
        $this->assertTrue(Zend_Controller_Front::getInstance()->hasPlugin('Zend_Layout_LayoutTest_Controller_Plugin_Layout'));
    }

    /**
     * @return void
     */
    public function testHelperClassAccessorsSetState()
    {
        $layout = new Zend_Layout();
        $layout->setHelperClass('Foo_Bar');
        $this->assertEquals('Foo_Bar', $layout->getHelperClass());
    }

    /**
     * @return void
     */
    public function testHelperClassPassedToStartMvcIsUsed()
    {
        $layout = Zend_Layout::startMvc(array('helperClass' => 'Zend_Layout_LayoutTest_Controller_Action_Helper_Layout'));
        $this->assertTrue(Zend_Controller_Action_HelperBroker::hasHelper('layout'));
        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('layout');
        $this->assertTrue($helper instanceof Zend_Layout_LayoutTest_Controller_Action_Helper_Layout);
    }

    /**
     * @return void
     */
    public function testEnableInflector()
    {
        $layout = new Zend_Layout();
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
        $layout = new Zend_Layout();
        $layout->disableInflector();
        $this->assertFalse($layout->inflectorEnabled());
    }

    /**
     * @return void
     */
    public function testOverloadingAccessorsWork()
    {
        $layout = new Zend_Layout();
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
        $layout = new Zend_Layout();
        $layout->assign('foo', 'bar');
        $this->assertEquals('bar', $layout->foo);
    }

    /**
     * @return void
     */
    public function testAssignWithArrayPopulatesPropertiesAccessibleViaOverloading()
    {
        $layout = new Zend_Layout();
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
        $layout = new Zend_Layout();
        $view   = new Zend_View();
        $layout->setLayoutPath(dirname(__FILE__) . '/_files/layouts')
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
        $layout = new Zend_Layout();
        $view   = new Zend_View();
        $layout->setLayoutPath(dirname(__FILE__) . '/_files/layouts')
               ->setView($view);
        $layout->message = 'Rendered layout';
        $received = $layout->render();
        $this->assertContains('Testing layouts:', $received);
        $this->assertContains($layout->message, $received);
    }

    public function testRenderWithCustomInflection()
    {
        $layout = new Zend_Layout();
        $view   = new Zend_View();
        $layout->setLayoutPath(dirname(__FILE__) . '/_files/layouts')
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
        $this->assertNull(Zend_Layout::getMvcInstance());
    }

    public function testGetMvcInstanceReturnsLayoutInstanceWhenStartMvcHasBeenCalled()
    {
        $layout = Zend_Layout::startMvc();
        $received = Zend_Layout::getMvcInstance();
        $this->assertSame($layout, $received);
    }

    public function testSubsequentCallsToStartMvcWithOptionsSetState()
    {
        $layout = Zend_Layout::startMvc();
        $this->assertTrue($layout->getMvcSuccessfulActionOnly());
        $this->assertEquals('content', $layout->getContentKey());

        Zend_Layout::startMvc(array(
            'mvcSuccessfulActionOnly' => false,
            'contentKey'              => 'foobar'
        ));
        $this->assertFalse($layout->getMvcSuccessfulActionOnly());
        $this->assertEquals('foobar', $layout->getContentKey());
    }

    public function testGetViewSuffixRetrievesDefaultValue()
    {
        $layout = new Zend_Layout();
        $this->assertEquals('phtml', $layout->getViewSuffix());
    }

    public function testViewSuffixAccessorsWork()
    {
        $layout = new Zend_Layout();
        $layout->setViewSuffix('php');
        $this->assertEquals('php', $layout->getViewSuffix());
    }

    public function testSettingViewSuffixChangesInflectorSuffix()
    {
        $layout = new Zend_Layout();
        $inflector = $layout->getInflector();
        $rules = $inflector->getRules();
        $this->assertTrue(isset($rules['suffix']));
        $this->assertEquals($layout->getViewSuffix(), $rules['suffix']);
        $layout->setViewSuffix('php');
        $this->assertEquals($layout->getViewSuffix(), $rules['suffix']);
    }

    public function testGetInflectorTargetRetrievesDefaultValue()
    {
        $layout = new Zend_Layout();
        $this->assertEquals(':script.:suffix', $layout->getInflectorTarget());
    }

    public function testInflectorTargetAccessorsWork()
    {
        $layout = new Zend_Layout();
        $layout->setInflectorTarget(':script-foo.:suffix');
        $this->assertEquals(':script-foo.:suffix', $layout->getInflectorTarget());
    }

    public function testSettingInflectorTargetChangesInflectorSuffix()
    {
        $layout = new Zend_Layout();
        $inflector = $layout->getInflector();
        $target = $inflector->getTarget();
        $this->assertEquals($layout->getInflectorTarget(), $inflector->getTarget());
        $layout->setInflectorTarget('php');
        $this->assertEquals($layout->getInflectorTarget(), $inflector->getTarget());
    }

    public function testLayoutWithViewBasePath()
    {
        $layout = new Zend_Layout(array(
            'viewBasePath' => dirname(__FILE__) . '/_files/layouts-basepath/')
            );
        $this->assertEquals('layout inside basePath', $layout->render());
        $layout->setLayout('layout2');
        $this->assertEquals('foobar-helper-output', $layout->render());
    }

    public function testResettingMvcInstanceUnregistersHelperAndPlugin()
    {
        $this->testGetMvcInstanceReturnsLayoutInstanceWhenStartMvcHasBeenCalled();
        Zend_Layout::resetMvcInstance();
        $front = Zend_Controller_Front::getInstance();
        $this->assertFalse($front->hasPlugin('Zend_Layout_Controller_Plugin_Layout'), 'Plugin not unregistered');
        $this->assertFalse(Zend_Controller_Action_HelperBroker::hasHelper('Layout'), 'Helper not unregistered');
    }

    public function testResettingMvcInstanceRemovesMvcSingleton()
    {
        $this->testGetMvcInstanceReturnsLayoutInstanceWhenStartMvcHasBeenCalled();
        Zend_Layout::resetMvcInstance();
        $this->assertNull(Zend_Layout::getMvcInstance());
    }

    public function testMinimalViewObjectWorks()
    {
        require_once dirname(__FILE__) . '/_files/MinimalCustomView.php';
        $layout = new Zend_Layout(array(
            'view' => new Zend_Layout_Test_MinimalCustomView(),
            'ViewScriptPath' => 'some/path'
            ));
        $layout->render();
    }

    /**
     * @group ZF-5152
     */
    public function testCallingStartMvcTwiceDoesntGenerateAnyUnexpectedBehavior()
    {
        Zend_Layout::startMvc('/some/path');
        $this->assertEquals(Zend_Layout::getMvcInstance()->getLayoutPath(),'/some/path');
        Zend_Layout::startMvc('/some/other/path');
        $this->assertEquals(Zend_Layout::getMvcInstance()->getLayoutPath(),'/some/other/path');
        $this->assertTrue(Zend_Layout::getMvcInstance()->isEnabled());
    }

    /**
     * @group ZF-5891
     */
    public function testSetLayoutWithDisabledFlag()
    {
        $layout = new Zend_Layout();
        $layout->disableLayout();
        $layout->setLayout('foo', false);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertFalse($layout->isEnabled());
    }
}

/**
 * Zend_Layout extension to allow resetting mvcInstance static member
 */
class Zend_Layout_LayoutTest_Override extends Zend_Layout
{
    public static function resetMvcInstance()
    {
        self::$_mvcInstance = null;
    }
}

class Zend_Layout_LayoutTest_Controller_Plugin_Layout extends Zend_Layout_Controller_Plugin_Layout
{
}

class Zend_Layout_LayoutTest_Controller_Action_Helper_Layout extends Zend_Layout_Controller_Action_Helper_Layout
{
}
