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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Test helper */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_Rest_Route */
require_once 'Zend/Rest/Route.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/** Zend_Controller_Request_HttpTestCase */
require_once 'Zend/Controller/Request/HttpTestCase.php';

// Call Zend_Rest_RouteTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Rest_RouteTest::main");
}

/**
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Rest
 */
class Zend_Rest_RouteTest extends PHPUnit_Framework_TestCase
{

    protected $_front;
    protected $_request;
    protected $_dispatcher;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Rest_RouteTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->_front = Zend_Controller_Front::getInstance();
        $this->_front->resetInstance();
        $this->_front->setParam('noErrorHandler', true)
        ->setParam('noViewRenderer', true);

        $this->_dispatcher = $this->_front->getDispatcher();

        $this->_dispatcher->setControllerDirectory(array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR .
                '..' . DIRECTORY_SEPARATOR .
                'Controller' . DIRECTORY_SEPARATOR .
                '_files',
            'mod'     => dirname(__FILE__) . DIRECTORY_SEPARATOR .
                '..' . DIRECTORY_SEPARATOR .
                'Controller' . DIRECTORY_SEPARATOR .
                '_files' . DIRECTORY_SEPARATOR .
                'Admin',
        ));
    }

    public function test_getVersion()
    {
        $route = new Zend_Rest_Route($this->_front);
        $this->assertEquals(2, $route->getVersion());
    }

    public function test_RESTfulApp_defaults()
    {
        $request = $this->_buildRequest('GET', '/');
        $values = $this->_invokeRouteMatch($request);

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('default', $values['module']);
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

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('default', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
    }

    public function test_RESTfulApp_GET_user_index()
    {
        $request = $this->_buildRequest('GET', '/user/index');
        $values = $this->_invokeRouteMatch($request);

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('default', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
    }

    public function test_RESTfulApp_GET_user_index_withParams()
    {
        $request = $this->_buildRequest('GET', '/user/index/changedSince/123456789/status/active');
        $values = $this->_invokeRouteMatch($request);

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('default', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('index', $values['action']);
        $this->assertEquals(123456789, $values['changedSince']);
        $this->assertEquals('active', $values['status']);
    }

    public function test_RESTfulApp_GET_project_byIdentifier()
    {
        $request = $this->_buildRequest('GET', '/project/zendframework');
        $values = $this->_invokeRouteMatch($request);

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('default', $values['module']);
        $this->assertEquals('project', $values['controller']);
        $this->assertEquals('get', $values['action']);
        $this->assertEquals('zendframework', $values['id']);
    }

    public function test_RESTfulApp_GET_project_byIdentifier_urlencoded()
    {
        $request = $this->_buildRequest('GET', '/project/zend+framework');
        $values = $this->_invokeRouteMatch($request);

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('default', $values['module']);
        $this->assertEquals('project', $values['controller']);
        $this->assertEquals('get', $values['action']);
        $this->assertEquals('zend framework', $values['id']);
    }
    
    public function test_RESTfulApp_GET_project_edit()
    {
        $request = $this->_buildRequest('GET', '/project/zendframework/edit');
        $values = $this->_invokeRouteMatch($request);

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('default', $values['module']);
        $this->assertEquals('project', $values['controller']);
        $this->assertEquals('edit', $values['action']);
        $this->assertEquals('zendframework', $values['id']);
    }

    public function test_RESTfulApp_PUT_user_byIdentifier()
    {
        $request = $this->_buildRequest('PUT', '/mod/user/lcrouch');
        $values = $this->_invokeRouteMatch($request);

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('post', $values['action']);
    }

    public function test_RESTfulApp_DELETE_user_byIdentifier()
    {
        $request = $this->_buildRequest('DELETE', '/mod/user/lcrouch');
        $values = $this->_invokeRouteMatch($request);

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
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

        $nonRESTRoute = new Zend_Controller_Router_Route('api');
        $RESTRoute = new Zend_Rest_Route($this->_front);
        $router->addRoute("api", $nonRESTRoute->chain($RESTRoute));

        $routedRequest = $router->route($request);

        $this->assertEquals("default", $routedRequest->getParam("module"));
        $this->assertEquals("user", $routedRequest->getParam("controller"));
        $this->assertEquals("get", $routedRequest->getParam("action"));
        $this->assertEquals("lcrouch", $routedRequest->getParam("id"));
    }

    public function test_RESTfulModule_GET_user_index()
    {
        $request = $this->_buildRequest('GET', '/mod/user/index');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('post', $values['action']);
    }

    public function test_RESTfulModule_POST_user_inNonRESTModule_returnsFalse()
    {
        $request = $this->_buildRequest('POST', '/default/user');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertFalse($values);
    }

    public function test_RESTfulModule_PUT_user_byIdentifier()
    {
        $request = $this->_buildRequest('PUT', '/mod/user/lcrouch');
        $config = array('mod');
        $values = $this->_invokeRouteMatch($request, $config);

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('post', $values['action']);
    }

    public function test_RESTfulController_POST_user_inNonRESTModule_returnsFalse()
    {
        $request = $this->_buildRequest('POST', '/default/user');
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

        $this->assertType('array', $values);
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

        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertEquals('user', $values['controller']);
        $this->assertEquals('delete', $values['action']);
        $this->assertEquals('lcrouch', $values['id']);
    }

    public function test_assemble_plain_ignores_action()
    {
        $route = new Zend_Rest_Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'action'=>'get');
        $url = $route->assemble($params);
        $this->assertEquals('mod/user', $url);
    }

    public function test_assemble_id_after_controller()
    {
        $route = new Zend_Rest_Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'id'=>'lcrouch');
        $url = $route->assemble($params);
        $this->assertEquals('mod/user/lcrouch', $url);
    }

    public function test_assemble_index_after_controller_with_params()
    {
        $route = new Zend_Rest_Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'index'=>true, 'foo'=>'bar');
        $url = $route->assemble($params);
        $this->assertEquals('mod/user/index/foo/bar', $url);
    }
    
    public function test_assemble_encode_param_values()
    {
        $route = new Zend_Rest_Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'index'=>true, 'foo'=>'bar is n!ice');
        $url = $route->assemble($params);
        $this->assertEquals('mod/user/index/foo/bar+is+n%21ice', $url);
    }

    public function test_assemble_does_NOT_encode_param_values()
    {
        $route = new Zend_Rest_Route($this->_front, array(), array());
        $params = array('module'=>'mod', 'controller'=>'user', 'index'=>true, 'foo'=>'bar is n!ice');
        $url = $route->assemble($params, false, false);
        $this->assertEquals('mod/user/index/foo/bar is n!ice', $url);
    }
    
    private function _buildRequest($method, $uri)
    {
        $request = new Zend_Controller_Request_HttpTestCase();
        $request->setMethod($method)->setRequestUri($uri);
        return $request;
    }

    private function _invokeRouteMatch($request, $config = array())
    {
        $this->_front->setRequest($request);
        $route = new Zend_Rest_Route($this->_front, array(), $config);
        $values = $route->match($request);
        return $values;
    }
}

// Call Zend_Rest_RouteTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Rest_RouteTest::main") {
    Zend_Rest_RouteTest::main();
}
