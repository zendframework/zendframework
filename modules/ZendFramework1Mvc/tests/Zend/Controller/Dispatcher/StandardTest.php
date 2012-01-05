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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Dispatcher;

use Zend\Controller,
    Zend\Controller\Request,
    Zend\Controller\Response;


/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Dispatcher
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
    protected $_dispatcher;

    public function setUp()
    {
        if (isset($this->error)) {
            unset($this->error);
        }
        $front = Controller\Front::getInstance();
        $front->resetInstance();
        $broker = $front->getHelperBroker();
        $this->_dispatcher = new \Zend\Controller\Dispatcher\Standard();
        $this->_dispatcher->setControllerDirectory(array(
            'application' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'admin'   => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin'
        ));
        $this->_dispatcher->setHelperBroker($broker);
    }

    public function tearDown()
    {
        unset($this->_dispatcher);
    }

    public function testFormatControllerName()
    {
        $this->assertEquals('IndexController', $this->_dispatcher->formatControllerName('index'));
        $this->assertEquals('Site\CustomController', $this->_dispatcher->formatControllerName('site\custom'));
    }

    public function testFormatActionName()
    {
        $this->assertEquals('indexAction', $this->_dispatcher->formatActionName('index'));
        $this->assertEquals('myindexAction', $this->_dispatcher->formatActionName('myIndex'));
        $this->assertEquals('myindexAction', $this->_dispatcher->formatActionName('my_index'));
        $this->assertEquals('myIndexAction', $this->_dispatcher->formatActionName('my.index'));
        $this->assertEquals('myIndexAction', $this->_dispatcher->formatActionName('my-index'));
    }

    public function testSetGetControllerDirectory()
    {
        $expected = array(
            'application' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'admin'   => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin'
        );
        $dirs = $this->_dispatcher->getControllerDirectory();
        $this->assertEquals($expected, $dirs);
    }

    public function testIsDispatchable()
    {
        $request = new Request\Http();

        $this->assertFalse($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('index');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('foo');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        // True, because it will dispatch to default controller
        $request->setControllerName('bogus');
        $this->assertFalse($this->_dispatcher->isDispatchable($request));
    }

    public function testModuleIsDispatchable()
    {
        $request = new Request\Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo');
        $request->setActionName('bar');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $request->setModuleName('bogus');
        $request->setControllerName('bogus');
        $request->setActionName('bar');
        $this->assertFalse($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));
    }

    /**
     * @group ZF-8222
     */
    public function testIsDispatchableManuallyIncludedController()
    {
        require_once __DIR__ . '/../_files/ManuallyIncludedControllers.php';
        require_once __DIR__ . '/../_files/ManuallyIncludedControllersNamespaced.php';
        $request = new \Zend\Controller\Request\Http();


        $this->_dispatcher->setParam('prefixDefaultModule', true);

        $request->setControllerName('included');
        $this->assertFalse($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('included-prefix');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        $this->_dispatcher->setParam('prefixDefaultModule', false);

        $request->setModuleName('admin');
        $request->setControllerName('included-admin');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));
    }

    public function testSetGetResponse()
    {
        $response = new Response\Cli();
        $this->_dispatcher->setResponse($response);
        $this->assertTrue($response === $this->_dispatcher->getResponse());
    }

    public function testSetGetDefaultControllerName()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultControllerName());

        $this->_dispatcher->setDefaultControllerName('foo');
        $this->assertEquals('foo', $this->_dispatcher->getDefaultControllerName());
    }

    public function testSetGetDefaultAction()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultAction());

        $this->_dispatcher->setDefaultAction('bar');
        $this->assertEquals('bar', $this->_dispatcher->getDefaultAction());
    }

    public function testDispatchValidControllerDefaultAction()
    {
        $request = new Request\Http();
        $request->setControllerName('index');
        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertContains('Index action called', $this->_dispatcher->getResponse()->getBody());
    }

    public function testDispatchValidControllerAndAction()
    {
        $request = new Request\Http();
        $request->setControllerName('index');
        $request->setActionName('index');
        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertContains('Index action called', $this->_dispatcher->getResponse()->getBody());
    }

    public function testDispatchValidControllerWithInvalidAction()
    {
        $request = new Request\Http();
        $request->setControllerName('index');
        $request->setActionName('foo');
        $response = new Response\Cli();

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->fail('Exception should be raised by __call');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testDispatchInvalidController()
    {
        $request = new Request\Http();
        $request->setControllerName('bogus');
        $response = new Response\Cli();

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->fail('Exception should be raised; no such controller');
        } catch (\Exception $e) {
            // success
        }
    }

    public function testDispatchInvalidControllerUsingDefaults()
    {
        $request = new Request\Http();
        $request->setControllerName('bogus');
        $response = new Response\Cli();

        $this->_dispatcher->setParam('useDefaultControllerAlways', true);

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->assertEquals('index', $request->getControllerName());
            $this->assertEquals('index', $request->getActionName());
        } catch (\Exception $e) {
            $this->fail('Exception should not be raised when useDefaultControllerAlways set; message: ' . $e->getMessage());
        }
    }

    /**
     * @group ZF-3465
     */
    public function testUsingDefaultControllerAlwaysShouldRewriteActionNameToDefault()
    {
        $request = new Request\Http();
        $request->setControllerName('bogus');
        $request->setActionName('really');
        $request->setParam('action', 'really'); // router sets action as a param
        $response = new Response\Cli();

        $this->_dispatcher->setParam('useDefaultControllerAlways', true);

        try {
            $this->_dispatcher->dispatch($request, $response);
        } catch (\Zend\Controller\Dispatcher\Exception $e) {
            $this->fail('Exception should not be raised when useDefaultControllerAlways set; message: ' . $e->getMessage());
        }

        $this->assertEquals('index', $request->getControllerName());
        $this->assertEquals('index', $request->getActionName());
    }

    public function testDispatchInvalidControllerUsingDefaultsWithDefaultModule()
    {
        $request = new Request\Http();
        $request->setControllerName('bogus')
                ->setModuleName('application');
        $response = new Response\Cli();

        $this->_dispatcher->setParam('useDefaultControllerAlways', true);

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->assertSame('application', $request->getModuleName());
            $this->assertSame('index', $request->getControllerName());
            $this->assertSame('index', $request->getActionName());
        } catch (\Exception $e) {
            $this->fail('Exception should not be raised when useDefaultControllerAlways set; exception: ' . $e->getMessage());
        }
    }

    public function testDispatchValidControllerWithPrePostDispatch()
    {
        $request = new Request\Http();
        $request->setControllerName('foo');
        $request->setActionName('bar');
        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);

        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains('Bar action called', $body);
        $this->assertContains('preDispatch called', $body);
        $this->assertContains('postDispatch called', $body);
    }

    public function testDispatchNoControllerUsesDefaults()
    {
        $request = new Request\Http();
        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertEquals('index', $request->getControllerName());
        $this->assertEquals('index', $request->getActionName());
    }

    /**
     * Tests ZF-637 -- action names with underscores not being correctly changed to camelCase
     */
    public function testZf637()
    {
        $test = $this->_dispatcher->formatActionName('view_entry');
        $this->assertEquals('viewentryAction', $test);
    }

    public function testWordDelimiter()
    {
        $this->assertEquals(array('-', '.'), $this->_dispatcher->getWordDelimiter());
        $this->_dispatcher->setWordDelimiter(':');
        $this->assertEquals(array(':'), $this->_dispatcher->getWordDelimiter());
    }

    public function testPathDelimiter()
    {
        $this->assertEquals('\\', $this->_dispatcher->getPathDelimiter());
        $this->_dispatcher->setPathDelimiter(':');
        $this->assertEquals(':', $this->_dispatcher->getPathDelimiter());
    }

    /**
     * Test that classes are found in modules, using a prefix
     */
    public function testModules()
    {
        $request = new Request\Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo');
        $request->setActionName('bar');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Admin_Foo::bar action called", $body, $body);
    }

    public function testModuleControllerInSubdirWithCamelCaseAction()
    {
        $request = new Request\Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo-bar');
        $request->setActionName('baz.bat');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Admin_FooBar::bazBat action called", $body, $body);
    }

    public function testUseModuleDefaultController()
    {
        $this->_dispatcher->setDefaultControllerName('foo')
             ->setParam('useDefaultControllerAlways', true);

        $request = new Request\Http();
        $request->setModuleName('admin');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Admin_Foo::index action called", $body, $body);
    }

    public function testNoModuleOrControllerDefaultsCorrectly()
    {
        $request = new Request\Http('http://example.com/');

        $this->assertFalse($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Index action called", $body, $body);
    }

    public function testOutputBuffering()
    {
        $request = new Request\Http();
        $request->setControllerName('ob');
        $request->setActionName('index');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("OB index action called", $body, $body);
    }

    public function testDisableOutputBuffering()
    {
        if (!defined('TESTS_ZEND_CONTROLLER_DISPATCHER_OB') || !TESTS_ZEND_CONTROLLER_DISPATCHER_OB) {
            $this->markTestSkipped('Skipping output buffer disabling in Zend_Controller_Dispatcher_Standard');
        }

        $request = new Request\Http();
        $request->setControllerName('ob');
        $request->setActionName('index');
        $this->_dispatcher->setParam('disableOutputBuffering', true);

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertEquals('', $body, $body);
    }

    public function testModuleSubdirControllerFound()
    {
        Controller\Front::getInstance()
            ->setDispatcher($this->_dispatcher)
            ->addControllerDirectory(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'controllers',
                'foo'
        );

        $request = new Request\Http();
        $request->setModuleName('foo');
        $request->setControllerName('admin\index');
        $request->setActionName('index');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        $this->_dispatcher->dispatch($request, $response);
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains("Foo_Admin_IndexController::indexAction() called", $body, $body);
    }

    public function testDefaultModule()
    {
        $this->assertEquals('application', $this->_dispatcher->getDefaultModule());
        $this->_dispatcher->setDefaultModule('foobar');
        $this->assertEquals('foobar', $this->_dispatcher->getDefaultModule());
    }

    public function testModuleValid()
    {
        $this->assertTrue($this->_dispatcher->isValidModule('application'));
        $this->assertTrue($this->_dispatcher->isValidModule('admin'));
        $this->assertFalse($this->_dispatcher->isValidModule('bogus'));
        $this->assertFalse($this->_dispatcher->isValidModule(null));
        $this->assertFalse($this->_dispatcher->isValidModule($this));
        $this->assertFalse($this->_dispatcher->isValidModule(array()));
    }

    /**
     * @group ZF-3034
     */
    public function testIsValidModuleShouldNormalizeModuleName()
    {
        $this->assertTrue($this->_dispatcher->isValidModule('Admin'));
    }

    public function testSanelyDiscardOutputBufferOnException()
    {
        $request = new Request\Http();
        $request->setControllerName('ob');
        $request->setActionName('exception');

        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->fail('Exception should have been rethrown');
        } catch (\Exception $e) {
        }
        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertNotContains("In exception action", $body, $body);
        $this->assertNotContains("Foo", $body, $body);
    }

    public function testGetDefaultControllerClassResetsRequestObject()
    {
        $request = new Request\Http();
        $request->setModuleName('foobar')
                ->setControllerName('bazbatbegone')
                ->setActionName('bebop');
        $this->_dispatcher->getDefaultControllerClass($request);
        $this->assertEquals($this->_dispatcher->getDefaultModule(), $request->getModuleName());
        $this->assertEquals($this->_dispatcher->getDefaultControllerName(), $request->getControllerName());
        $this->assertNull($request->getActionName());
    }

    public function testLoadClassLoadsControllerInDefaultModuleWithoutModulePrefix()
    {
        $request = new Request\Simple();
        $request->setControllerName('empty');
        $class = $this->_dispatcher->getControllerClass($request);
        $this->assertEquals('EmptyController', $class);
        $test = $this->_dispatcher->loadClass($class);
        $this->assertEquals($class, $test);
        $this->assertTrue(class_exists($class));
    }

    public function testLoadClassLoadsControllerInSpecifiedModuleWithModulePrefix()
    {
        Controller\Front::getInstance()
            ->setDispatcher($this->_dispatcher)
            ->addModuleDirectory(__DIR__ . '/../_files/modules');
        $request = new Request\Simple();
        $request->setControllerName('index')
                ->setModuleName('bar');
        $class = $this->_dispatcher->getControllerClass($request);
        $this->assertEquals('IndexController', $class);
        $test = $this->_dispatcher->loadClass($class);
        $this->assertEquals('Bar\IndexController', $test);
        $this->assertTrue(class_exists($test));
    }

    public function testLoadClassLoadsControllerInDefaultModuleWithModulePrefixWhenRequested()
    {
        Controller\Front::getInstance()
            ->setDispatcher($this->_dispatcher)
            ->addModuleDirectory(__DIR__ . '/../_files/modules');
        $this->_dispatcher->setDefaultModule('foo')
                          ->setParam('prefixDefaultModule', true);
        $request = new Request\Simple();
        $request->setControllerName('index');
        $class = $this->_dispatcher->getControllerClass($request);
        $this->assertEquals('IndexController', $class);
        $test = $this->_dispatcher->loadClass($class);
        $this->assertEquals('Foo\IndexController', $test);
        $this->assertTrue(class_exists($test));
    }

    /**
     * @group ZF-2435
     */
    public function testCanRemoveControllerDirectory()
    {
        Controller\Front::getInstance()
            ->setDispatcher($this->_dispatcher)
            ->addModuleDirectory(__DIR__ . '/../_files/modules');
        $dirs = $this->_dispatcher->getControllerDirectory();
        $this->_dispatcher->removeControllerDirectory('foo');
        $test = $this->_dispatcher->getControllerDirectory();
        $this->assertNotEquals($dirs, $test);
        $this->assertFalse(array_key_exists('foo', $test));
    }

    /**
     * @group ZF-2693
     */
    public function testCamelCasedActionsNotRequestedWithWordSeparatorsShouldNotResolve()
    {
        $request = new Request\Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo-bar');
        $request->setActionName('bazBat');
        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->fail('Invalid camelCased action should raise exception');
        } catch (Controller\Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
    }

    /**
     * @group ZF-2693
     * @group disable
     */
    public function testCamelCasedActionsNotRequestedWithWordSeparatorsShouldResolveIfForced()
    {
        $this->_dispatcher->setParam('useCaseSensitiveActions', true);
        $request = new Request\Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo-bar');
        $request->setActionName('bazBat');
        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        $oldLevel = error_reporting(0);
        try {
            $this->_dispatcher->dispatch($request, $response);
            $body = $this->_dispatcher->getResponse()->getBody();
            error_reporting($oldLevel);
            $this->assertContains("Admin_FooBar::bazBat action called", $body, $body);
        } catch (Controller\Exception $e) {
            error_reporting($oldLevel);
            $this->fail('camelCased actions should succeed when forced');
        }
    }

    public function handleErrors($errno, $errstr)
    {
        $this->error = $errstr;
    }

    /**
     * @group ZF-2693
     */
    public function testForcingCamelCasedActionsNotRequestedWithWordSeparatorsShouldRaiseNotices()
    {
        $this->_dispatcher->setParam('useCaseSensitiveActions', true);
        $request = new Request\Http();
        $request->setModuleName('admin');
        $request->setControllerName('foo-bar');
        $request->setActionName('bazBat');
        $this->assertTrue($this->_dispatcher->isDispatchable($request), var_export($this->_dispatcher->getControllerDirectory(), 1));

        $response = new Response\Cli();
        set_error_handler(array($this, 'handleErrors'));
        try {
            $this->_dispatcher->dispatch($request, $response);
            $body = $this->_dispatcher->getResponse()->getBody();
            restore_error_handler();
            $this->assertTrue(isset($this->error));
            $this->assertContains('deprecated', $this->error);
        } catch (Controller\Exception $e) {
            restore_error_handler();
            $this->fail('camelCased actions should succeed when forced');
        }
    }

    /**
     * @group ZF-2887
     */
    public function testGetControllerClassThrowsExceptionIfNoDefaultModuleDefined()
    {
        $this->_dispatcher->setControllerDirectory(array());

        $request = new Request\Simple();
        $request->setControllerName('empty');
        try {
            $class = $this->_dispatcher->getControllerClass($request);
        } catch (Controller\Exception $e) {
            $this->assertContains('No default module', $e->getMessage());
        }
    }
}
