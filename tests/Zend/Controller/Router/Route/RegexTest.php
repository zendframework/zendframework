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

use Zend\Config\Config,
    Zend\Controller\Router\Route;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class RegexTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticMatch()
    {
        $route = new Route\Regex('users/all');
        $values = $route->match('users/all');

        $this->assertSame(array(), $values);
    }

    public function testStaticUTFMatch()
    {
        $route = new Route\Regex('żółć');
        $values = $route->match('żółć');

        $this->assertSame(array(), $values);
    }

    public function testURLDecode()
    {
        $route = new Route\Regex('żółć');
        $values = $route->match('%C5%BC%C3%B3%C5%82%C4%87');

        $this->assertSame(array(), $values);
    }

    public function testStaticNoMatch()
    {
        $route = new Route\Regex('users/a/martel');
        $values = $route->match('users/a');

        $this->assertSame(false, $values);
    }

    public function testStaticMatchWithDefaults()
    {
        $route = new Route\Regex('users/all', array('controller' => 'ctrl'));
        $values = $route->match('users/all');

        $this->assertSame(1, count($values));
        $this->assertSame('ctrl', $values['controller']);
    }

    public function testRootRoute()
    {
        $route = new Route\Regex('');
        $values = $route->match('/');

        $this->assertSame(array(), $values);
    }

    public function testVariableMatch()
    {
        $route = new Route\Regex('users/(.+)');
        $values = $route->match('users/martel');

        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values[1]);
    }

    public function testDoubleMatch()
    {
        $route = new Route\Regex('users/(user_(\d+).html)');
        $values = $route->match('users/user_1354.html');

        $this->assertSame(2, count($values));
        $this->assertSame('user_1354.html', $values[1]);
        $this->assertSame('1354', $values[2]);
    }

    public function testNegativeMatch()
    {

        $route = new Route\Regex('((?!admin|moderator).+)',
           array('module' => 'index', 'controller' => 'index'),
           array(1 => 'action')
        );

        $values = $route->match('users');

        $this->assertSame(3, count($values));
        $this->assertSame('index', $values['module']);
        $this->assertSame('index', $values['controller']);
        $this->assertSame('users', $values['action']);
    }

    public function testNumericDefault()
    {
        $route = new Route\Regex('users/?(.+)?', array(1 => 'martel'));
        $values = $route->match('users');

        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values[1]);
    }

    public function testVariableMatchWithNumericDefault()
    {
        $route = new Route\Regex('users/?(.+)?', array(1 => 'martel'));
        $values = $route->match('users/vicki');

        $this->assertSame(1, count($values));
        $this->assertSame('vicki', $values[1]);
    }

    public function testNamedVariableMatch()
    {
        $route = new Route\Regex('users/(?P<username>.+)');
        $values = $route->match('users/martel');

        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values[1]);
    }

    public function testMappedVariableMatch()
    {
        $route = new Route\Regex('users/(.+)', null, array(1 => 'username'));
        $values = $route->match('users/martel');

        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }

    public function testMappedVariableWithDefault()
    {
        $route = new Route\Regex('users(?:/(.+))?', array('username' => 'martel'), array(1 => 'username'));
        $values = $route->match('users');

        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }

    public function testMappedVariableWithNamedSubpattern()
    {
        $route = new Route\Regex('users/(?P<name>.+)', null, array(1 => 'username'));
        $values = $route->match('users/martel');

        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }

    public function testOptionalVar()
    {
        $route = new Route\Regex('users/(\w+)/?(?:p/(\d+))?', null, array(1 => 'username', 2 => 'page'));
        $values = $route->match('users/martel/p/1');

        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('1', $values['page']);
    }

    public function testEmptyOptionalVar()
    {
        $route = new Route\Regex('users/(\w+)/?(?:p/(\d+))?', null, array(1 => 'username', 2 => 'page'));
        $values = $route->match('users/martel');

        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }

    public function testMixedMap()
    {
        $route = new Route\Regex('users/(\w+)/?(?:p/(\d+))?', null, array(1 => 'username'));
        $values = $route->match('users/martel/p/1');

        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('1', $values[2]);
    }

    public function testNumericDefaultWithMap()
    {
        $route = new Route\Regex('users/?(.+)?', array(1 => 'martel'), array(1 => 'username'));
        $values = $route->match('users');

        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }

    public function testMixedMapWithDefault()
    {
        $route = new Route\Regex('users/(\w+)/?(?:p/(\d+))?', array(2 => '1'), array(1 => 'username'));
        $values = $route->match('users/martel/p/10');

        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('10', $values[2]);
    }

    public function testMixedMapWithDefaults2()
    {
        $route = new Route\Regex('users/?(\w+)?/?(?:p/(\d+))?', array(2 => '1', 'username' => 'martel'), array(1 => 'username'));
        $values = $route->match('users');

        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('1', $values[2]);
    }

    public function testOptionalVarWithMapAndDefault()
    {
        $route = new Route\Regex('users/(\w+)/?(?:p/(\d+))?', array('page' => '1', 'username' => 'martel'), array(1 => 'username', 2 => 'page'));
        $values = $route->match('users/martel');

        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('1', $values['page']);
    }

    public function testOptionalVarWithMapAndNumericDefault()
    {
        $route = new Route\Regex('users/(\w+)/?(?:p/(\d+))?', array(2 => '1'), array(2 => 'page'));
        $values = $route->match('users/martel');

        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values[1]);
        $this->assertSame('1', $values['page']);
    }

    public function testMappedAndNumericDefault()
    {
        $route = new Route\Regex('users/?(\w+)?', array(1 => 'martel', 'username' => 'vicki'), array(1 => 'username'));
        $values = $route->match('users');

        // Matches both defaults but the one defined last is used

        $this->assertSame(1, count($values));
        $this->assertSame('vicki', $values['username']);
    }

    public function testAssemble()
    {
        $route = new Route\Regex('users/(.+)', null, array(1 => 'username'), 'users/%s');
        $values = $route->match('users/martel');

        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }

    public function testAssembleWithDefault()
    {
        $route = new Route\Regex('users/?(.+)?', array(1 => 'martel'), null, 'users/%s');
        $values = $route->match('users');

        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }

    public function testAssembleWithMappedDefault()
    {
        $route = new Route\Regex('users/?(.+)?', array('username' => 'martel'), array(1 => 'username'), 'users/%s');
        $values = $route->match('users');

        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }

    public function testAssembleWithData()
    {
        $route = new Route\Regex('users/(.+)', null, null, 'users/%s');
        $values = $route->match('users/martel');

        $url = $route->assemble(array(1 => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }

    public function testAssembleWithMappedVariable()
    {
        $route = new Route\Regex('users/(.+)', null, array(1 => 'username'), 'users/%s');
        $values = $route->match('users/martel');

        $url = $route->assemble(array('username' => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }

    public function testAssembleWithMappedVariableAndNumericKey()
    {
        $route = new Route\Regex('users/(.+)', null, array(1 => 'username'), 'users/%s');
        $values = $route->match('users/martel');

        $url = $route->assemble(array(1 => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }

    public function testAssembleWithoutMatch()
    {
        $route = new Route\Regex('users/(.+)', null, null, 'users/%s');

        try {
            $url = $route->assemble();
            $this->fail();
        } catch (\Exception $e) {}
    }

    public function testAssembleWithDefaultWithoutMatch()
    {
        $route = new Route\Regex('users/?(.+)?', array(1 => 'martel'), null, 'users/%s');

        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }

    public function testAssembleWithMappedDefaultWithoutMatch()
    {
        $route = new Route\Regex('users/?(.+)?', array('username' => 'martel'), array(1 => 'username'), 'users/%s');

        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }

    public function testAssembleWithDataWithoutMatch()
    {
        $route = new Route\Regex('users/(.+)', null, null, 'users/%s');

        $url = $route->assemble(array(1 => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }

    public function testAssembleWithMappedVariableWithoutMatch()
    {
        $route = new Route\Regex('users/(.+)', null, array(1 => 'username'), 'users/%s');

        $url = $route->assemble(array('username' => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }


    public function testAssembleZF1332()
    {
        $route = new Route\Regex(
            '(.+)\.([0-9]+)-([0-9]+)\.html',
            array('module' => 'default', 'controller' => 'content.item', 'action' => 'forward'),
            array(1 => 'name', 2 => 'id', 3 => 'class'),
            '%s.%s-%s.html'
         );

        $route->match('uml-explained-composition.72-3.html');

        $url = $route->assemble();

        $this->assertSame('uml-explained-composition.72-3.html', $url);

        $url = $route->assemble(array('name' => 'post_name', 'id' => '12', 'class' => 5));

        $this->assertSame('post_name.12-5.html', $url);
    }

    public function testGetInstance()
    {

        $routeConf = array(
            'route' => 'forum/(\d+)',
            'reverse' => 'forum/%d',
            'defaults' => array(
                'controller' => 'ctrl'
            )
        );
        /* numeric Zend_Config indexes don't work at the moment
            'map' => array(
                '1' => 'forum_id'
            )
        */

        $config = new Config($routeConf);
        $route = Route\Regex::getInstance($config);

        $this->assertInstanceOf('Zend\Controller\Router\Route\Regex', $route);

        $values = $route->match('forum/1');

        $this->assertSame('ctrl', $values['controller']);

    }

    /**
     * @issue ZF-2301
     */
    public function testAssemblyOfRouteWithMergedMatchedParts()
    {
        $route = new Route\Regex(
            'itemlist(?:/(\d+))?',
            array('page' => 1), // Defaults
            array(1 => 'page'), // Parameter map
            'itemlist/%d'
        );

        // make sure defaults work
        $this->assertEquals(array('page' => 1), $route->match('/itemlist/'));

        // make sure default assembly work
        $this->assertEquals('itemlist/1', $route->assemble());

        // make sure the route is parsed correctly
        $this->assertEquals(array('page' => 2), $route->match('/itemlist/2'));

        // check to make sure that the default assembly will return with default 1 (previously defined)
        $this->assertEquals('itemlist/2', $route->assemble());

        // check to make sure that the assembly will return with provided page=3 in the correct place
        $this->assertEquals('itemlist/3', $route->assemble(array('page' => 3)));

        // check to make sure that the assembly can reset a single parameter
        $this->assertEquals('itemlist/1', $route->assemble(array('page' => null)));

    }

    /**
     * @group ZF-4335
     */
    public function testAssembleMethodShouldNotIgnoreEncodeParam()
    {
        $route = new Route\Regex(
            'blog/archive/(.+)-(.+)\.html',
            array(
                'controller' => 'blog',
                'action'     => 'view'
            ),
            array(
                1 => 'name',
                2 => 'description'
            ),
            'blog/archive/%s-%s.html'
        );

        $data = array('string.that&has=some>', 'characters<that;need+to$be*encoded');
        $url = $route->assemble($data, false, true);
        $expectedUrl = 'blog/archive/string.that%26has%3Dsome%3E-characters%3Cthat%3Bneed%2Bto%24be%2Aencoded.html';

        $this->assertEquals($url, $expectedUrl, 'Assembled url isn\'t encoded properly when using the encode parameter.');
    }

    /**
     * Allow using <lang>1</lang> instead of invalid <1>lang</1> for xml router
     * config.
     *
     * <zend-config>
     *     <routes>
     *         <page>
     *             <type>Zend_Controller_Router_Route_Regex</type>
     *             <route>([a-z]{2})/page/(.*)</route>
     *             <defaults>
     *                 <controller>index</controller>
     *                 <action>showpage</action>
     *             </defaults>
     *             <map>
     *                 <lang>1</lang>
     *                 <title>2</title>
     *             </map>
     *             <reverse>%s/page/%s</reverse>
     *         </page>
     *     </routes>
     * </zend-config>
     *
     *
     * @group ZF-7658
     */
    public function testAssembleWithFlippedMappedVariables()
    {
        $route = new Route\Regex(
            '([a-z]{2})/page/(.*)',
            array('controller' => 'index', 'action' => 'showpage'),
            array('lang' => 1, 'title' => 2),
            '%s/page/%s'
        );

        $url = $route->assemble(array(
            'lang'  => 'fi',
            'title' => 'Suomi'
        ), true, true);

        $this->assertEquals($url, 'fi/page/Suomi');
    }
}
