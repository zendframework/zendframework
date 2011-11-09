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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Layout;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Layout,
    Zend\Config,
    Zend\View;

/**
 * Test class for Zend_Layout.
 *
 * @category   Zend
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Layout
 */
class LayoutTest extends TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
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

    public function testDefaultLayoutStatusAtInitialization()
    {
        $layout = new Layout\Layout();
        $this->assertEquals('layout', $layout->getLayout());
        $this->assertEquals('content', $layout->getContentKey());
        $this->assertTrue($layout->isEnabled());
        $this->assertTrue($layout->inflectorEnabled());
        $this->assertNull($layout->getLayoutPath());
    }

    public function testSetConfigModifiesAttributes()
    {
        $layout = new Layout\Layout();

        $config = new Config\Config(array(
            'layout'           => 'foo',
            'contentKey'       => 'foo',
            'layoutPath'       => __DIR__,
        ));
        $layout->setConfig($config);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertEquals('foo', $layout->getContentKey());
        $this->assertEquals(__DIR__, $layout->getLayoutPath());
    }

    public function testSetOptionsWithConfigObjectModifiesAttributes()
    {
        $layout = new Layout\Layout();

        $config = new Config\Config(array(
            'layout'           => 'foo',
            'contentKey'       => 'foo',
            'layoutPath'       => __DIR__,
        ));
        $layout->setOptions($config);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertEquals('foo', $layout->getContentKey());
        $this->assertEquals(__DIR__, $layout->getLayoutPath());
    }

    public function testLayoutAccessorsModifyAndRetrieveLayoutValue()
    {
        $layout = new Layout\Layout();
        $layout->setLayout('foo');
        $this->assertEquals('foo', $layout->getLayout());
    }

    public function testSetLayoutEnablesLayouts()
    {
        $layout = new Layout\Layout();
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
        $layout->setLayout('foo');
        $this->assertTrue($layout->isEnabled());
    }

    public function testDisableLayoutDisablesLayouts()
    {
        $layout = new Layout\Layout();
        $this->assertTrue($layout->isEnabled());
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
    }

    public function testEnableLayoutEnablesLayouts()
    {
        $layout = new Layout\Layout();
        $this->assertTrue($layout->isEnabled());
        $layout->disableLayout();
        $this->assertFalse($layout->isEnabled());
        $layout->enableLayout();
        $this->assertTrue($layout->isEnabled());
    }

    public function testLayoutPathAccessorsWork()
    {
        $layout = new Layout\Layout();
        $layout->setLayoutPath(__DIR__);
        $this->assertEquals(__DIR__, $layout->getLayoutPath());
    }

    public function testContentKeyAccessorsWork()
    {
        $layout = new Layout\Layout();
        $layout->setContentKey('foo');
        $this->assertEquals('foo', $layout->getContentKey());
    }

    public function testGetViewRetrievesViewWhenNoneSet()
    {
        $layout = new Layout\Layout();
        $view   = $layout->getView();
        $this->assertTrue($view instanceof View\Renderer);
    }

    public function testViewAccessorsAllowSettingView()
    {
        $layout = new Layout\Layout();
        $view   = new View\PhpRenderer();
        $layout->setView($view);
        $received = $layout->getView();
        $this->assertSame($view, $received);
    }

    public function testInflectorAccessorsWork()
    {
        $layout = new Layout\Layout();
        $inflector = new \Zend\Filter\Inflector();
        $layout->setInflector($inflector);
        $this->assertSame($inflector, $layout->getInflector());
    }

    public function testEnableInflector()
    {
        $layout = new Layout\Layout();
        $layout->disableInflector();
        $this->assertFalse($layout->inflectorEnabled());
        $layout->enableInflector();
        $this->assertTrue($layout->inflectorEnabled());
    }

    public function testDisableInflector()
    {
        $layout = new Layout\Layout();
        $layout->disableInflector();
        $this->assertFalse($layout->inflectorEnabled());
    }

    public function testOverloadingAccessorsWork()
    {
        $layout = new Layout\Layout();
        $layout->foo = 'bar';
        $this->assertTrue(isset($layout->foo));
        $this->assertEquals('bar', $layout->foo);
        unset($layout->foo);
        $this->assertFalse(isset($layout->foo));
    }

    public function testAssignWithKeyValuePairPopulatesPropertyAccessibleViaOverloading()
    {
        $layout = new Layout\Layout();
        $layout->assign('foo', 'bar');
        $this->assertEquals('bar', $layout->foo);
    }

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

    public function testMinimalViewObjectWorks()
    {
        $layout = new Layout\Layout(array(
            'view'           => new TestAsset\MinimalCustomView(),
            'ViewScriptPath' => 'some/path'
            ));
        $layout->render();
    }

    public function testSetLayoutWithDisabledFlag()
    {
        $layout = new Layout\Layout();
        $layout->disableLayout();
        $layout->setLayout('foo', false);
        $this->assertEquals('foo', $layout->getLayout());
        $this->assertFalse($layout->isEnabled());
    }
}
