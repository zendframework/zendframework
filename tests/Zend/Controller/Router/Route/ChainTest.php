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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Controller_Router_Route_ChainTest::main');
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Config */
require_once 'Zend/Config.php';

/** Zend_Controller_Router_Rewrite */
require_once 'Zend/Controller/Router/Rewrite.php';

/** Zend_Controller_Router_Route_Chain */
require_once 'Zend/Controller/Router/Route/Chain.php';

/** Zend_Controller_Router_Route */
require_once 'Zend/Controller/Router/Route.php';

/** Zend_Controller_Router_Route_Module */
require_once 'Zend/Controller/Router/Route/Module.php';

/** Zend_Controller_Router_Route_Static */
require_once 'Zend/Controller/Router/Route/Static.php';

/** Zend_Controller_Router_Route_Regex */
require_once 'Zend/Controller/Router/Route/Regex.php';

/** Zend_Controller_Router_Route_Hostname */
require_once 'Zend/Controller/Router/Route/Hostname.php';

/** Zend_Controller_Request_Http */
require_once 'Zend/Controller/Request/Http.php';

/** Zend_Uri_Http */
require_once 'Zend/Uri/Http.php';

/** Zend_Config */
require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class Zend_Controller_Router_Route_ChainTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Router_Route_ChainTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
 
    public function testChaining()
    {
        $request = new Zend_Controller_Router_ChainTest_Request('http://localhost/foo/bar');

        $foo = new Zend_Controller_Router_Route('foo');
        $bar = new Zend_Controller_Router_Route('bar');

        $chain = $foo->chain($bar);

        $this->assertType('Zend_Controller_Router_Route_Chain', $chain);
    }

    public function testChainingMatch()
    {
        $chain = new Zend_Controller_Router_Route_Chain();
        $foo = new Zend_Controller_Router_Route_Hostname('www.zend.com', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route_Static('bar', array('bar' => 2));

        $chain->chain($foo)->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/bla');
        $res = $chain->match($request);

        $this->assertFalse($res);
        
        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/bar');
        $res = $chain->match($request);

        $this->assertEquals(1, $res['foo']);
        $this->assertEquals(2, $res['bar']);
    }

    public function testChainingShortcutMatch()
    {
        $foo = new Zend_Controller_Router_Route_Hostname('www.zend.com', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route_Static('bar', array('bar' => 2, 'controller' => 'foo', 'action' => 'bar'));

        $chain = $foo->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/bar');
        $res = $chain->match($request);
        
        $this->assertEquals(1, $res['foo']);
        $this->assertEquals(2, $res['bar']);
    }

    public function testChainingMatchFailure()
    {
        $foo = new Zend_Controller_Router_Route_Hostname('www.zend.com', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route_Static('bar', array('bar' => 2, 'controller' => 'foo', 'action' => 'bar'));

        $chain = $foo->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://nope.zend.com/bar');
        $res = $chain->match($request);

        $this->assertFalse($res);
    }

    public function testChainingVariableOverriding()
    {
        $foo = new Zend_Controller_Router_Route_Hostname('www.zend.com', array('foo' => 1, 'controller' => 'foo', 'module' => 'foo'));
        $bar = new Zend_Controller_Router_Route('bar', array('bar' => 2, 'controller' => 'bar', 'action' => 'bar'));

        $chain = $foo->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/bar');
        $res = $chain->match($request);

        $this->assertEquals('foo', $res['module']);
        $this->assertEquals('bar', $res['controller']);
        $this->assertEquals('bar', $res['action']);
    }

    public function testChainingTooLongPath()
    {
        $foo = new Zend_Controller_Router_Route_Static('foo', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route_Static('bar', array('bar' => 2));

        $chain = $foo->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/foo/bar/baz');
        $res = $chain->match($request);

        $this->assertFalse($res);
    }
    
    public function testChainingRegex()
    {
        $foo = new Zend_Controller_Router_Route_Regex('f..', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route_Regex('b..', array('bar' => 2));

        $chain = $foo->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/foo/bar');
        $res = $chain->match($request);

        $this->assertEquals(1, $res['foo']);
        $this->assertEquals(2, $res['bar']);
    }
    
    public function testChainingModule()
    {
        $foo = new Zend_Controller_Router_Route_Static('foo', array('foo' => 'bar'));
        $bar = new Zend_Controller_Router_Route_Module();

        $chain = $foo->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/foo/bar/baz/var/val');
        $res = $chain->match($request);

        $this->assertEquals('bar', $res['foo']);
        $this->assertEquals('bar', $res['controller']);
        $this->assertEquals('baz', $res['action']);
        $this->assertEquals('val', $res['var']);
    }
    
    public function testVariableOmittingWhenPartial()
    {
        $foo = new Zend_Controller_Router_Route(':foo', array('foo' => 'foo'));
        $bar = new Zend_Controller_Router_Route(':bar', array('bar' => 'bar'));

        $chain = $foo->chain($bar);

        $path = $chain->assemble(array());

        $this->assertEquals('foo/', $path);
    }
    
    public function testVariableUnsettingRoute()
    {
        $foo = new Zend_Controller_Router_Route(':foo');
        $bar = new Zend_Controller_Router_Route_Module(array('controller' => 'ctrl', 'action' => 'act'));

        $chain = $foo->chain($bar);

        $path = $chain->assemble(array('foo' => 'bar', 'baz' => 'bat'));

        $this->assertEquals('bar/ctrl/act/baz/bat', $path);
    }
    
    public function testVariableUnsettingRegex()
    {
        $foo = new Zend_Controller_Router_Route_Regex('([^/]+)', array(), array(1 => 'foo'), '%s');
        $bar = new Zend_Controller_Router_Route_Module(array('controller' => 'ctrl', 'action' => 'act'));

        $chain = $foo->chain($bar);

        $path = $chain->assemble(array('foo' => 'bar', 'baz' => 'bat'));

        $this->assertEquals('bar/ctrl/act/baz/bat', $path);
    }
    
    public function testChainingSeparatorOverriding()
    {
        $foo = new Zend_Controller_Router_Route_Static('foo', array('foo' => 1));
        $bar = new Zend_Controller_Router_Route_Static('bar', array('bar' => 2));
        $baz = new Zend_Controller_Router_Route_Static('baz', array('baz' => 3));

        $chain = $foo->chain($bar, '.');

        $res = $chain->match(new Zend_Controller_Router_ChainTest_Request('http://localhost/foo.bar'));
        
        $this->assertType('array', $res);

        $res = $chain->match(new Zend_Controller_Router_ChainTest_Request('http://localhost/foo/bar'));
        $this->assertEquals(false, $res);

        $chain->chain($baz, ':');

        $res = $chain->match(new Zend_Controller_Router_ChainTest_Request('http://localhost/foo.bar:baz'));
        $this->assertType('array', $res);
    }

    public function testI18nChaining()
    {       
        $lang = new Zend_Controller_Router_Route(':lang', array('lang' => 'en'));
        $profile = new Zend_Controller_Router_Route('user/:id', array('controller' => 'foo', 'action' => 'bar'));

        $chain = $lang->chain($profile);

        $res = $chain->match(new Zend_Controller_Router_ChainTest_Request('http://localhost/en/user/1'));

        $this->assertEquals('en', $res['lang']);
        $this->assertEquals('1', $res['id']);
    }
    
    public function testChainingAssembleWithRoutePlaceholder()
    {
        $chain = new Zend_Controller_Router_Route_Chain();

        $foo = new Zend_Controller_Router_Route_Hostname(':account.zend.com');
        $bar = new Zend_Controller_Router_Route('bar/*');

        $chain->chain($foo)->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://foobar.zend.com/bar');
        $res = $chain->match($request);
        
        $this->assertType('array', $res);
        $this->assertRegexp('#[^a-z0-9]?foobar\.zend\.com/bar/foo/bar#i', $chain->assemble(array('account' => 'foobar', 'foo' => 'bar')));
    }

    public function testChainingAssembleWithStatic()
    {
        $chain = new Zend_Controller_Router_Route_Chain();

        $foo = new Zend_Controller_Router_Route_Hostname('www.zend.com', array('foo' => 'foo'));
        $bar = new Zend_Controller_Router_Route_Static('bar', array('bar' => 'bar'));

        $chain->chain($foo)->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/bar');
        $res = $chain->match($request);
        
        $this->assertType('array', $res);
        $this->assertRegexp('#[^a-z0-9]?www\.zend\.com/bar$#i', $chain->assemble());
    }

    public function testChainingAssembleWithRegex()
    {
        $chain = new Zend_Controller_Router_Route_Chain();

        $foo = new Zend_Controller_Router_Route_Hostname('www.zend.com', array('foo' => 'foo'));
        $bar = new Zend_Controller_Router_Route_Regex('bar', array('bar' => 'bar'), array(), 'bar');

        $chain->chain($foo)->chain($bar);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/bar');
        $res = $chain->match($request);
        
        $this->assertType('array', $res);
        $this->assertRegexp('#[^a-z0-9]?www\.zend\.com/bar$#i', $chain->assemble());
    }
    
    public function testChainingReuse()
    {
        $foo = new Zend_Controller_Router_Route_Hostname('www.zend.com', array('foo' => 'foo'));
        $profile = new Zend_Controller_Router_Route('user/:id', array('controller' => 'prof'));
        $article = new Zend_Controller_Router_Route('article/:id', array('controller' => 'art', 'action' => 'art'));

        $profileChain = $foo->chain($profile);
        $articleChain = $foo->chain($article);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/user/1');
        $res = $profileChain->match($request);
        
        $this->assertType('array', $res);
        $this->assertEquals('prof', $res['controller']);

        $request = new Zend_Controller_Router_ChainTest_Request('http://www.zend.com/article/1');
        $res = $articleChain->match($request);
        
        $this->assertType('array', $res);
        $this->assertEquals('art', $res['controller']);
        $this->assertEquals('art', $res['action']);
    }

    public function testConfigChaining()
    {
        $routes = array(
            
            /** Abstract routes */
        
            'www' => array(
                'type'  => 'Zend_Controller_Router_Route_Hostname',
                'route' => 'www.example.com',
                'abstract' => true
            ),
            'user' => array(
                'type'  => 'Zend_Controller_Router_Route_Hostname',
                'route' => 'user.example.com',
                'abstract' => true
            ),
            'index' => array(
                'type'  => 'Zend_Controller_Router_Route_Static',
                'route' => '',
                'abstract' => true,
                'defaults' => array(
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'index'
                )
            ),
            'imprint' => array(
                'type'  => 'Zend_Controller_Router_Route_Static',
                'route' => 'imprint',
                'abstract' => true,
                'defaults' => array(
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'imprint'
                )
            ),
            'profile' => array(
                'type'  => 'Zend_Controller_Router_Route_Static',
                'route' => 'profile',
                'abstract' => true,
                'defaults' => array(
                    'module'     => 'user',
                    'controller' => 'profile',
                    'action'     => 'index'
                )
            ),
            'profile-edit' => array(
                'type'  => 'Zend_Controller_Router_Route_Static',
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
                'type'  => 'Zend_Controller_Router_Route_Chain',
                'chain' => 'www, index' // or array('www-subdomain', 'index'); / maybe both 
            ),
            'www-imprint' => array(
                'type'  => 'Zend_Controller_Router_Route_Chain',
                'chain' => 'www, imprint'
            ),
            'user-index' => array(
                'type'  => 'Zend_Controller_Router_Route_Chain',
                'chain' => 'user, index'
            ),
            'user-profile' => array(
                'type'  => 'Zend_Controller_Router_Route_Chain',
                'chain' => 'user, profile'
            ),
            'user-profile-edit' => array(
                'type'  => 'Zend_Controller_Router_Route_Chain',
                'chain' => 'user, profile-edit'
            )
        );
        
        $router = new Zend_Controller_Router_Rewrite();
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setDispatcher(new Zend_Controller_Router_RewriteTest_Dispatcher());
        $front->setRequest(new Zend_Controller_Router_RewriteTest_Request());
        $router->setFrontController($front);
        
        $router->addConfig(new Zend_Config($routes));
        
        $request = new Zend_Controller_Router_ChainTest_Request('http://user.example.com/profile');
        $token   = $router->route($request);
        
        $this->assertEquals('user',    $token->getModuleName());
        $this->assertEquals('profile', $token->getControllerName());
        $this->assertEquals('index',   $token->getActionName());
        
        $request = new Zend_Controller_Router_ChainTest_Request('http://foo.example.com/imprint');
        $token   = $router->route($request);
        
        $this->assertEquals('default', $token->getModuleName());
        $this->assertEquals('imprint', $token->getControllerName());
        $this->assertEquals('defact',  $token->getActionName());
    }

    public function testConfigChainingAlternative()
    {        
        $routes = array(
            'www' => array(
                'type'  => 'Zend_Controller_Router_Route_Hostname',
                'route' => 'www.example.com',
                'chains' => array(
                    'index' => array(
                        'type'  => 'Zend_Controller_Router_Route_Static',
                        'route' => '',
                        'defaults' => array(
                            'module'     => 'default',
                            'controller' => 'index',
                            'action'     => 'index'
                        )
                    ),
                    'imprint' => array(
                        'type'  => 'Zend_Controller_Router_Route_Static',
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
                'type'  => 'Zend_Controller_Router_Route_Hostname',
                'route' => 'user.example.com',
                'chains' => array(
                    'profile' => array(
                        'type'  => 'Zend_Controller_Router_Route_Static',
                        'route' => 'profile',
                        'defaults' => array(
                            'module'     => 'user',
                            'controller' => 'profile',
                            'action'     => 'index'
                        ),
                        'chains' => array(
                            'standard' => array(
                                'type'  => 'Zend_Controller_Router_Route_Static',
                                'route' => 'standard2',
                                'defaults' => array(
                                    'mode' => 'standard'
                                )
                            ),
                            'detail' => array(
                                'type'  => 'Zend_Controller_Router_Route_Static',
                                'route' => 'detail',
                                'defaults' => array(
                                    'mode' => 'detail'
                                )
                            )
                        )
                    ),
                    'profile-edit' => array(
                        'type'  => 'Zend_Controller_Router_Route_Static',
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
        $router->addConfig(new Zend_Config($routes));
        
        $request = new Zend_Controller_Router_ChainTest_Request('http://user.example.com/profile/edit');
        $token   = $router->route($request);
        
        $this->assertEquals('user',    $token->getModuleName());
        $this->assertEquals('profile', $token->getControllerName());
        $this->assertEquals('edit',    $token->getActionName());

        $request = new Zend_Controller_Router_ChainTest_Request('http://user.example.com/profile/detail');
        $token   = $router->route($request);

        $this->assertEquals('user',    $token->getModuleName());
        $this->assertEquals('profile', $token->getControllerName());
        $this->assertEquals('index',   $token->getActionName());
        $this->assertEquals('detail',  $token->getParam('mode'));
        
        $request = new Zend_Controller_Router_ChainTest_Request('http://user.example.com/profile');
        $token   = $router->route($request);
    }


    public function testConfigChainingMixed()
    {  
        $routes = array(
            'index' => array(
                'type'  => 'Zend_Controller_Router_Route_Static',
                'route' => '',
                'defaults' => array(
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'index'
                )
            ),
            'www' => array(
                'type'  => 'Zend_Controller_Router_Route_Hostname',
                'route' => 'www.example.com',
                'chains' => array(
                    'index',
                    'imprint' => array(
                        'type'  => 'Zend_Controller_Router_Route_Static',
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
                'type'  => 'Zend_Controller_Router_Route_Hostname',
                'route' => 'user.example.com',
                'chains' => array(
                    'index',
                    'profile' => array(
                        'type'  => 'Zend_Controller_Router_Route_Static',
                        'route' => 'profile',
                        'defaults' => array(
                            'module'     => 'user',
                            'controller' => 'profile',
                            'action'     => 'index'
                        )
                    ),
                    'profile-edit' => array(
                        'type'  => 'Zend_Controller_Router_Route_Static',
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
        
        $router->addConfig(new Zend_Config($routes));
        
        $request = new Zend_Controller_Router_ChainTest_Request('http://user.example.com');
        $token   = $router->route($request);
        
        $this->assertEquals('default',    $token->getModuleName());
        $this->assertEquals('index', $token->getControllerName());
        $this->assertEquals('index',   $token->getActionName());
        
        $this->assertType('Zend_Controller_Router_Route_Chain', $router->getRoute('user-profile'));
        $this->assertType('Zend_Controller_Router_Route_Chain', $router->getRoute('www-imprint'));
        $this->assertType('Zend_Controller_Router_Route_Chain', $router->getRoute('www-index'));
        $this->assertType('Zend_Controller_Router_Route_Chain', $router->getRoute('www-index'));
    }
    
    protected function _getRouter()
    {
        $router = new Zend_Controller_Router_Rewrite();
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setRequest(new Zend_Controller_Router_ChainTest_Request());
        $router->setFrontController($front);
        
        return $router;
    }
}

/**
 * Zend_Controller_Router_ChainTest_Request - request object for router testing
 *
 * @uses Zend_Controller_Request_Interface
 */
class Zend_Controller_Router_ChainTest_Request extends Zend_Controller_Request_Http
{
    protected $_host;
    protected $_port;
    
    public function __construct($uri = null)
    {
        if (null === $uri) {
            $uri = 'http://localhost/foo/bar/baz/2';
        }

        $uri = Zend_Uri_Http::fromString($uri);
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

if (PHPUnit_MAIN_METHOD == "Zend_Controller_Router_Route_ChainTest::main") {
    Zend_Controller_Router_Route_ChainTest::main();
}
