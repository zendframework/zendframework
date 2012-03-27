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
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Rest;

use Zend\Rest,
    Zend\Config\Ini as INIConfig,
    Zend\Controller\Front as FrontController,
    Zend\Controller\Request\HttpTestCase as Request,
    Zend\Controller\Router\Rewrite as RewriteRouter,
    Zend\Controller\Router\Route\Route;

/**
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Rest
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{

    protected $_front;
    protected $_request;
    protected $_dispatcher;

    public function setUp()
    {
        $this->_front = FrontController::getInstance();
        $this->_front->resetInstance();
        $this->_front->setParam('noErrorHandler', true)
        ->setParam('noViewRenderer', true);

        $this->_dispatcher = $this->_front->getDispatcher();

        $this->_dispatcher->setControllerDirectory(array(
            'application' => __DIR__ . DIRECTORY_SEPARATOR .
                '..' . DIRECTORY_SEPARATOR .
                'Controller' . DIRECTORY_SEPARATOR .
                '_files',
            'mod'     => __DIR__ . DIRECTORY_SEPARATOR .
                '..' . DIRECTORY_SEPARATOR .
                'Controller' . DIRECTORY_SEPARATOR .
                '_files' . DIRECTORY_SEPARATOR .
                'Admin',
        ));
    }

    public function test_getVersion()
    {
        $route = new Rest\Route($this->_front);
        $this->assertEquals(2, $route->getVersion());
    }
    
    public function test_getInstance_fromINIConfig()
    {
    	$config = new INIConfig(__DIR__ . '/../Controller/_files/routes.ini', 'testing');
    	$router = new RewriteRouter();
    	$router->addConfig($config, 'routes');
    	$route = $router->getRoute('rest');
    	$this->assertInstanceOf('Zend\\Rest\\Route', $route);
    	$this->assertEquals('object', $route->getDefault('controller'));
    	
    	$request = $this->_buildRequest('GET', '/mod/project');
    	$values = $this->_invokeRouteMatch($request, array(), $route);
    	$this->assertEquals('mod', $values['module']);
    	$this->assertEquals('project', $values['controller']);
    	$this->assertEquals('index', $values['action']);

    	$request = $this->_buildRequest('POST', '/mod/user');
    	$values = $this->_invokeRouteMatch($request, array(), $route);
    	$this->assertEquals('mod', $values['module']);
    	$this->assertEquals('user', $values['controller']);
    	$this->assertEquals('post', $values['action']);
    	
    	$request = $this->_buildRequest('GET', '/other');
    	$values = $this->_invokeRouteMatch($request, array(), $route);
    	$this->assertFalse($values);
    }

    public function test_RESTfulApp_defaults()
    {
        $request = $this->_buildRequest('GET', '/');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('index', $values['controller']);
        $this->assertEquals('index', $values['action']);
    }

    /*
     * @group ZF-7437
     */
    public function test_RESTfulApp_GET_user_defaults()
    {
        $request = $this->_buildRequest('GET', '/user');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
    }

    public function test_RESTfulApp_GET_user_index()
    {
        $request = $this->_buildRequest('GET', '/user/index');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
    }

    public function test_RESTfulApp_GET_user_index_withParams()
    {
        $request = $this->_buildRequest('GET', '/user/index/changedSince/123456789/status/active');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
        $this->assertEquals(123456789, $values['changedSince']);
        $this->assertEquals('active', $values['status']);
    }

    public function test_RESTfulApp_GET_user_index_withQueryParams()
    {
        $request = $this->_buildRequest('GET', '/user/?changedSince=123456789&status=active');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
        $this->assertEquals(123456789, $values['changedSince']);
        $this->assertEquals('active', $values['status']);
    }
    
    public function test_RESTfulApp_GET_project_byIdentifier()
    {
        $request = $this->_buildRequest('GET', '/project/zendframework');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('project', $values['controller']);
        $this->assertEquals('get', $values['action']);
        $this->assertEquals('zendframework', $values['id']);
    }

    public function test_RESTfulApp_GET_project_byIdQueryParam()
    {
        $request = $this->_buildRequest('GET', '/project/?id=zendframework');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('project', $values['controller']);
        $this->assertEquals('get', $values['action']);
        $this->assertEquals('zendframework', $values['id']);
    }
    
    public function test_RESTfulApp_GET_project_byIdentifier_urlencoded()
    {
        $request = $this->_buildRequest('GET', '/project/zend+framework');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('project', $values['controller']);
        $this->assertEquals('get', $values['action']);
        $this->assertEquals('zend framework', $values['id']);
    }
    
    public function test_RESTfulApp_GET_project_edit()
    {
        $request = $this->_buildRequest('GET', '/project/zendframework/edit');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('application', $values['module']);
        $this->assertEquals('project', $values['controller']);
        $this->assertEquals('edit', $values['action']);
        $this->assertEquals('zendframework', $values['id']);
    }

    public function test_RESTfulApp_PUT_user_byIdentifier()
    {
        $request = $this->_buildRequest('PUT', '/mod/user/lcrouch');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('put', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_RESTfulApp_POST_user()
    {
        $request = $this->_buildRequest('POST', '/mod/user');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('post', $values['action']);
    }

    public function test_RESTfulApp_DELETE_user_byIdentifier()
    {
        $request = $this->_buildRequest('DELETE', '/mod/user/lcrouch');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('delete', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_RESTfulApp_POST_user_with_identifier_doesPUT()
    {
        $request = $this->_buildRequest('POST', '/mod/user/lcrouch');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('put', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_RESTfulApp_overload_POST_with_method_param_PUT()
    {
        $request = $this->_buildRequest('POST', '/mod/user');
        $request->setParam('_method', 'PUT');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('put', $values['action']);
    }

    public function test_RESTfulApp_overload_POST_with_http_header_DELETE()
    {
        $request = $this->_buildRequest('POST', '/mod/user/lcrouch');
        $request->setHeader('X-HTTP-Method-Override', 'DELETE');
        $values = $this->_invokeRouteMatch($request);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('delete', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_RESTfulApp_route_chaining()
    {
        $request = $this->_buildRequest('GET', '/api/user/lcrouch');
        $this->_front->setRequest($request);

        $router = $this->_front->getRouter();
        $router->removeDefaultRoutes();

        $nonRESTRoute = new Route('api');
        $RESTRoute = new Rest\Route($this->_front);
        $router->addRoute("api", $nonRESTRoute->addChain($RESTRoute));

        $routedRequest = $router->route($request);

        $this->assertEquals("application", $routedRequest->getParam("module"));
        $this->assertEquals("user", $routedRequest->getParam("controller"));
        $this->assertEquals("get", $routedRequest->getParam("action"));
        $this->assertEquals("lcrouch", $routedRequest->getParam("id"));
    }

    public function test_RESTfulModule_GET_user_index()
    {
        $request = $this->_buildRequest('GET', '/mod/user/index');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
    }

    public function test_RESTfulModule_GET_user()
    {
        $request = $this->_buildRequest('GET', '/mod/user/1234');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('get', $values['action']);
    }

    public function test_RESTfulModule_POST_user()
    {
        $request = $this->_buildRequest('POST', '/mod/user');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('post', $values['action']);
    }

    public function test_RESTfulModule_POST_user_inNonRESTModule_returnsFalse()
    {
        $request = $this->_buildRequest('POST', '/application/user');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertFalse($values);
    }

    public function test_RESTfulModule_PUT_user_byIdentifier()
    {
        $request = $this->_buildRequest('PUT', '/mod/user/lcrouch');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('put', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_RESTfulModule_DELETE_user_byIdentifier()
    {
        $request = $this->_buildRequest('DELETE', '/mod/user/lcrouch');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('delete', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_RESTfulController_GET_user_index()
    {
        $request = $this->_buildRequest('GET', '/mod/user/index');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
    }

    public function test_RESTfulController_GET_default_controller_returns_false()
    {
        $request = $this->_buildRequest('GET', '/mod/index/index');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertFalse($values);
    }

    public function test_RESTfulController_GET_other_index_returns_false()
    {
        $request = $this->_buildRequest('GET', '/mod/project/index');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertFalse($values);
    }

    public function test_RESTfulController_GET_user()
    {
        $request = $this->_buildRequest('GET', '/mod/user/1234');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('get', $values['action']);
    }

    public function test_RESTfulController_POST_user()
    {
        $request = $this->_buildRequest('POST', '/mod/user');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('post', $values['action']);
    }

    public function test_RESTfulController_POST_user_inNonRESTModule_returnsFalse()
    {
        $request = $this->_buildRequest('POST', '/application/user');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertFalse($values);
    }

    public function test_postToNonRESTfulDefaultController_moduleHasAnotherRESTfulController_defaultControllerInURL_returnsFalse()
    {
        $request = $this->_buildRequest('POST', '/mod/index');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);
    
        $this->assertFalse($values);
    }

    public function test_postToNonRESTfulDefaultController_moduleHasAnotherRESTfulController_noDefaultControllerInURL_returnsFalse()
    {
        $request = $this->_buildRequest('POST', '/mod');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);
    
        $this->assertFalse($values);
    }

    public function test_RESTfulController_PUT_user_byIdentifier()
    {
        $request = $this->_buildRequest('PUT', '/mod/user/lcrouch');
        $config = array('mod'=>array('user'));
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('put', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_RESTfulController_DELETE_user_byIdentifier()
    {
        $request = $this->_buildRequest('DELETE', '/mod/user/lcrouch');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('delete', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_assemble_plain_ignores_action()
    {
        $route = new Rest\Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'action'=>'get');
        $url = $route->assemble($params);
        $this->assertEquals('mod/user', $url);
    }

    public function test_assemble_id_after_controller()
    {
        $route = new Rest\Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'id'=>'lcrouch');
        $url = $route->assemble($params);
        $this->assertEquals('mod/user/lcrouch', $url);
    }

    public function test_assemble_index_after_controller_with_params()
    {
        $route = new Rest\Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'index'=>true, 'foo'=>'bar');
        $url = $route->assemble($params);
        $this->assertEquals('mod/user/index/foo/bar', $url);
    }
    
    public function test_assemble_encode_param_values()
    {
        $route = new Rest\Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'index'=>true, 'foo'=>'bar is n!ice');
        $url = $route->assemble($params);
        $this->assertEquals('mod/user/index/foo/bar+is+n%21ice', $url);
    }

    public function test_assemble_does_NOT_encode_param_values()
    {
        $route = new Rest\Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'index'=>true, 'foo'=>'bar is n!ice');
        $url = $route->assemble($params, false, false);
        $this->assertEquals('mod/user/index/foo/bar is n!ice', $url);
    }
    
    private function _buildRequest($method, $uri)
    {
        $request = new Request();
        $request->setMethod($method)->setRequestUri($uri);
        return $request;
    }

    private function _invokeRouteMatch($request, $config = array(), $route = null)
    {
        $this->_front->setRequest($request);
        if ($route == null)
        	$route = new Rest\Route($this->_front, array(), $config);
        $values = $route->match($request);
        return $values;
    }
}
