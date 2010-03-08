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
 * @version    $Id$
 */

/**
 * Test helper
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Controller_Router_RouteTest::main');
}

/** @see Zend_Controller_Request_Http */

/** @see Zend_Controller_Router_Route */

/** @see Zend_Translate */

/** @see Zend_Registry */

/** PHPUnit test case */

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class Zend_Controller_Router_RouteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Server backup
     *
     * @var array
     */
    protected $_server = array();

    /**
     * Setup test
     *
     * @return void
     */
    public function setUp()
    {
        // Backup server array
        $this->_server = $_SERVER;

        // Clean host env
        unset($_SERVER['HTTP_HOST'],
            $_SERVER['HTTPS'], $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']);

       // Set translator
       $translator = new Zend_Translate('array', array('foo' => 'en_foo', 'bar' => 'en_bar'), 'en');
       $translator->addTranslation(array('foo' => 'de_foo', 'bar' => 'de_bar'), 'de');
       $translator->setLocale('en');

       Zend_Registry::set('Zend_Translate', $translator);
    }

    /**
     * Clean
     *
     * @return void
     */
    public function tearDown()
    {
        // Restore server array
        $_SERVER = $this->_server;

        // Remove translator and locale
        Zend_Registry::set('Zend_Translate', null);
        Zend_Registry::set('Zend_Locale', null);
        Zend_Controller_Router_Route::setDefaultTranslator(null);
        Zend_Controller_Router_Route::setDefaultLocale(null);
    }

    public function testStaticMatch()
    {
        $route = new Zend_Controller_Router_Route('users/all');
        $values = $route->match('users/all');

        $this->assertSame(array(), $values);
    }

    public function testStaticUTFMatch()
    {
        $route = new Zend_Controller_Router_Route('żółć');
        $values = $route->match('żółć');

        $this->assertSame(array(), $values);
    }

    public function testURLDecode()
    {
        $route = new Zend_Controller_Router_Route('żółć');
        $values = $route->match('%C5%BC%C3%B3%C5%82%C4%87');

        $this->assertSame(array(), $values);
    }

    public function testStaticPathShorterThanParts()
    {
        $route = new Zend_Controller_Router_Route('users/a/martel');
        $values = $route->match('users/a');

        $this->assertSame(false, $values);
    }

    public function testStaticPathLongerThanParts()
    {
        $route = new Zend_Controller_Router_Route('users/a');
        $values = $route->match('users/a/martel');

        $this->assertEquals(false, $values);
    }

    public function testStaticMatchWithDefaults()
    {
        $route = new Zend_Controller_Router_Route('users/all', array('controller' => 'ctrl'));
        $values = $route->match('users/all');

        $this->assertEquals('ctrl', $values['controller']);
    }

    public function testNotMatched()
    {
        $route = new Zend_Controller_Router_Route('users/all');
        $values = $route->match('users/martel');

        $this->assertEquals(false, $values);
    }

    public function testNotMatchedWithVariablesAndDefaults()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action', array('controller' => 'index', 'action' => 'index'));
        $values = $route->match('archive/action/bogus');

        $this->assertEquals(false, $values);
    }


    public function testNotMatchedWithVariablesAndStatic()
    {
        $route = new Zend_Controller_Router_Route('archive/:year/:month');
        $values = $route->match('ctrl/act/2000');

        $this->assertEquals(false, $values);
    }

    public function testStaticMatchWithWildcard()
    {
        $route = new Zend_Controller_Router_Route('news/view/*', array('controller' => 'news', 'action' => 'view'));
        $values = $route->match('news/view/show/all/year/2000/empty');

        $this->assertEquals('news', $values['controller']);
        $this->assertEquals('view', $values['action']);
        $this->assertEquals('all', $values['show']);
        $this->assertEquals('2000', $values['year']);
        $this->assertEquals(null, $values['empty']);
    }

    public function testWildcardWithUTF()
    {
        $route = new Zend_Controller_Router_Route('news/*', array('controller' => 'news', 'action' => 'view'));
        $values = $route->match('news/klucz/wartość/wskaźnik/wartość');

        $this->assertEquals('news', $values['controller']);
        $this->assertEquals('view', $values['action']);
        $this->assertEquals('wartość', $values['klucz']);
        $this->assertEquals('wartość', $values['wskaźnik']);
    }

    public function testWildcardURLDecode()
    {
        $route = new Zend_Controller_Router_Route('news/*', array('controller' => 'news', 'action' => 'view'));
        $values = $route->match('news/wska%C5%BAnik/warto%C5%9B%C4%87');

        $this->assertEquals('news', $values['controller']);
        $this->assertEquals('view', $values['action']);
        $this->assertEquals('wartość', $values['wskaźnik']);
    }

    public function testVariableValues()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year');
        $values = $route->match('ctrl/act/2000');

        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
        $this->assertEquals('2000', $values['year']);
    }

    public function testVariableUTFValues()
    {
        $route = new Zend_Controller_Router_Route('test/:param');
        $values = $route->match('test/aä');

        $this->assertEquals('aä', $values['param']);
    }

    public function testOneVariableValue()
    {
        $route = new Zend_Controller_Router_Route(':action', array('controller' => 'ctrl', 'action' => 'action'));
        $values = $route->match('act');

        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
    }

    public function testVariablesWithDefault()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'));
        $values = $route->match('ctrl/act');

        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
        $this->assertEquals('2006', $values['year']);
    }

    public function testVariablesWithNullDefault() // Kevin McArthur
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => null));
        $values = $route->match('ctrl/act');

        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
        $this->assertNull($values['year']);
    }

    public function testVariablesWithDefaultAndValue()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'));
        $values = $route->match('ctrl/act/2000');

        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
        $this->assertEquals('2000', $values['year']);
    }

    public function testVariablesWithRequirementAndValue()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year', null, array('year' => '\d+'));
        $values = $route->match('ctrl/act/2000');

        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
        $this->assertEquals('2000', $values['year']);
    }

    public function testVariablesWithRequirementAndIncorrectValue()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year', null, array('year' => '\d+'));
        $values = $route->match('ctrl/act/2000t');

        $this->assertEquals(false, $values);
    }

    public function testVariablesWithDefaultAndRequirement()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'), array('year' => '\d+'));
        $values = $route->match('ctrl/act/2000');

        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
        $this->assertEquals('2000', $values['year']);
    }

    public function testVariablesWithDefaultAndRequirementAndIncorrectValue()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'), array('year' => '\d+'));
        $values = $route->match('ctrl/act/2000t');

        $this->assertEquals(false, $values);
    }

    public function testVariablesWithDefaultAndRequirementAndWithoutValue()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => '2006'), array('year' => '\d+'));
        $values = $route->match('ctrl/act');

        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
        $this->assertEquals('2006', $values['year']);
    }

    public function testVariablesWithWildcardAndNumericKey()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/:next/*');
        $values = $route->match('c/a/next/2000/show/all/sort/name');

        $this->assertEquals('c', $values['controller']);
        $this->assertEquals('a', $values['action']);
        $this->assertEquals('next', $values['next']);
        $this->assertTrue(array_key_exists('2000', $values));
    }

    public function testRootRoute()
    {
        $route = new Zend_Controller_Router_Route('/');
        $values = $route->match('');

        $this->assertEquals(array(), $values);
    }

    public function testAssemble()
    {
        $route = new Zend_Controller_Router_Route('authors/:name');
        $url = $route->assemble(array('name' => 'martel'));

        $this->assertEquals('authors/martel', $url);
    }

    public function testAssembleWithoutValue()
    {
        $route = new Zend_Controller_Router_Route('authors/:name');
        try {
            $url = $route->assemble();
        } catch (Exception $e) {
            return true;
        }

        $this->fail();
    }

    public function testAssembleWithDefault()
    {
        $route = new Zend_Controller_Router_Route('authors/:name', array('name' => 'martel'));
        $url = $route->assemble();

        $this->assertEquals('authors', $url);
    }

    public function testAssembleWithDefaultAndValue()
    {
        $route = new Zend_Controller_Router_Route('authors/:name', array('name' => 'martel'));
        $url = $route->assemble(array('name' => 'mike'));

        $this->assertEquals('authors/mike', $url);
    }

    public function testAssembleWithWildcardMap()
    {
        $route = new Zend_Controller_Router_Route('authors/:name/*');
        $url = $route->assemble(array('name' => 'martel'));

        $this->assertEquals('authors/martel', $url);
    }

    public function testAssembleWithReset()
    {
        $route = new Zend_Controller_Router_Route('archive/:year/*', array('controller' => 'archive', 'action' => 'show'));
        $values = $route->match('archive/2006/show/all/sort/name');

        $url = $route->assemble(array('year' => '2005'), true);

        $this->assertEquals('archive/2005', $url);
    }

    public function testAssembleWithReset2()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'archive', 'action' => 'show'));
        $values = $route->match('users/list');

        $url = $route->assemble(array(), true);

        $this->assertEquals('', $url);
    }

    public function testAssembleWithReset3()
    {
        $route = new Zend_Controller_Router_Route('archive/:year/*', array('controller' => 'archive', 'action' => 'show', 'year' => 2005));
        $values = $route->match('archive/2006/show/all/sort/name');

        $url = $route->assemble(array(), true);

        $this->assertEquals('archive', $url);
    }

    public function testAssembleWithReset4()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'archive', 'action' => 'show'));
        $values = $route->match('users/list');

        $url = $route->assemble(array('action' => 'display'), true);

        $this->assertEquals('archive/display', $url);
    }

    public function testAssembleWithReset5()
    {
        $route = new Zend_Controller_Router_Route('*', array('controller' => 'index', 'action' => 'index'));
        $values = $route->match('key1/value1/key2/value2');

        $url = $route->assemble(array('key1' => 'newvalue'), true);

        $this->assertEquals('key1/newvalue', $url);
    }

    public function testAssembleWithWildcardAndAdditionalParameters()
    {
        $route = new Zend_Controller_Router_Route('authors/:name/*');
        $url = $route->assemble(array('name' => 'martel', 'var' => 'value'));

        $this->assertEquals('authors/martel/var/value', $url);
    }

    public function testAssembleWithUrlVariablesReuse()
    {
        $route = new Zend_Controller_Router_Route('archives/:year/:month');
        $values = $route->match('archives/2006/07');
        $this->assertType('array', $values);

        $url = $route->assemble(array('month' => '03'));
        $this->assertEquals('archives/2006/03', $url);
    }

    /**
     * @group ZF-7917
     */
    public function testAssembleWithGivenDataEqualsDefaults()
    {
        $route = new Zend_Controller_Router_Route('index/*', array(
            'module'     => 'default',
            'controller' => 'index',
            'action'     => 'index'
        ));

        $this->assertEquals('index', $route->assemble(array(
            'module'     => 'default',
            'controller' => 'index',
            'action'     => 'index'
        )));
    }

    public function testWildcardUrlVariablesOverwriting()
    {
        $route = new Zend_Controller_Router_Route('archives/:year/:month/*', array('controller' => 'archive'));
        $values = $route->match('archives/2006/07/controller/test/year/10000/sort/author');
        $this->assertType('array', $values);

        $this->assertEquals('archive', $values['controller']);
        $this->assertEquals('2006', $values['year']);
        $this->assertEquals('07', $values['month']);
        $this->assertEquals('author', $values['sort']);
    }

    public function testGetDefaults()
    {
        $route = new Zend_Controller_Router_Route('users/all',
                    array('controller' => 'ctrl', 'action' => 'act'));

        $values = $route->getDefaults();

        $this->assertType('array', $values);
        $this->assertEquals('ctrl', $values['controller']);
        $this->assertEquals('act', $values['action']);
    }

    public function testGetDefault()
    {
        $route = new Zend_Controller_Router_Route('users/all',
                    array('controller' => 'ctrl', 'action' => 'act'));

        $this->assertEquals('ctrl', $route->getDefault('controller'));
        $this->assertEquals(null, $route->getDefault('bogus'));
    }

    public function testGetInstance()
    {

        $routeConf = array(
            'route' => 'users/all',
            'defaults' => array(
                'controller' => 'ctrl'
            )
        );

        $config = new Zend_Config($routeConf);
        $route = Zend_Controller_Router_Route::getInstance($config);

        $this->assertType('Zend_Controller_Router_Route', $route);

        $values = $route->match('users/all');

        $this->assertEquals('ctrl', $values['controller']);

    }

    public function testAssembleResetDefaults()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index'));

        $values = $route->match('news/view/id/3');

        $url = $route->assemble(array('controller' => null));
        $this->assertEquals('index/view/id/3', $url);

        $url = $route->assemble(array('action' => null));
        $this->assertEquals('news/index/id/3', $url);

        $url = $route->assemble(array('action' => null, 'id' => null));
        $this->assertEquals('news', $url);
    }

    public function testAssembleWithRemovedDefaults() // Test for ZF-1197
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index'));

        $url = $route->assemble(array('id' => 3));
        $this->assertEquals('index/index/id/3', $url);

        $url = $route->assemble(array('action' => 'test'));
        $this->assertEquals('index/test', $url);

        $url = $route->assemble(array('action' => 'test', 'id' => 3));
        $this->assertEquals('index/test/id/3', $url);

        $url = $route->assemble(array('controller' => 'test'));
        $this->assertEquals('test', $url);

        $url = $route->assemble(array('controller' => 'test', 'action' => 'test'));
        $this->assertEquals('test/test', $url);

        $url = $route->assemble(array('controller' => 'test', 'id' => 3));
        $this->assertEquals('test/index/id/3', $url);

        $url = $route->assemble(array());
        $this->assertEquals('', $url);

        $route->match('ctrl');

        $url = $route->assemble(array('id' => 3));
        $this->assertEquals('ctrl/index/id/3', $url);

        $url = $route->assemble(array('action' => 'test'));
        $this->assertEquals('ctrl/test', $url);

        $url = $route->assemble();
        $this->assertEquals('ctrl', $url);

        $route->match('index');

        $url = $route->assemble();
        $this->assertEquals('', $url);
    }

    /**
     * Test guarding performance. Test may be failing on slow systems and shouldn't be failing on production.
     * This test is not critical in nature - it allows keeping changes performant.
     */

    /**
     * This test is commented out because performance testing should be done separately from unit
     * testing. It will be ported to a performance regression suite when such a suite is available.
     */
