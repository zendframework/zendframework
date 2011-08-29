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

use Zend\Config,
    Zend\Controller\Router\Route,
    Zend\Controller\Router,
    Zend\Controller,
    Zend\Uri\UriFactory;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class ChainTest extends \PHPUnit_Framework_TestCase
{


    public function testChaining()
    {
        $request = new Request('http://localhost/foo/bar');

        $foo = new Route\Route('foo');
        $bar = new Route\Route('bar');

        $chain = $foo->addChain($bar);

        $this->assertInstanceOf('Zend\Controller\Router\Route\Chain', $chain);
    }

    public function testChainingMatch()
    {
        $chain = new Route\Chain();
        $foo = new Route\Hostname('www.zend.com', array('foo' => 1));
        $bar = new Route\StaticRoute('bar', array('bar' => 2));

        $chain->addChain($foo)->addChain($bar);

        $request = new Request('http://www.zend.com/bla');
        $res = $chain->match($request);

        $this->assertFalse($res);

        $request = new Request('http://www.zend.com/bar');
        $res = $chain->match($request);

        $this->assertEquals(1, $res['foo']);
        $this->assertEquals(2, $res['bar']);
    }

    public function testChainingShortcutMatch()
    {
        $foo = new Route\Hostname('www.zend.com', array('foo' => 1));
        $bar = new Route\StaticRoute('bar', array('bar' => 2, 'controller' => 'foo', 'action' => 'bar'));

        $chain = $foo->addChain($bar);

        $request = new Request('http://www.zend.com/bar');
        $res = $chain->match($request);

        $this->assertEquals(1, $res['foo']);
        $this->assertEquals(2, $res['bar']);
    }

    public function testChainingMatchFailure()
    {
        $foo = new Route\Hostname('www.zend.com', array('foo' => 1));
        $bar = new Route\StaticRoute('bar', array('bar' => 2, 'controller' => 'foo', 'action' => 'bar'));

        $chain = $foo->addChain($bar);

        $request = new Request('http://nope.zend.com/bar');
        $res = $chain->match($request);

        $this->assertFalse($res);
    }

    public function testChainingVariableOverriding()
    {
        $foo = new Route\Hostname('www.zend.com', array('foo' => 1, 'controller' => 'foo', 'module' => 'foo'));
        $bar = new Route\Route('bar', array('bar' => 2, 'controller' => 'bar', 'action' => 'bar'));

        $chain = $foo->addChain($bar);

        $request = new Request('http://www.zend.com/bar');
        $res = $chain->match($request);

        $this->assertEquals('foo', $res['module']);
        $this->assertEquals('bar', $res['controller']);
        $this->assertEquals('bar', $res['action']);
    }

    public function testChainingTooLongPath()
    {
        $foo = new Route\StaticRoute('foo', array('foo' => 1));
        $bar = new Route\StaticRoute('bar', array('bar' => 2));

        $chain = $foo->addChain($bar);

        $request = new Request('http://www.zend.com/foo/bar/baz');
        $res = $chain->match($request);

        $this->assertFalse($res);
    }

    public function testChainingRegex()
    {
        $foo = new Route\Regex('f..', array('foo' => 1));
        $bar = new Route\Regex('b..', array('bar' => 2));

        $chain = $foo->addChain($bar);

        $request = new Request('http://www.zend.com/foo/bar');
        $res = $chain->match($request);

        $this->assertEquals(1, $res['foo']);
        $this->assertEquals(2, $res['bar']);
    }

    public function testChainingModule()
    {
        $foo = new Route\StaticRoute('foo', array('foo' => 'bar'));
        $bar = new Route\Module();

        $chain = $foo->addChain($bar);

        $request = new Request('http://www.zend.com/foo/bar/baz/var/val');
        $res = $chain->match($request);

        $this->assertEquals('bar', $res['foo']);
        $this->assertEquals('bar', $res['controller']);
        $this->assertEquals('baz', $res['action']);
        $this->assertEquals('val', $res['var']);
    }

    public function testVariableOmittingWhenPartial()
    {
        $foo = new Route\Route(':foo', array('foo' => 'foo'));
        $bar = new Route\Route(':bar', array('bar' => 'bar'));

        $chain = $foo->addChain($bar);

        $path = $chain->assemble(array());

        $this->assertEquals('foo/', $path);
    }

    public function testVariableUnsettingRoute()
    {
        $foo = new Route\Route(':foo');
        $bar = new Route\Module(array('controller' => 'ctrl', 'action' => 'act'));

        $chain = $foo->addChain($bar);

        $path = $chain->assemble(array('foo' => 'bar', 'baz' => 'bat'));

        $this->assertEquals('bar/ctrl/act/baz/bat', $path);
    }

    public function testVariableUnsettingRegex()
    {
        $foo = new Route\Regex('([^/]+)', array(), array(1 => 'foo'), '%s');
        $bar = new Route\Module(array('controller' => 'ctrl', 'action' => 'act'));

        $chain = $foo->addChain($bar);

        $path = $chain->assemble(array('foo' => 'bar', 'baz' => 'bat'));

        $this->assertEquals('bar/ctrl/act/baz/bat', $path);
    }

    public function testChainingSeparatorOverriding()
    {
        $foo = new Route\StaticRoute('foo', array('foo' => 1));
        $bar = new Route\StaticRoute('bar', array('bar' => 2));
        $baz = new Route\StaticRoute('baz', array('baz' => 3));

        $chain = $foo->addChain($bar, '.');

        $res = $chain->match(new Request('http://localhost/foo.bar'));

        $this->assertInternalType('array', $res);

        $res = $chain->match(new Request('http://localhost/foo/bar'));
        $this->assertEquals(false, $res);

        $chain->addChain($baz, ':');

        $res = $chain->match(new Request('http://localhost/foo.bar:baz'));
        $this->assertInternalType('array', $res);
    }

    public function testI18nChaining()
    {
        $lang = new Route\Route(':lang', array('lang' => 'en'));
        $profile = new Route\Route('user/:id', array('controller' => 'foo', 'action' => 'bar'));

        $chain = $lang->addChain($profile);

        $res = $chain->match(new Request('http://localhost/en/user/1'));

        $this->assertEquals('en', $res['lang']);
        $this->assertEquals('1', $res['id']);
    }

    public function testChainingAssembleWithRoutePlaceholder()
    {
        $chain = new Route\Chain();

        $foo = new Route\Hostname(':account.zend.com');
        $bar = new Route\Route('bar/*');

        $chain->addChain($foo)->addChain($bar);

        $request = new Request('http://foobar.zend.com/bar');
        $res = $chain->match($request);

        $this->assertInternalType('array', $res);
        $this->assertRegexp('#[^a-z0-9]?foobar\.zend\.com/bar/foo/bar#i', $chain->assemble(array('account' => 'foobar', 'foo' => 'bar')));
    }

    public function testChainingAssembleWithStatic()
    {
        $chain = new Route\Chain();

        $foo = new Route\Hostname('www.zend.com', array('foo' => 'foo'));
        $bar = new Route\StaticRoute('bar', array('bar' => 'bar'));

        $chain->addChain($foo)->addChain($bar);

        $request = new Request('http://www.zend.com/bar');
        $res = $chain->match($request);

        $this->assertInternalType('array', $res);
        $this->assertRegexp('#[^a-z0-9]?www\.zend\.com/bar$#i', $chain->assemble());
    }

    public function testChainingAssembleWithRegex()
    {
        $chain = new Route\Chain();

        $foo = new Route\Hostname('www.zend.com', array('foo' => 'foo'));
        $bar = new Route\Regex('bar', array('bar' => 'bar'), array(), 'bar');

        $chain->addChain($foo)->addChain($bar);

        $request = new Request('http://www.zend.com/bar');
        $res = $chain->match($request);

        $this->assertInternalType('array', $res);
        $this->assertRegexp('#[^a-z0-9]?www\.zend\.com/bar$#i', $chain->assemble());
    }

    public function testChainingReuse()
    {
        $foo = new Route\Hostname('www.zend.com', array('foo' => 'foo'));
        $profile = new Route\Route('user/:id', array('controller' => 'prof'));
        $article = new Route\Route('article/:id', array('controller' => 'art', 'action' => 'art'));

        $profileChain = $foo->addChain($profile);
        $articleChain = $foo->addChain($article);

        $request = new Request('http://www.zend.com/user/1');
        $res = $profileChain->match($request);

        $this->assertInternalType('array', $res);
        $this->assertEquals('prof', $res['controller']);

        $request = new Request('http://www.zend.com/article/1');
        $res = $articleChain->match($request);

        $this->assertInternalType('array', $res);
        $this->assertEquals('art', $res['controller']);
        $this->assertEquals('art', $res['action']);
    }

    public function testConfigChaining()
    {
        $routes = array(

            /** Abstract routes */

            'www' => array(
                'type'  => 'Zend\Controller\Router\Route\Hostname',
                'route' => 'www.example.com',
                'abstract' => true
            ),
            'user' => array(
                'type'  => 'Zend\Controller\Router\Route\Hostname',
                'route' => 'user.example.com',
                'abstract' => true
            ),
            'index' => array(
                'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                'route' => '',
                'abstract' => true,
                'defaults' => array(
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'index'
                )
            ),
            'imprint' => array(
                'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                'route' => 'imprint',
                'abstract' => true,
                'defaults' => array(
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'imprint'
                )
            ),
            'profile' => array(
                'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                'route' => 'profile',
                'abstract' => true,
                'defaults' => array(
                    'module'     => 'user',
                    'controller' => 'profile',
                    'action'     => 'index'
                )
            ),
            'profile-edit' => array(
                'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                'route' => 'profile/edit',
                'abstract' => true,
                'defaults' => array(
                    'module'     => 'user',
                    'controller' => 'profile',
                    'action'     => 'edit'
                )
            ),

            /** Chains */

            'www-index' => array(
                'type'  => 'Zend\Controller\Router\Route\Chain',
                'chain' => 'www, index' // or array('www-subdomain', 'index'); / maybe both
            ),
            'www-imprint' => array(
                'type'  => 'Zend\Controller\Router\Route\Chain',
                'chain' => 'www, imprint'
            ),
            'user-index' => array(
                'type'  => 'Zend\Controller\Router\Route\Chain',
                'chain' => 'user, index'
            ),
            'user-profile' => array(
                'type'  => 'Zend\Controller\Router\Route\Chain',
                'chain' => 'user, profile'
            ),
            'user-profile-edit' => array(
                'type'  => 'Zend\Controller\Router\Route\Chain',
                'chain' => 'user, profile-edit'
            )
        );

        $router = new Router\Rewrite();
        $front = Controller\Front::getInstance();
        $front->resetInstance();
        $front->setDispatcher(new Dispatcher());
        $front->setRequest(new Request());
        $router->setFrontController($front);

        $router->addConfig(new Config\Config($routes));

        $request = new Request('http://user.example.com/profile');
        $token   = $router->route($request);

        $this->assertEquals('user',    $token->getModuleName());
        $this->assertEquals('profile', $token->getControllerName());
        $this->assertEquals('index',   $token->getActionName());

        $request = new Request('http://foo.example.com/imprint');
        $token   = $router->route($request);

        $this->assertEquals('application', $token->getModuleName());
        $this->assertEquals('imprint', $token->getControllerName());
        $this->assertEquals('defact',  $token->getActionName());
    }

    public function testConfigChainingAlternative()
    {
        $routes = array(
            'www' => array(
                'type'  => 'Zend\Controller\Router\Route\Hostname',
                'route' => 'www.example.com',
                'chains' => array(
                    'index' => array(
                        'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                        'route' => '',
                        'defaults' => array(
                            'module'     => 'default',
                            'controller' => 'index',
                            'action'     => 'index'
                        )
                    ),
                    'imprint' => array(
                        'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                        'route' => 'imprint',
                        'defaults' => array(
                            'module'     => 'default',
                            'controller' => 'index',
                            'action'     => 'imprint'
                        )
                    ),
                )
            ),
            'user' => array(
                'type'  => 'Zend\Controller\Router\Route\Hostname',
                'route' => 'user.example.com',
                'chains' => array(
                    'profile' => array(
                        'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                        'route' => 'profile',
                        'defaults' => array(
                            'module'     => 'user',
                            'controller' => 'profile',
                            'action'     => 'index'
                        ),
                        'chains' => array(
                            'standard' => array(
                                'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                                'route' => 'standard2',
                                'defaults' => array(
                                    'mode' => 'standard'
                                )
                            ),
                            'detail' => array(
                                'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                                'route' => 'detail',
                                'defaults' => array(
                                    'mode' => 'detail'
                                )
                            )
                        )
                    ),
                    'profile-edit' => array(
                        'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                        'route' => 'profile/edit',
                        'defaults' => array(
                            'module'     => 'user',
                            'controller' => 'profile',
                            'action'     => 'edit'
                        )
                    ),
                )
            ),
        );

        $router = $this->_getRouter();
        $router->addConfig(new Config\Config($routes));

        $request = new Request('http://user.example.com/profile/edit');
        $token   = $router->route($request);

        $this->assertEquals('user',    $token->getModuleName());
        $this->assertEquals('profile', $token->getControllerName());
        $this->assertEquals('edit',    $token->getActionName());

        $request = new Request('http://user.example.com/profile/detail');
        $token   = $router->route($request);

        $this->assertEquals('user',    $token->getModuleName());
        $this->assertEquals('profile', $token->getControllerName());
        $this->assertEquals('index',   $token->getActionName());
        $this->assertEquals('detail',  $token->getParam('mode'));

        $request = new Request('http://user.example.com/profile');
        $token   = $router->route($request);
    }


    public function testConfigChainingMixed()
    {
        $routes = array(
            'index' => array(
                'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                'route' => '',
                'defaults' => array(
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'index'
                )
            ),
            'www' => array(
                'type'  => 'Zend\Controller\Router\Route\Hostname',
                'route' => 'www.example.com',
                'chains' => array(
                    'index',
                    'imprint' => array(
                        'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                        'route' => 'imprint',
                        'defaults' => array(
                            'module'     => 'default',
                            'controller' => 'index',
                            'action'     => 'imprint'
                        )
                    ),
                )
            ),
            'user' => array(
                'type'  => 'Zend\Controller\Router\Route\Hostname',
                'route' => 'user.example.com',
                'chains' => array(
                    'index',
                    'profile' => array(
                        'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                        'route' => 'profile',
                        'defaults' => array(
                            'module'     => 'user',
                            'controller' => 'profile',
                            'action'     => 'index'
                        )
                    ),
                    'profile-edit' => array(
                        'type'  => 'Zend\Controller\Router\Route\StaticRoute',
                        'route' => 'profile/edit',
                        'defaults' => array(
                            'module'     => 'user',
                            'controller' => 'profile',
                            'action'     => 'edit'
                        )
                    ),
                )
            ),
        );

        $router = $this->_getRouter();

        $router->addConfig(new Config\Config($routes));

        $request = new Request('http://user.example.com');
        $token   = $router->route($request);

        $this->assertEquals('default',    $token->getModuleName());
        $this->assertEquals('index', $token->getControllerName());
        $this->assertEquals('index',   $token->getActionName());

        $this->assertInstanceOf('Zend\Controller\Router\Route\Chain', $router->getRoute('user-profile'));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Chain', $router->getRoute('www-imprint'));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Chain', $router->getRoute('www-index'));
        $this->assertInstanceOf('Zend\Controller\Router\Route\Chain', $router->getRoute('www-index'));
    }

    public function testChainingWorksWithWildcardAndNoParameters()
    {
        $foo = new Route\Hostname('www.zend.com', array('module' => 'simple', 'controller' => 'bar', 'action' => 'bar'));
        $bar = new Route\Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index'));

        $chain = $foo->addChain($bar);

        $request = new Request('http://www.zend.com/foo/bar/');
        $res = $chain->match($request);

        $this->assertEquals('simple', $res['module']);
        $this->assertEquals('foo', $res['controller']);
        $this->assertEquals('bar', $res['action']);
    }

    public function testChainingWorksWithWildcardAndOneParameter()
    {
        $foo = new Route\Hostname('www.zend.com', array('module' => 'simple', 'controller' => 'foo', 'action' => 'bar'));
        $bar = new Route\Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index'));

        $chain = $foo->addChain($bar);

        $request = new Request('http://www.zend.com/foo/bar/id/12');
        $res = $chain->match($request);

        $this->assertEquals('simple', $res['module']);
        $this->assertEquals('foo', $res['controller']);
        $this->assertEquals('bar', $res['action']);
    }
    
    protected function _getRouter()
    {
        $router = new Router\Rewrite();
        $front = Controller\Front::getInstance();
        $front->resetInstance();
        $front->setRequest(new Request());
        $router->setFrontController($front);

        return $router;
    }
}

/**
 * Zend_Controller_RouterTest_Dispatcher
 */
class Dispatcher extends \Zend\Controller\Dispatcher\Standard
{
    public function getDefaultControllerName()
    {
        return 'defctrl';
    }

    public function getDefaultAction()
    {
        return 'defact';
    }
}

/**
 * Zend_Controller_Router_ChainTest_Request - request object for router testing
 *
 * @uses Zend_Controller_Request_Interface
 */
class Request extends \Zend\Controller\Request\Http
{
    protected $_host;
    protected $_port;

    public function __construct($uri = null)
    {
        if (null === $uri) {
            $uri = 'http://localhost/foo/bar/baz/2';
        }

        $uri = UriFactory::factory($uri, 'http');
        $this->_host = $uri->getHost();
        $this->_port = $uri->getPort();

        parent::__construct($uri);
    }

    public function getHttpHost() {
        $return = $this->_host;
        if ($this->_port)  $return .= ':' . $this->_port;
        return $return;
    }
}
