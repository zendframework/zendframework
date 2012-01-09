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
namespace ZendTest\Controller\Action\Helper;

use Zend\Controller\Action\Helper,
    Zend\Controller\Front as FrontController,
    Zend\View,
    Zend\Filter;

require_once __DIR__ . '/../../_files/modules/foo/controllers/IndexController.php';
require_once __DIR__ . '/../../_files/modules/bar/controllers/IndexController.php';

/**
 * Test class for Zend_Controller_Action_Helper_ViewRenderer.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class ViewRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Base path to controllers, views
     * @var string
     */
    public $basePath;

    /**
     * Front controller object
     * @var Zend_Controller_Front
     */
    public $front;

    /**
     * ViewRenderer helper
     * @var Zend_Controller_Action_Helper_ViewRenderer
     */
    public $helper;

    /**
     * Request object
     * @var Zend_Controller_Request_HTTP
     */
    public $request;

    /**
     * Response object
     * @var Zend_Controller_Response_HTTP
     */
    public $response;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->basePath = realpath(__DIR__ . str_repeat(DIRECTORY_SEPARATOR . '..', 2));
        $this->request  = new \Zend\Controller\Request\Http();
        $this->response = new \Zend\Controller\Response\Http();
        $this->front    = \Zend\Controller\Front::getInstance();
        $this->front->resetInstance();
        $this->front->addModuleDirectory($this->basePath . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules')
                    ->setRequest($this->request)
                    ->setResponse($this->response);
        $this->broker   = $this->front->getHelperBroker();

        $this->helper   = new Helper\ViewRenderer();
        $this->broker->register('viewRenderer', $this->helper);
    }

    public function testConstructorSetsViewWhenPassed()
    {
        $view   = new View\PhpRenderer();
        $helper = new Helper\ViewRenderer($view);
        $this->assertNotNull(isset($helper->view));
        $this->assertSame($view, $helper->view);
    }

    public function testConstructorSetsOptionsWhenPassed()
    {
        $helper = new Helper\ViewRenderer(null, array(
            'neverRender'     => true,
            'noRender'        => true,
            'noController'    => true,
            'viewSuffix'      => 'php',
            'scriptAction'    => 'foo',
            'responseSegment' => 'baz'
        ));

        $this->assertTrue($helper->getNeverRender());
        $this->assertTrue($helper->getNoRender());
        $this->assertTrue($helper->getNoController());
        $this->assertEquals('php', $helper->getViewSuffix());
        $this->assertEquals('foo', $helper->getScriptAction());
        $this->assertEquals('baz', $helper->getResponseSegment());
    }

    public function testSetView()
    {
        $view = new View\PhpRenderer();
        $this->helper->setView($view);
        $this->assertSame($view, $this->helper->view);
    }

    public function testGetFrontController()
    {
        $this->assertSame($this->front, $this->helper->getFrontController());
    }

    protected function _checkDefaults($module = 'foo', $count = 1)
    {
        $this->assertTrue(isset($this->helper->view));
        $this->assertTrue($this->helper->view instanceof View\Renderer);
        $this->assertFalse($this->helper->getNeverRender());
        $this->assertFalse($this->helper->getNoRender());
        $this->assertNull($this->helper->getResponseSegment());
        $this->assertNull($this->helper->getScriptAction());

        /*
         * @todo determine how paths, helpers, and filters will be affected by view renderer
        $scriptPaths = $this->helper->view->getScriptPaths();
        $this->assertEquals($count, count($scriptPaths), var_export($scriptPaths, 1));
        $this->assertContains($module, $scriptPaths[0]);

        $helperPaths = $this->helper->view->getHelperPaths();
        $testNS      = ucfirst($module) . '\View\Helper\\';
        $testPrefix  = ucfirst($module) . '_View_Helper_';
        $found       = false;
        foreach ($helperPaths as $prefix => $paths) {
            if ($testNS == $prefix || $testPrefix == $prefix) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Did not find auto-initialized helper path: ' . var_export($helperPaths, 1));

        $filterPaths = $this->helper->view->getFilterPaths();
        $testNS      = ucfirst($module) . '\View\Filter\\';
        $testPrefix  = ucfirst($module) . '_View_Filter_';
        $found = false;
        foreach ($filterPaths as $prefix => $paths) {
            if ($testNS == $prefix || $testPrefix == $prefix) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Did not find auto-initialized filter path: ' . var_export($filterPaths, 1));
         */
    }

    public function testInitViewWithDefaults()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');

        $controller = new \Foo\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);
        $this->helper->initView();
        $this->_checkDefaults();
    }

    public function testInitViewWillNotRegisterSameViewPathTwice()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');
        $controller = new \Foo\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);

        $this->helper->initView();

        $moduleDir = dirname($this->front->getControllerDirectory('foo'));
        $this->helper->initView($moduleDir . '/views', 'Foo', array('encoding' => 'ISO-8858-1'));
        $this->_checkDefaults();
    }

    public function testInitViewCanBeCalledAfterPostDispatch()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');
        $controller = new \Foo\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);

        $this->helper->initView();
        $this->helper->setNoRender();
        $this->helper->postDispatch();
        $this->request->setModuleName('bar')
                      ->setControllerName('index');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);
        $this->helper->initView();
        $this->_checkDefaults('bar', 2);
    }

    public function testPreDispatchWithDefaults()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');
        $controller = new \Foo\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);

        $this->helper->preDispatch();
        $this->_checkDefaults();
    }

    public function testInitViewWithOptions()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');
        $controller = new \Foo\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);

        $viewDir = __DIR__ . str_repeat(DIRECTORY_SEPARATOR . '..', 2) . DIRECTORY_SEPARATOR . 'views';
        $this->helper->initView($viewDir, 'Baz_Bat', array(
            'neverRender'     => true,
            'noRender'        => true,
            'noController'    => true,
            'viewSuffix'      => 'php',
            'scriptAction'    => 'foo',
            'responseSegment' => 'baz'
        ));

        $this->assertTrue($this->helper->getNeverRender());
        $this->assertTrue($this->helper->getNoRender());
        $this->assertTrue($this->helper->getNoController());
        $this->assertEquals('php', $this->helper->getViewSuffix());
        $this->assertEquals('foo', $this->helper->getScriptAction());
        $this->assertEquals('baz', $this->helper->getResponseSegment());

        $scriptPaths = $this->helper->view->resolver()->getPaths();
        $scriptPath  = $scriptPaths[0];
        $this->assertContains(
            $this->_normalizePath($viewDir),
            $this->_normalizePath($scriptPath)
            );

        /*
         * @todo Determine how helpers and filters are affected by view renderer
        $helperPaths = $this->helper->view->getHelperPaths();
        $found       = false;
        foreach ($helperPaths as $prefix => $paths) {
            if ('Baz_Bat_Helper_' == $prefix) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Helper prefix not set according to spec: ' . var_export($helperPaths, 1));

        $filterPaths = $this->helper->view->getFilterPaths();
        $found       = false;
        foreach ($filterPaths as $prefix => $paths) {
            if ('Baz_Bat_Filter_' == $prefix) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Filter prefix not set according to spec' . var_export($filterPaths, 1));
         */
    }

    public function testNeverRenderFlag()
    {
        $this->assertFalse($this->helper->getNeverRender());
        $this->helper->setNeverRender();
        $this->assertTrue($this->helper->getNeverRender());
        $this->helper->setNeverRender(false);
        $this->assertFalse($this->helper->getNeverRender());
        $this->helper->setNeverRender(true);
        $this->assertTrue($this->helper->getNeverRender());
    }

    public function testNeverRenderFlagDisablesRendering()
    {
        $this->helper->setNeverRender();
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test')
                      ->setDispatched(true);
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);
        $this->helper->postDispatch();

        $content = $this->response->getBody();
        $this->assertNotContains('Rendered index/test.phtml', $this->response->getBody());
    }

    public function testNoRenderFlag()
    {
        $this->assertFalse($this->helper->getNoRender());
        $this->helper->setNoRender();
        $this->assertTrue($this->helper->getNoRender());
        $this->helper->setNoRender(false);
        $this->assertFalse($this->helper->getNoRender());
        $this->helper->setNoRender(true);
        $this->assertTrue($this->helper->getNoRender());
    }

    public function testScriptActionProperty()
    {
        $this->assertNull($this->helper->getScriptAction());
        $this->helper->setScriptAction('foo');
        $this->assertEquals('foo', $this->helper->getScriptAction());
        $this->helper->setScriptAction('foo/bar');
        $this->assertEquals('foo/bar', $this->helper->getScriptAction());
    }

    public function testResponseSegmentProperty()
    {
        $this->assertNull($this->helper->getResponseSegment());
        $this->helper->setResponseSegment('foo');
        $this->assertEquals('foo', $this->helper->getResponseSegment());
        $this->helper->setResponseSegment('foo/bar');
        $this->assertEquals('foo/bar', $this->helper->getResponseSegment());
    }

    public function testNoControllerFlag()
    {
        $this->assertFalse($this->helper->getNoController());
        $this->helper->setNoController();
        $this->assertTrue($this->helper->getNoController());
        $this->helper->setNoController(false);
        $this->assertFalse($this->helper->getNoController());
        $this->helper->setNoController(true);
        $this->assertTrue($this->helper->getNoController());
    }

    public function testNeverControllerFlag()
    {
        $this->assertFalse($this->helper->getNeverController());
        $this->helper->setNeverController();
        $this->assertTrue($this->helper->getNeverController());
        $this->helper->setNeverController(false);
        $this->assertFalse($this->helper->getNeverController());
        $this->helper->setNeverController(true);
        $this->assertTrue($this->helper->getNeverController());
    }

    protected function _checkRenderProperties()
    {
        $this->assertEquals('foo', $this->helper->getScriptAction());
        $this->assertEquals('bar', $this->helper->getResponseSegment());
        $this->assertTrue($this->helper->getNoController());
    }

    public function testSetRenderSetsProperties()
    {
        $this->helper->setRender('foo', 'bar', true);
        $this->_checkRenderProperties();
    }

    public function testPostDispatchRendersAppropriateScript()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test')
                      ->setDispatched(true);
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);
        $this->helper->postDispatch();

        $content = $this->response->getBody();
        $this->assertContains('Rendered index/test.phtml in bar module', $content);
    }

    public function testPostDispatchDoesNothingOnForward()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test')
                      ->setDispatched(false);
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);
        $this->helper->postDispatch();

        $content = $this->response->getBody();
        $this->assertNotContains('Rendered index/test.phtml in bar module', $content);
        $this->assertTrue(empty($content));
    }

    public function testPostDispatchDoesNothingOnRedirect()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test')
                      ->setDispatched(true);
        $this->response->setHttpResponseCode(302);
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);
        $this->helper->postDispatch();

        $content = $this->response->getBody();
        $this->assertNotContains('Rendered index/test.phtml in bar module', $content);
        $this->assertTrue(empty($content));
    }

    public function testPostDispatchDoesNothingWithNoController()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test')
                      ->setDispatched(true);
        $this->helper->postDispatch();

        $content = $this->response->getBody();
        $this->assertNotContains('Rendered index/test.phtml in bar module', $content);
        $this->assertTrue(empty($content));
    }

    public function testPostDispatchDoesNothingWithNeverController()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test')
                      ->setDispatched(true);
        $this->helper->setNeverController(true);
        $this->helper->postDispatch();

        $content = $this->response->getBody();
        $this->assertNotContains('Rendered index/test.phtml in bar module', $content);
        $this->assertTrue(empty($content));
    }

    public function testDirectProxiesToSetRender()
    {
        $this->helper->direct('foo', 'bar', true);
        $this->_checkRenderProperties();
    }

    public function testViewBasePathSpecDefault()
    {
        $this->assertEquals(':moduleDir/views', $this->helper->getViewBasePathSpec());
    }

    public function testSettingViewBasePathSpec()
    {
        $this->helper->setViewBasePathSpec(':moduleDir/views/:controller');
        $this->assertEquals(':moduleDir/views/:controller', $this->helper->getViewBasePathSpec());
    }

    public function testViewScriptPathSpecDefault()
    {
        $this->assertEquals(':controller/:action.:suffix', $this->helper->getViewScriptPathSpec());
    }

    public function testSettingViewScriptPathSpec()
    {
        $this->helper->setViewScriptPathSpec(':moduleDir/views/:controller');
        $this->assertEquals(':moduleDir/views/:controller', $this->helper->getViewScriptPathSpec());
    }

    public function testViewScriptPathNoControllerSpecDefault()
    {
        $this->assertEquals(':action.:suffix', $this->helper->getViewScriptPathNoControllerSpec());
    }

    public function testSettingViewScriptPathNoControllerSpec()
    {
        $this->helper->setViewScriptPathNoControllerSpec(':module/:action.:suffix');
        $this->assertEquals(':module/:action.:suffix', $this->helper->getViewScriptPathNoControllerSpec());
    }

    public function testGetViewScriptWithDefaults()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $expected   = 'index/test.phtml';
        $this->assertEquals($expected, $this->helper->getViewScript());
    }

    public function testGetViewScriptWithSpecifiedAction()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $expected   = 'index/baz.phtml';
        $this->assertEquals($expected, $this->helper->getViewScript('baz'));
    }

    public function testGetViewScriptWithSpecifiedVars()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $expected   = 'baz/bat.php';
        $this->assertEquals(
            $expected,
            $this->helper->getViewScript(
                null,
                array('controller' => 'baz', 'action' => 'bat', 'suffix' => 'php')
            )
        );
    }

    public function testGetViewScriptWithNoControllerSet()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $this->helper->setNoController();
        $expected   = 'test.phtml';
        $this->assertEquals($expected, $this->helper->getViewScript());
    }

    public function testRenderScript()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->renderScript('index/test.phtml');
        $body = $this->response->getBody();
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderScriptToNamedResponseSegment()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->renderScript('index/test.phtml', 'foo');
        $body = $this->response->getBody('foo');
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderScriptToPreviouslyNamedResponseSegment()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setResponseSegment('foo');
        $this->helper->renderScript('index/test.phtml');
        $body = $this->response->getBody('foo');
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderWithDefaults()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->render();
        $body = $this->response->getBody();
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderToSpecifiedAction()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->render('test');
        $body = $this->response->getBody();
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderWithNoController()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->render(null, null, true);
        $body = $this->response->getBody();
        $this->assertContains('Rendered test.phtml in bar module', $body);
    }

    public function testRenderToNamedSegment()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->render(null, 'foo');
        $body = $this->response->getBody('foo');
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderBySpec()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->renderBySpec('foo', array('controller' => 'test', 'suffix' => 'php'));
        $body = $this->response->getBody();
        $this->assertContains('Rendered test/foo.php', $body);
    }

    public function testRenderBySpecToNamedResponseSegment()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->renderBySpec('foo', array('controller' => 'test', 'suffix' => 'php'), 'foo');
        $body = $this->response->getBody('foo');
        $this->assertContains('Rendered test/foo.php', $body);
    }

    public function testInitDoesNotInitViewWhenNoViewRendererSet()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $this->front->setParam('noViewRenderer', true);
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->assertNull($controller->view);
    }

    public function testPostDispatchDoesNotRenderViewWhenNoViewRendererSet()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $this->front->setParam('noViewRenderer', true);
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->postDispatch();
        $body = $this->response->getBody();
        $this->assertTrue(empty($body));
    }

    public function testRenderNormalizationIsCorrect()
    {
        $this->request->setModuleName('application')
                      ->setControllerName('foo')
                      ->setActionName('myBar');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);
        $scriptName = $this->helper->getViewScript();
        $this->assertEquals('foo/my-bar.phtml', $scriptName);

        $this->request->setModuleName('application')
                      ->setControllerName('foo')
                      ->setActionName('baz__bat');
        $scriptName = $this->helper->getViewScript();
        $this->assertEquals('foo/baz-bat.phtml', $scriptName);

        $this->request->setModuleName('application')
                      ->setControllerName('Foo_Bar')
                      ->setActionName('bar__baz');
        $scriptName = $this->helper->getViewScript();
        $this->assertEquals('foo/bar/bar-baz.phtml', $scriptName);
    }

    public function testGetInflectorGetsDefaultInflectorWhenNoneProvided()
    {
        $inflector = $this->helper->getInflector();
        $this->assertTrue($inflector instanceof Filter\Inflector);
        $rules = $inflector->getRules();
        $this->assertTrue(isset($rules['module']));
        $this->assertTrue(isset($rules['moduleDir']));
        $this->assertTrue(isset($rules['controller']));
        $this->assertTrue(isset($rules['action']));
        $this->assertTrue(isset($rules['suffix']));
    }

    public function testInflectorAccessorsAllowSwappingInflectors()
    {
        $inflector = $this->helper->getInflector();
        $this->assertTrue($inflector instanceof Filter\Inflector);
        $newInflector = new Filter\Inflector();
        $this->helper->setInflector($newInflector);
        $receivedInflector = $this->helper->getInflector();
        $this->assertSame($newInflector, $receivedInflector);
        $this->assertNotSame($newInflector, $inflector);
    }

    public function testCustomInflectorCanUseItsOwnTarget()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);

        $this->helper->view->resolver()->addPath($this->basePath . '/_files/modules/bar/views');

        $inflector = new Filter\Inflector('test.phtml');
        $inflector->addFilterRule(':controller', array('Word\CamelCaseToDash'));
        $this->helper->setInflector($inflector);

        $this->helper->render();
        $body = $this->response->getBody();
        $this->assertContains('Rendered test.phtml in bar module', $body);
    }

    public function testCustomInflectorUsesViewRendererTargetWhenPassedInWithReferenceFlag()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);

        $this->helper->view->resolver()->addPath($this->basePath . '/_files/modules/bar/views');


        $inflector = new Filter\Inflector('test.phtml');
        $inflector->addRules(array(
            ':module'     => array('Word\CamelCaseToDash', 'stringToLower'),
            ':controller' => array('Word\CamelCaseToDash', new \Zend\Filter\Word\UnderscoreToSeparator(DIRECTORY_SEPARATOR), 'StringToLower'),
            ':action'     => array(
                'Word\CamelCaseToDash',
                new Filter\PregReplace('/[^a-z0-9]+/i', '-'),
                'StringToLower'
            ),
        ));
        $this->helper->setInflector($inflector, true);

        $this->helper->render();
        $body = $this->response->getBody();
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testStockInflectorAllowsSubDirectoryViewScripts()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('layout/admin');
        $this->assertEquals('index/layout/admin.phtml', $this->helper->getViewScript());
    }

    /**
     * @group ZF-2443
     */
    public function testStockInflectorWorksWithViewBaseSpec()
    {
        $this->request->setModuleName('bar')  // bar must exist so the ViewRendere doesnt throw an exception
                      ->setControllerName('index')
                      ->setActionName('admin');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);

        $this->helper->setViewBasePathSpec(':moduleDir/:module');
        $this->helper->initView();

        $viewScriptPaths = $this->helper->view->resolver()->getPaths();

        $expectedPathRegex = '#modules/bar/bar/scripts/$#';
        $this->assertRegExp(
            $expectedPathRegex,
            $this->_normalizePath($viewScriptPaths[0])
            );
        $this->assertEquals($this->helper->getViewScript(), 'index/admin.phtml');
    }

    /**
     * @group ZF-2738
     */
    public function testStockInflectorWorksWithDottedRequestParts()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('car.bar')
                      ->setActionName('baz');
        $controller = new \Bar\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);
        $this->helper->setActionController($controller);
        $viewScriptPaths = $this->helper->view->resolver()->getPaths();

        $expectedPathRegex = '#modules/foo/views/scripts/$#';
        $this->assertRegExp(
            $expectedPathRegex,
            $this->_normalizePath($viewScriptPaths[0])
            );
        $this->assertEquals('car-bar/baz.phtml', $this->helper->getViewScript());
    }

    /**
     * Disabled, as not using prefix path autoloading by default any more
     *
     * @group disable
     */
    public function testCorrectViewHelperPathShouldBePropagatedWhenSubControllerInvoked()
    {
        require_once $this->basePath . '/_files/modules/foo/controllers/Admin/IndexController.php';
        $this->request->setModuleName('foo')
                      ->setControllerName('admin_index')
                      ->setActionName('use-helper');
        $controller = new \Foo\Admin\IndexController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);

        $this->helper->render();
        $body = $this->response->getBody();
        $this->assertContains('FooUseHelper invoked', $body, 'Received ' . $body);
    }

    /**
     * Disabled, as not using prefix path autoloading by default any more
     *
     * @group disable
     */
    public function testCorrectViewHelperPathShouldBePropagatedWhenSubControllerInvokedInDefaultModule()
    {
        require_once $this->basePath . '/_files/modules/application/controllers/Admin/HelperController.php';
        $this->request->setControllerName('admin_helper')
                      ->setActionName('render');
        $controller = new \Admin\HelperController($this->request, $this->response, array());
        $controller->setHelperBroker($this->broker);

        $this->helper->render();
        $body = $this->response->getBody();
        $this->assertContains('SampleZfHelper invoked', $body, 'Received ' . $body);
    }

    protected function _normalizePath($path)
    {
        return str_replace(array('/', '\\'), '/', $path);
    }
}