//    public function testRoutePerformance()
//    {
//        $count = 10000;
//        $expectedTime = 1;
//
//        $info = "This test may be failing on slow systems and shouldn't be failing on production. Tests if " . ($count / 10) . " complicated routes can be matched in a tenth of a second. Actual test matches " . $count . " times to make the test more reliable.";
//
//        $route = new Zend_Controller_Router_Route('archives/:year/:month/*', array('controller' => 'archive'));
//
//        $time_start = microtime(true);
//
//        for ($i = 1; $i <= $count; $i++) {
//            $values = $route->match('archives/2006/' . $i . '/controller/test/year/' . $i . '/sort/author');
//        }
//
//        $time_end = microtime(true);
//        $time = $time_end - $time_start;
//
//        $this->assertLessThan($expectedTime, $time, $info);
//    }

    public function testForZF2543()
    {
        $route = new Zend_Controller_Router_Route('families/:action/*', array('module' => 'default', 'controller' => 'categories', 'action' => 'index'));
        $this->assertEquals('families', $route->assemble());

        $values = $route->match('families/edit/id/4');
        $this->assertType('array', $values);

        $this->assertEquals('families/edit/id/4', $route->assemble());
    }

    public function testEncode()
    {
        $route = new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index'));

        $url = $route->assemble(array('controller' => 'My Controller'), false, true);
        $this->assertEquals('My+Controller', $url);

        $url = $route->assemble(array('controller' => 'My Controller'), false, false);
        $this->assertEquals('My Controller', $url);

        $token = $route->match('en/foo/id/My Value');

        $url = $route->assemble(array(), false, true);
        $this->assertEquals('en/foo/id/My+Value', $url);

        $url = $route->assemble(array('id' => 'My Other Value'), false, true);
        $this->assertEquals('en/foo/id/My+Other+Value', $url);

        $route = new Zend_Controller_Router_Route(':controller/*', array('controller' => 'My Controller'));
        $url = $route->assemble(array('id' => 1), false, true);
        $this->assertEquals('My+Controller/id/1', $url);
    }

    public function testPartialMatch()
    {
        $route = new Zend_Controller_Router_Route(':lang/:temp', array('lang' => 'pl'), array('temp' => '\d+'));

        $values = $route->match('en/tmp/ctrl/action/id/1', true);

        $this->assertFalse($values);

        $route = new Zend_Controller_Router_Route(':lang/:temp', array('lang' => 'pl'));

        $values = $route->match('en/tmp/ctrl/action/id/1', true);

        $this->assertType('array', $values);
        $this->assertEquals('en', $values['lang']);
        $this->assertEquals('tmp', $values['temp']);
        $this->assertEquals('en/tmp', $route->getMatchedPath());

    }

    /**
     * Translated behaviour
     */
    public function testStaticTranslationAssemble()
    {
        $route = new Zend_Controller_Router_Route('foo/@foo');
        $url   = $route->assemble();

        $this->assertEquals('foo/en_foo', $url);
    }

    public function testStaticTranslationMatch()
    {
        $route  = new Zend_Controller_Router_Route('foo/@foo');
        $values = $route->match('foo/en_foo');

        $this->assertTrue(is_array($values));
    }

    public function testDynamicTranslationAssemble()
    {
        $route = new Zend_Controller_Router_Route('foo/:@myvar');
        $url   = $route->assemble(array('myvar' => 'foo'));

        $this->assertEquals('foo/en_foo', $url);
    }

    public function testDynamicTranslationMatch()
    {
        $route  = new Zend_Controller_Router_Route('foo/:@myvar');
        $values = $route->match('foo/en_foo');

        $this->assertEquals($values['myvar'], 'foo');
    }

    public function testTranslationMatchWrongLanguage()
    {
        $route  = new Zend_Controller_Router_Route('foo/@foo');
        $values = $route->match('foo/de_foo');

        $this->assertFalse($values);
    }

    public function testTranslationAssembleLocaleInstanceOverride()
    {
        $route = new Zend_Controller_Router_Route('foo/@foo', null, null, null, 'de');
        $url   = $route->assemble();

        $this->assertEquals('foo/de_foo', $url);
    }

    public function testTranslationAssembleLocaleParamOverride()
    {
        $route = new Zend_Controller_Router_Route('foo/@foo');
        $url   = $route->assemble(array('@locale' => 'de'));

        $this->assertEquals('foo/de_foo', $url);
    }

    public function testTranslationAssembleLocaleStaticOverride()
    {
        Zend_Controller_Router_Route::setDefaultLocale('de');

        $route = new Zend_Controller_Router_Route('foo/@foo');
        $url   = $route->assemble();

        $this->assertEquals('foo/de_foo', $url);
    }

    public function testTranslationAssembleLocaleRegistryOverride()
    {
        Zend_Registry::set('Zend_Locale', 'de');

        $route = new Zend_Controller_Router_Route('foo/@foo');
        $url   = $route->assemble();

        $this->assertEquals('foo/de_foo', $url);
    }

    public function testTranslationAssembleTranslatorInstanceOverride()
    {
        $translator = new Zend_Translate('array', array('foo' => 'en_baz'), 'en');

        $route = new Zend_Controller_Router_Route('foo/@foo', null, null, $translator);
        $url   = $route->assemble();

        $this->assertEquals('foo/en_baz', $url);
    }

    public function testTranslationAssembleTranslatorStaticOverride()
    {
        $translator = new Zend_Translate('array', array('foo' => 'en_baz'), 'en');

        Zend_Controller_Router_Route::setDefaultTranslator($translator);

        $route = new Zend_Controller_Router_Route('foo/@foo');
        $url   = $route->assemble();

        $this->assertEquals('foo/en_baz', $url);
    }

    public function testTranslationAssembleTranslatorRegistryOverride()
    {
        $translator = new Zend_Translate('array', array('foo' => 'en_baz'), 'en');

        Zend_Registry::set('Zend_Translate', $translator);

        $route = new Zend_Controller_Router_Route('foo/@foo');
        $url   = $route->assemble();

        $this->assertEquals('foo/en_baz', $url);
    }

    public function testTranslationAssembleTranslatorNotFound()
    {
        Zend_Registry::set('Zend_Translate', null);

        $route = new Zend_Controller_Router_Route('foo/@foo');

        try {
            $url = $route->assemble();
            $this->fail('Expected Zend_Controller_Router_Exception was not raised');
        } catch (Zend_Controller_Router_Exception $e) {
            $this->assertEquals('Could not find a translator', $e->getMessage());
        }
    }

    public function testEscapedSpecialCharsWithoutTranslation()
    {
        $route = new Zend_Controller_Router_Route('::foo/@@bar/:foo');

        $path = $route->assemble(array('foo' => 'bar'));
        $this->assertEquals($path, ':foo/@bar/bar');

        $values = $route->match(':foo/@bar/bar');
        $this->assertEquals($values['foo'], 'bar');
    }

    public function testEscapedSpecialCharsWithTranslation()
    {
        $route = new Zend_Controller_Router_Route('::foo/@@bar/:@myvar');

        $path = $route->assemble(array('myvar' => 'foo'));
        $this->assertEquals($path, ':foo/@bar/en_foo');

        $values = $route->match(':foo/@bar/en_foo');
        $this->assertEquals($values['myvar'], 'foo');
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Controller_Router_RouteTests::main') {
    Zend_Controller_Router_RouteTests::main();
}
