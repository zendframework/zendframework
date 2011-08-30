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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Router\Route;
use Zend\Controller\Router\Route;
use Zend\Config;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{

    protected $_request;
    protected $_dispatcher;
    protected $route;


    public function setUp()
    {
        $front = \Zend\Controller\Front::getInstance();
        $front->resetInstance();
        $front->setParam('noErrorHandler', true)
              ->setParam('noViewRenderer', true);

        $this->_dispatcher = $front->getDispatcher();

        $this->_dispatcher->setControllerDirectory(array(
            'default' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'mod'     => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin',
        ));

        $defaults = array(
            'controller' => 'defctrl',
            'action'     => 'defact',
            'module'     => 'default'
        );

        $this->_request = new \Zend\Controller\Request\Http();
        $front->setRequest($this->_request);

        $this->route = new Route\Module($defaults, $this->_dispatcher, $this->_request);
    }

    public function testModuleMatch()
    {
        $values = $this->route->match('mod');

        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
    }

    public function testModuleAndControllerMatch()
    {
        $values = $this->route->match('mod/con');
        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
    }

    public function testModuleControllerAndActionMatch()
    {
        $values = $this->route->match('mod/con/act');
        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
    }

    public function testModuleControllerActionAndParamsMatch()
    {
        $values = $this->route->match('mod/con/act/var/val/foo');
        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
        $this->assertTrue(isset($values['var']));
        $this->assertEquals('val', $values['var']);
        $this->assertTrue(array_key_exists('foo', $values), var_export($values, 1));
        $this->assertTrue(empty($values['foo']));
    }

    public function testControllerOnlyMatch()
    {
        $values = $this->route->match('con');
        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
    }

    public function testControllerOnlyAndActionMatch()
    {
        $values = $this->route->match('con/act');
        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
    }

    public function testControllerOnlyActionAndParamsMatch()
    {
        $values = $this->route->match('con/act/var/val/foo');
        $this->assertInternalType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
        $this->assertTrue(isset($values['var']));
        $this->assertEquals('val', $values['var']);
        $this->assertTrue(array_key_exists('foo', $values), var_export($values, 1));
        $this->assertTrue(empty($values['foo']));
    }

    public function testModuleMatchWithControlKeysChange()
    {
        $this->_request->setModuleKey('m');
        $this->_request->setControllerKey('c');
        $this->_request->setActionKey('a');

        $this->route = new Route\Module(array(), $this->_dispatcher, $this->_request);

        $values = $this->route->match('mod/ctrl');

        $this->assertInternalType('array', $values);
        $this->assertSame('mod', $values['m']);
        $this->assertSame('ctrl', $values['c']);
        $this->assertSame('index', $values['a']);
    }

    public function testModuleMatchWithLateControlKeysChange()
    {
        $this->_request->setModuleKey('m');
        $this->_request->setControllerKey('c');
        $this->_request->setActionKey('a');

        $values = $this->route->match('mod/ctrl');

        $this->assertInternalType('array', $values);
        $this->assertSame('mod', $values['m'], var_export(array_keys($values), 1));
        $this->assertSame('ctrl', $values['c'], var_export(array_keys($values), 1));
        $this->assertSame('index', $values['a'], var_export(array_keys($values), 1));
    }

    public function testAssembleNoModuleOrController()
    {
        $params = array(
            'action' => 'act',
            'foo'    => 'bar'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('defctrl/act/foo/bar', $url);
    }

    public function testAssembleControllerOnly()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);

        $this->assertEquals('con/act/foo/bar', $url);
    }

    public function testAssembleModuleAndController()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/con/act/foo/bar', $url);
    }

    public function testAssembleNoController()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/defctrl/act/foo/bar', $url);
    }

    public function testAssembleNoAction()
    {
        $params = array(
            'module'     => 'mod',
            'controller' => 'ctrl'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/ctrl', $url);
    }

    public function testAssembleNoActionWithParams()
    {
        $params = array(
            'foo'         => 'bar',
            'module'     => 'mod',
            'controller' => 'ctrl'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/ctrl/defact/foo/bar', $url);
    }

    public function testAssembleNoModuleOrControllerMatched()
    {
        $this->route->match('');

        $params = array(
            'action' => 'act',
            'foo'    => 'bar'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('defctrl/act/foo/bar', $url);
    }

    public function testAssembleControllerOnlyMatched()
    {
        $this->route->match('ctrl');

        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);

        $this->assertEquals('con/act/foo/bar', $url);
    }

    public function testAssembleModuleAndControllerMatched()
    {
        $this->route->match('mod/ctrl');

        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'm'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('m/ctrl/act/foo/bar', $url);
    }

    public function testAssembleNoControllerMatched()
    {
        $this->route->match('mod');

        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/defctrl/act/foo/bar', $url);
    }

    public function testAssembleNoActionMatched()
    {
        $this->route->match('mod/ctrl');

        $params = array(
            'module'     => 'def',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('def/con', $url);
    }

    public function testAssembleWithReset()
    {
        $values = $this->route->match('mod/con/act/sort/name');

        $url = $this->route->assemble(array('action' => 'new'), true);

        $this->assertSame('defctrl/new', $url);
    }

    public function testAssembleWithReset2()
    {
        $values = $this->route->match('mod/con/act/sort/name');

        $url = $this->route->assemble(array('controller' => 'new'), true);

        $this->assertSame('new', $url);
    }

    public function testAssembleWithReset3()
    {
        $values = $this->route->match('mod/con/act/sort/name');

        $url = $this->route->assemble(array('controller' => 'new', 'action' => 'test'), true);

        $this->assertSame('new/test', $url);
    }

    public function testAssembleResetOneVariable()
    {
        $values = $this->route->match('mod/con/act');

        $url = $this->route->assemble(array('action' => null), false);

        $this->assertSame('mod/con', $url);
    }

    public function testAssembleResetOneVariable2()
    {
        $values = $this->route->match('mod/con/act');

        $url = $this->route->assemble(array('controller' => null), false);

        $this->assertSame('mod/defctrl/act', $url);
    }

    public function testAssembleResetOneVariable3()
    {
        $values = $this->route->match('mod/con/act');

        $url = $this->route->assemble(array('module' => null), false);

        $this->assertSame('con/act', $url);
    }

    public function testAssembleDefaultModuleResetZF1415()
    {
        $values = $this->route->match('con/act');

        $url = $this->route->assemble(array('controller' => 'foo', 'action' => 'bar'), true);

        $this->assertSame('foo/bar', $url);
    }

    public function testAssembleDefaultModuleZF1415()
    {
        $values = $this->route->match('con/act');

        $url = $this->route->assemble(array('controller' => 'foo', 'action' => 'bar'), false);

        $this->assertSame('foo/bar', $url);
    }

    public function testAssembleDefaultModuleZF1415_2()
    {
        $values = $this->route->match('default/defctrl/defact');
        $url = $this->route->assemble();
        $this->assertSame('', $url);

        $values = $this->route->match('mod/defctrl/defact');
        $url = $this->route->assemble();
        $this->assertSame('mod', $url);
    }

    public function testGetInstance()
    {

        $routeConf = array(
            'defaults' => array(
                'controller' => 'ctrl'
            )
        );

        $config = new Config\Config($routeConf);
        $route = Route\Module::getInstance($config);

        $this->assertInstanceOf('Zend\Controller\Router\Route\Module', $route);
    }

    public function testEncode()
    {
        $url = $this->route->assemble(array('controller' => 'My Controller'), false, true);
        $this->assertEquals('My+Controller', $url);

        $url = $this->route->assemble(array('controller' => 'My Controller'), false, false);
        $this->assertEquals('My Controller', $url);

        $token = $this->route->match('en/foo/id/My Value');

        $url = $this->route->assemble(array(), false, true);
        $this->assertEquals('en/foo/id/My+Value', $url);

        $url = $this->route->assemble(array('id' => 'My Other Value'), false, true);
        $this->assertEquals('en/foo/id/My+Other+Value', $url);

    }

    public function testArrayValues()
    {
        $url = $this->route->assemble(array('foo' => array('bar', 'baz')));
        $this->assertEquals('defctrl/defact/foo/bar/foo/baz', $url);

        $token = $this->route->match('defctrl/defact/foo/bar/foo/baz');
        $this->assertEquals('bar', $token['foo'][0]);
        $this->assertEquals('baz', $token['foo'][1]);
    }

    public function testGetInstanceMatching()
    {
        $this->route = Route\Module::getInstance(new Config\Config(array()));

        $this->_request->setModuleKey('m');
        $this->_request->setControllerKey('c');
        $this->_request->setActionKey('a');

        $values = $this->route->match('mod/ctrl');

        $this->assertInternalType('array', $values);
        $this->assertSame('mod', $values['m'], var_export(array_keys($values), 1));
        $this->assertSame('ctrl', $values['c'], var_export(array_keys($values), 1));
        $this->assertSame('index', $values['a'], var_export(array_keys($values), 1));
    }

    /**
     * @group ZF-8029
     */
    public function testAssembleShouldUrlEncodeAllParameterNames()
    {
        $params = array(
            'controller' => 'foo',
            'action' => 'bar',
            '"><script>alert(11639)<' => 'script>',
            'module' => 'default',
        );
        $url = $this->route->assemble($params);
        $this->assertNotContains('"><script>alert(11639)<', $url);
    }
}
