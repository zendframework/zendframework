<?php
// Call Zend_Controller_Action_Helper_ViewRendererTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_ViewRendererTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Controller/Action/Helper/ViewRenderer.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Http.php';
require_once 'Zend/Filter/Inflector.php';
require_once 'Zend/View.php';

require_once dirname(__FILE__) . '/../../_files/modules/foo/controllers/IndexController.php';
require_once dirname(__FILE__) . '/../../_files/modules/bar/controllers/IndexController.php';

/**
 * Test class for Zend_Controller_Action_Helper_ViewRenderer.
 */
class Zend_Controller_Action_Helper_ViewRendererTest extends PHPUnit_Framework_TestCase 
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
     * @var Zend_Controller_Request_Http
     */
    public $request;

    /**
     * Response object
     * @var Zend_Controller_Response_Http
     */
    public $response;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_ViewRendererTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->basePath = realpath(dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 2));
        $this->request  = new Zend_Controller_Request_Http();
        $this->response = new Zend_Controller_Response_Http();
        $this->front    = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->addModuleDirectory($this->basePath . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'modules')
                    ->setRequest($this->request)
                    ->setResponse($this->response);

        $this->helper   = new Zend_Controller_Action_Helper_ViewRenderer();
        Zend_Controller_Action_HelperBroker::addHelper($this->helper);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        Zend_Controller_Action_HelperBroker::resetHelpers();
    }

    public function testConstructorSetsViewWhenPassed()
    {
        $view   = new Zend_View();
        $helper = new Zend_Controller_Action_Helper_ViewRenderer($view);
        $this->assertNotNull(isset($helper->view));
        $this->assertSame($view, $helper->view);
    }

    public function testConstructorSetsOptionsWhenPassed()
    {
        $helper = new Zend_Controller_Action_Helper_ViewRenderer(null, array(
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
        $view = new Zend_View();
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
        $this->assertTrue($this->helper->view instanceof Zend_View);
        $this->assertFalse($this->helper->getNeverRender());
        $this->assertFalse($this->helper->getNoRender());
        $this->assertNull($this->helper->getResponseSegment());
        $this->assertNull($this->helper->getScriptAction());

        $scriptPaths = $this->helper->view->getScriptPaths();
        $this->assertEquals($count, count($scriptPaths), var_export($scriptPaths, 1));
        $this->assertContains($module, $scriptPaths[0]);

        $helperPaths = $this->helper->view->getHelperPaths();
        $test        = ucfirst($module) . '_View_Helper_';
        $found       = false;
        foreach ($helperPaths as $prefix => $paths) {
            if ($test == $prefix) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Did not find auto-initialized helper path: ' . var_export($helperPaths, 1));

        $filterPaths = $this->helper->view->getFilterPaths();
        $test        = ucfirst($module) . '_View_Filter_';
        $found = false;
        foreach ($filterPaths as $prefix => $paths) {
            if ($test == $prefix) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Did not find auto-initialized filter path: ' . var_export($filterPaths, 1));
    }

    public function testInitViewWithDefaults()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');

        $controller = new Foo_IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);
        $this->helper->initView();
        $this->_checkDefaults();
    }

    public function testInitViewWillNotRegisterSameViewPathTwice()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');
        $controller = new Foo_IndexController($this->request, $this->response, array());
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
        $controller = new Foo_IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);

        $this->helper->initView();
        $this->helper->setNoRender();
        $this->helper->postDispatch();
        $this->request->setModuleName('bar')
                      ->setControllerName('index');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);
        $this->helper->initView();
        $this->_checkDefaults('bar', 2);
    }

    public function testPreDispatchWithDefaults()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');
        $controller = new Foo_IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);

        $this->helper->preDispatch();
        $this->_checkDefaults();
    }

    public function testInitViewWithOptions()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('index');
        $controller = new Foo_IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);

        $viewDir = dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 2) . DIRECTORY_SEPARATOR . 'views';
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

        $scriptPaths = $this->helper->view->getScriptPaths();
        $scriptPath  = $scriptPaths[0];
        $this->assertContains($viewDir, $scriptPath);

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
        $controller = new Bar_IndexController($this->request, $this->response, array());
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
        $controller = new Bar_IndexController($this->request, $this->response, array());
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
        $controller = new Bar_IndexController($this->request, $this->response, array());
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
        $controller = new Bar_IndexController($this->request, $this->response, array());
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
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $expected   = 'index/test.phtml';
        $this->assertEquals($expected, $this->helper->getViewScript());
    }

    public function testGetViewScriptWithSpecifiedAction()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $expected   = 'index/baz.phtml';
        $this->assertEquals($expected, $this->helper->getViewScript('baz'));
    }

    public function testGetViewScriptWithSpecifiedVars()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new Bar_IndexController($this->request, $this->response, array());
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
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->setNoController();
        $expected   = 'test.phtml';
        $this->assertEquals($expected, $this->helper->getViewScript());
    }

    public function testRenderScript()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->renderScript('index/test.phtml');
        $body = $this->response->getBody();
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderScriptToNamedResponseSegment()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->renderScript('index/test.phtml', 'foo');
        $body = $this->response->getBody('foo');
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderScriptToPreviouslyNamedResponseSegment()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new Bar_IndexController($this->request, $this->response, array());
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
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->render();
        $body = $this->response->getBody();
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderToSpecifiedAction()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->render('test');
        $body = $this->response->getBody();
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderWithNoController()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->render(null, null, true);
        $body = $this->response->getBody();
        $this->assertContains('Rendered test.phtml in bar module', $body);
    }

    public function testRenderToNamedSegment()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('test');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->render(null, 'foo');
        $body = $this->response->getBody('foo');
        $this->assertContains('Rendered index/test.phtml in bar module', $body);
    }

    public function testRenderBySpec()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->renderBySpec('foo', array('controller' => 'test', 'suffix' => 'php'));
        $body = $this->response->getBody();
        $this->assertContains('Rendered test/foo.php', $body);
    }

    public function testRenderBySpecToNamedResponseSegment()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $controller = new Bar_IndexController($this->request, $this->response, array());
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
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->assertNull($controller->view);
    }

    public function testPostDispatchDoesNotRenderViewWhenNoViewRendererSet()
    {
        $this->request->setModuleName('bar')
                      ->setControllerName('index')
                      ->setActionName('index');
        $this->front->setParam('noViewRenderer', true);
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->postDispatch();
        $body = $this->response->getBody();
        $this->assertTrue(empty($body));
    }

    public function testRenderNormalizationIsCorrect()
    {
        $this->request->setModuleName('default')
                      ->setControllerName('foo')
                      ->setActionName('myBar');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);
        $scriptName = $this->helper->getViewScript();
        $this->assertEquals('foo/my-bar.phtml', $scriptName);

        $this->request->setModuleName('default')
                      ->setControllerName('foo')
                      ->setActionName('baz__bat');
        $scriptName = $this->helper->getViewScript();
        $this->assertEquals('foo/baz-bat.phtml', $scriptName);

        $this->request->setModuleName('default')
                      ->setControllerName('Foo_Bar')
                      ->setActionName('bar__baz');
        $scriptName = $this->helper->getViewScript();
        $this->assertEquals('foo/bar/bar-baz.phtml', $scriptName);
    }

    public function testGetInflectorGetsDefaultInflectorWhenNoneProvided()
    {
        $inflector = $this->helper->getInflector();
        $this->assertTrue($inflector instanceof Zend_Filter_Inflector);
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
        $this->assertTrue($inflector instanceof Zend_Filter_Inflector);
        $newInflector = new Zend_Filter_Inflector();
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
        $controller = new Bar_IndexController($this->request, $this->response, array());

        $this->helper->view->addBasePath($this->basePath . '/_files/modules/bar/views');

        $inflector = new Zend_Filter_Inflector('test.phtml');
        $inflector->addFilterRule(':controller', array('Word_CamelCaseToDash'));
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
        $controller = new Bar_IndexController($this->request, $this->response, array());

        $this->helper->view->addBasePath($this->basePath . '/_files/modules/bar/views');

        require_once 'Zend/Filter/PregReplace.php';
        require_once 'Zend/Filter/Word/UnderscoreToSeparator.php';
        
        $inflector = new Zend_Filter_Inflector('test.phtml');
        $inflector->addRules(array(
            ':module'     => array('Word_CamelCaseToDash', 'stringToLower'),
            ':controller' => array('Word_CamelCaseToDash', new Zend_Filter_Word_UnderscoreToSeparator(DIRECTORY_SEPARATOR), 'StringToLower'),
            ':action'     => array(
                'Word_CamelCaseToDash', 
                new Zend_Filter_PregReplace('/[^a-z0-9]+/i', '-'),
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
     * @see ZF-2443
     */
    public function testStockInflectorWorksWithViewBaseSpec()
    {
        $this->request->setModuleName('bar')  // bar must exist so the ViewRendere doesnt throw an exception
                      ->setControllerName('index')
                      ->setActionName('admin');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);
                      
        $this->helper->setViewBasePathSpec(':moduleDir/:module');
        $this->helper->initView();
        
        $viewScriptPaths = $this->helper->view->getAllPaths(); 

        // we need this until View decides to not use DIRECTORY_SEPARATOR
        $expectedPathRegex = (DIRECTORY_SEPARATOR == '\\') ? '#modules\\\\bar\\\\bar\\\\scripts\\\\$#' : '#modules/bar/bar/scripts/$#';
        $this->assertRegExp($expectedPathRegex, $viewScriptPaths['script'][0]);
        $this->assertEquals($this->helper->getViewScript(), 'index/admin.phtml');
    }
    
    /**
     * @see ZF-2738
     */
    public function testStockInflectorWorksWithDottedRequestParts()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('car.bar')
                      ->setActionName('baz');
        $controller = new Bar_IndexController($this->request, $this->response, array());
        $this->helper->setActionController($controller);
        $viewScriptPaths = $this->helper->view->getAllPaths();

        $expectedPathRegex = (DIRECTORY_SEPARATOR == '\\') ? '#modules\\\\foo\\\\views\\\\scripts\\\\$#' : '#modules/foo/views/scripts/$#';
        $this->assertRegExp($expectedPathRegex, $viewScriptPaths['script'][0]);
        $this->assertEquals('car-bar/baz.phtml', $this->helper->getViewScript());
    }
    
    public function testCorrectViewHelperPathShouldBePropagatedWhenSubControllerInvoked()
    {
        require_once $this->basePath . '/_files/modules/foo/controllers/Admin/IndexController.php';
        $this->request->setModuleName('foo')
                      ->setControllerName('admin_index')
                      ->setActionName('use-helper');
        $controller = new Foo_Admin_IndexController($this->request, $this->response, array());

        $this->helper->render();
        $body = $this->response->getBody();
        $this->assertContains('fooUseHelper invoked', $body, 'Received ' . $body);
    }
    
    public function testCorrectViewHelperPathShouldBePropagatedWhenSubControllerInvokedInDefaultModule()
    {
        require_once $this->basePath . '/_files/modules/default/controllers/Admin/HelperController.php';
        $this->request->setControllerName('admin_helper')
                      ->setActionName('render');
        $controller = new Admin_HelperController($this->request, $this->response, array());

        $this->helper->render();
        $body = $this->response->getBody();
        $this->assertContains('SampleZfHelper invoked', $body, 'Received ' . $body);
    }
}

// Call Zend_Controller_Action_Helper_ViewRendererTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_ViewRendererTest::main") {
    Zend_Controller_Action_Helper_ViewRendererTest::main();
}

