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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

namespace ZendTest\View;

use Zend\View\View,
    Zend\View\Helper\DeclareVars,
    Zend\View\Helper\Doctype,
    Zend\Loader;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->notices = array();
        $this->errorReporting = error_reporting();
        $this->displayErrors  = ini_get('display_errors');
    }

    public function tearDown()
    {
        error_reporting($this->errorReporting);
        ini_set('display_errors', $this->displayErrors);
    }

    /**
     * Tests that the default script path is properly initialized
     */
    public function testDefaultScriptPath()
    {
        $this->_testDefaultPath('script', false);
    }

    /**
     * Tests that the default helper path is properly initialized
     * and the directory is readable
     */
    public function testDefaultHelperPath()
    {
        $this->_testDefaultPath('helper');
    }

    /**
     * Tests that the default filter path is properly initialized
     * and the directory is readable
     */
    public function testDefaultFilterPath()
    {
        $this->_testDefaultPath('filter', false);
    }

    /**
     * Tests that script paths are added, properly ordered, and that
     * directory separators are handled correctly.
     */
    public function testAddScriptPath()
    {
        $this->_testAddPath('script');
    }

    /**
     * Tests that helper paths are added, properly ordered, and that
     * directory separators are handled correctly.
     */
    public function testAddHelperPath()
    {
        $this->_testAddPath('helper');
    }

    /**
     * Tests that filter paths are added, properly ordered, and that
     * directory separators are handled correctly.
     */
    public function testAddFilterPath()
    {
        $this->_testAddPath('filter');
    }

    /**
     * Tests that the (script|helper|filter) path array is properly
     * initialized after instantiation.
     *
     * @param string  $pathType         one of "script", "helper", or "filter".
     * @param boolean $testReadability  check if the path is readable?
     */
    protected function _testDefaultPath($pathType, $testReadability = true)
    {
        $view = new View();

        $reflector = $view->getAllPaths();
        $paths     = $this->_filterPath($reflector[$pathType]);

        // test default helper path
        $this->assertType('array', $paths);
        if ('script' == $pathType) {
            $this->assertEquals(0, count($paths));
        } else {
            $this->assertEquals(1, count($paths));

            $prefix = 'Zend\\View\\' . ucfirst($pathType) . '\\';
            $this->assertTrue(array_key_exists($prefix, $paths));

            if ($testReadability) {
                $path = current($paths[$prefix]);

                if (substr(PHP_OS, 0, 3) != 'WIN') {
                    $this->assertTrue(Loader::isReadable($path));
                } else {
                    $this->assertTrue(is_dir($path));
                }
            }
        }
    }

    /**
     * Tests (script|helper|filter) paths can be added, that they are added
     * in the proper order, and that directory separators are properly handled.
     *
     * @param string $pathType one of "script", "helper", or "filter".
     */
    protected function _testAddPath($pathType)
    {
        $view   = new View();
        $prefix = 'Zend\\View\\' . ucfirst($pathType) . '\\';

        // introspect default paths and build expected results.
        $reflector     = $view->getAllPaths();
        $expectedPaths = $reflector[$pathType];

        if ($pathType != 'script') {
            $expectedPaths = $this->_filterPath($expectedPaths[$prefix]);
        }

        array_push($expectedPaths, 'baz');
        array_push($expectedPaths, 'bar');
        array_push($expectedPaths, 'foo');

        // add paths
        $func = 'add' . ucfirst($pathType) . 'Path';
        $view->$func('baz');    // no separator
        $view->$func('bar\\');  // windows
        $view->$func('foo/');   // unix

        // introspect script paths after adding two new paths
        $reflector   = $view->getAllPaths();
        $actualPaths = $this->_filterPath($reflector[$pathType]);

        switch ($pathType) {
            case 'script':
                $this->assertSame(array_reverse($expectedPaths), $actualPaths);
                break;
            case 'helper':
            case 'filter':
            default:
                $this->assertTrue(array_key_exists($prefix, $actualPaths));
                $this->assertSame($expectedPaths, $actualPaths[$prefix], 'Actual: ' . var_export($actualPaths, 1) . "\nExpected: " . var_export($expectedPaths, 1));
        }
    }

    /**
     * Tests that the Zend_View environment is clean of any instance variables
     */
    public function testSandbox()
    {
        $view = new View();
        $this->assertSame(array(), get_object_vars($view));
    }

    /**
     * Tests that isset() and empty() work correctly.  This is a common problem
     * because __isset() was not supported until PHP 5.1.
     */
    public function testIssetEmpty()
    {
        $view = new View();
        $this->assertFalse(isset($view->foo));
        $this->assertTrue(empty($view->foo));

        $view->foo = 'bar';
        $this->assertTrue(isset($view->foo));
        $this->assertFalse(empty($view->foo));
    }

    /**
     * Tests that a helper can be loaded from the search path
     */
    public function testLoadHelper()
    {
        $view = new View();

        $view->addHelperPath(__DIR__ . '/_stubs/HelperDir1', 'Foo\\View\\Helper');
        $view->addHelperPath(__DIR__ . '/_stubs/HelperDir2');

        $this->assertEquals('foo', $view->stub1(), var_export($view->getHelperPaths(), 1));
        $this->assertEquals('bar', $view->stub2());

        // erase the paths to the helper stubs
        $view->setHelperPath(null);

        // verify that object handle of a stub was cached by calling it again
        // without its path in the helper search paths
        $this->assertEquals( 'foo', $view->stub1() );
    }

    /**
     * Tests that calling a nonexistant helper file throws the expected exception
     */
    public function testLoadHelperNonexistantFile()
    {
        $this->setExpectedException('Zend\\Loader\\PluginLoaderException', 'not found');
        $view = new View();
        $view->nonexistantHelper();
    }

    /**
     * Tests that calling a helper whose file exists but class is not found within
     * throws the expected exception
     */
    public function testLoadHelperNonexistantClass()
    {
        $this->setExpectedException('Zend\\Loader\\PluginLoaderException', 'not found');
        $view = new View();
        $view->setHelperPath(array(__DIR__ . '/_stubs/HelperDir1'));
        $view->stubEmpty();
    }

    public function testHelperPathMayBeRegisteredUnderMultiplePrefixes()
    {
        $view = new View();

        $view->addHelperPath(__DIR__ . '/_stubs/HelperDir1', 'Foo\\View\\Helper');
        $view->addHelperPath(__DIR__ . '/_stubs/HelperDir1', 'Zend\\View\\Helper');

        $helper = $view->getHelper('Stub1');
        $this->assertTrue($helper instanceof \Foo\View\Helper\Stub1);
    }

    /**
     * Tests that render() can render a template.
     */
    public function testRender()
    {
        $view = new View();

        $view->setScriptPath(__DIR__ . '/_templates');

        $view->bar = 'bar';

        $this->assertEquals("foo bar baz\n", $view->render('test.phtml') );
    }

    /**
     * Tests that render() works when called within a template, and that
     * protected members are not available
     */
    public function testRenderSubTemplates()
    {
        $view = new View();
        $view->setScriptPath(__DIR__ . '/_templates');
        $view->content = 'testSubTemplate.phtml';
        $this->assertEquals('', $view->render('testParent.phtml'));

        $logFile = __DIR__ . '/_templates/view.log';
        $this->assertTrue(file_exists($logFile));
        $log = file_get_contents($logFile);
        unlink($logFile); // clean up...
        $this->assertContains('This text should not be displayed', $log);
        $this->assertNotContains('testSubTemplate.phtml', $log);
    }

    /**
     * Tests that array properties may be modified after being set (see [ZF-460]
     * and [ZF-268] for symptoms leading to this test)
     */
    public function testSetArrayProperty()
    {
        $view = new View();
        $view->foo   = array();
        $view->foo[] = 42;

        $foo = $view->foo;

        $this->assertTrue(is_array($foo));
        $this->assertEquals(42, $foo[0], var_export($foo, 1));

        $view->assign('bar', array());
        $view->bar[] = 'life';
        $bar = $view->bar;
        $this->assertTrue(is_array($bar));
        $this->assertEquals('life', $bar[0], var_export($bar, 1));

        $view->assign(array(
            'baz' => array('universe'),
        ));
        $view->baz[] = 'everything';
        $baz = $view->baz;
        $this->assertTrue(is_array($baz));
        $this->assertEquals('universe', $baz[0]);
        $this->assertEquals('everything', $baz[1], var_export($baz, 1));
    }

    /**
     * Test that array properties are cleared following clearVars() call
     */
    public function testClearVars()
    {
        $view = new View();
        $view->foo     = array();
        $view->content = 'content';

        $this->assertTrue(is_array($view->foo));
        $this->assertEquals('content', $view->content);

        $view->clearVars();
        $this->assertFalse(isset($view->foo));
        $this->assertFalse(isset($view->content));
    }

    /**
     * Test that script paths are cleared following setScriptPath(null) call
     */
    public function testClearScriptPath()
    {
        $view = new View();

        // paths should be initially empty
        $this->assertSame(array(), $view->getScriptPaths());

        // add a path
        $view->setScriptPath('foo');
        $scriptPaths = $view->getScriptPaths();
        $this->assertType('array', $scriptPaths);
        $this->assertEquals(1, count($scriptPaths));

        // clear paths
        $view->setScriptPath(null);
        $this->assertSame(array(), $view->getScriptPaths());
    }

    /**
     * Test that an exception is thrown when no script path is set
     */
    public function testNoPath()
    {
        $view = new View();
        try {
            $view->render('somefootemplate.phtml');
            $this->fail('Rendering a template when no script path is set should raise an exception');
        } catch (\Exception $e) {
            // success...
            // @todo  assert something?
        }
    }

    /**
     * Test that getEngine() returns the same object
     */
    public function testGetEngine()
    {
        $view = new View();
        $this->assertSame($view, $view->getEngine());
    }

    public function testInstanceOfInterface()
    {
        $view = new View();
        $this->assertTrue($view instanceof \Zend\View\ViewEngine);
    }

    public function testGetVars()
    {
        $view = new View();
        $view->foo = 'bar';
        $view->bar = 'baz';
        $view->baz = array('foo', 'bar');

        $vars = $view->getVars();
        $this->assertEquals(3, count($vars));
        $this->assertEquals('bar', $vars['foo']);
        $this->assertEquals('baz', $vars['bar']);
        $this->assertEquals(array('foo', 'bar'), $vars['baz']);
    }

    /**
     * Test set/getEncoding()
     * @group ZF-8715
     */
    public function testSetGetEncoding()
    {
        $view = new View();
        $this->assertEquals('UTF-8', $view->getEncoding());

        $view->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $view->getEncoding());
    }

    public function testEmptyPropertiesReturnAppropriately()
    {
        $view = new View();
        $view->foo = false;
        $view->bar = null;
        $view->baz = '';
        $this->assertTrue(empty($view->foo));
        $this->assertTrue(empty($view->bar));
        $this->assertTrue(empty($view->baz));
    }

    public function testFluentInterfaces()
    {
        $view = new View();
        try {
            $test = $view->setEscape('strip_tags')
                ->setFilter('htmlspecialchars')
                ->setEncoding('UTF-8')
                ->setScriptPath(__DIR__ . '/_templates')
                ->setHelperPath(__DIR__ . '/_stubs/HelperDir1')
                ->setFilterPath(__DIR__ . '/_stubs/HelperDir1')
                ->assign('foo', 'bar');
        } catch (\Exception $e){
            $this->fail('Setters should not throw exceptions');
        }

        $this->assertTrue($test instanceof View);
    }

    public function testSetConfigInConstructor()
    {
        $scriptPath = $this->_filterPath(__DIR__ . '/_templates/');
        $helperPath = $this->_filterPath(__DIR__ . '/_stubs/HelperDir1/');
        $filterPath = $this->_filterPath(__DIR__ . '/_stubs/HelperDir1/');

        $config = array(
            'escape'           => 'strip_tags',
            'encoding'         => 'UTF-8',
            'scriptPath'       => $scriptPath,
            'helperPath'       => $helperPath,
            'helperPathPrefix' => 'My\\View\\Helper',
            'filterPath'       => $filterPath,
            'filterPathPrefix' => 'My\\View\\Filter',
            'filter'           => 'urlencode',
        );

        $view = new View($config);
        $scriptPaths = $view->getScriptPaths();
        $helperPaths = $view->getHelperPaths();
        $filterPaths = $view->getFilterPaths();

        $this->assertContains($this->_filterPath($scriptPath), $this->_filterPath($scriptPaths));

        $found  = false;
        $prefix = false;
        foreach ($helperPaths as $helperPrefix => $paths) {
            foreach ($paths as $path) {
                $path = $this->_filterPath($path);
                if (strstr($path, $helperPath)) {
                    $found  = true;
                    $prefix = $helperPrefix;
                }
            }
        }
        $this->assertTrue($found, var_export($helperPaths, 1));
        $this->assertEquals('My\\View\\Helper\\', $prefix);

        $found  = false;
        $prefix = false;
        foreach ($filterPaths as $classPrefix => $paths) {
            foreach ($paths as $pathInfo) {
                $path = $this->_filterPath($pathInfo);
                if (strstr($pathInfo, $filterPath)) {
                    $found  = true;
                    $prefix = $classPrefix;
                }
            }
        }
        $this->assertTrue($found, var_export($filterPaths, 1));
        $this->assertEquals('My\\View\\Filter\\', $prefix);
    }

    public function testUnset()
    {
        $view = new View();
        unset($view->_path);
        // @todo  assert something?
    }

    public function testSetProtectedThrowsException()
    {
        $view = new View();
        try {
            $view->_path = 'bar';
            $this->fail('Should not be able to set protected properties');
        } catch (\Exception $e) {
            // success
            // @todo  assert something?
        }
    }

    public function testHelperPathWithPrefix()
    {
        $view = new View();
        $status = $view->addHelperPath(__DIR__ . '/_stubs/HelperDir1/', 'My\\View\\Helper');
        $this->assertSame($view, $status);
        $helperPaths = $view->getHelperPaths();
        $this->assertTrue(array_key_exists('My\\View\\Helper\\', $helperPaths));
        $path = $this->_filterPath(current($helperPaths['My\\View\\Helper\\']));
        $this->assertEquals($this->_filterPath(__DIR__ . '/_stubs/HelperDir1/'), $path);

        $view->setHelperPath(__DIR__ . '/_stubs/HelperDir2/', 'Other\\View\\Helper');
        $helperPaths = $view->getHelperPaths();
        $this->assertTrue(array_key_exists('Other\\View\\Helper\\', $helperPaths));
        $path = $this->_filterPath(current($helperPaths['Other\\View\\Helper\\']));
        $this->assertEquals($this->_filterPath(__DIR__ . '/_stubs/HelperDir2/'), $path);
    }

    public function testHelperPathWithPrefixAndRelativePath()
    {
        $view = new View();
        $status = $view->addHelperPath('Zend/_stubs/HelperDir1/', 'My\\View\\Helper');
        $this->assertSame($view, $status);
        $helperPaths = $view->getHelperPaths();
        $this->assertTrue(array_key_exists('My\\View\\Helper\\', $helperPaths));
        $this->assertContains($this->_filterPath('Zend/_stubs/HelperDir1/'), $this->_filterPath(current($helperPaths['My\\View\\Helper\\'])));
    }

    public function testFilterPathWithPrefix()
    {
        $view = new View();
        $status = $view->addFilterPath(__DIR__ . '/_stubs/HelperDir1/', 'My\\View\\Filter');
        $this->assertSame($view, $status);
        $filterPaths = $view->getFilterPaths();
        $this->assertTrue(array_key_exists('My\\View\\Filter\\', $filterPaths));
        $this->assertEquals($this->_filterPath(__DIR__ . '/_stubs/HelperDir1/'), $this->_filterPath(current($filterPaths['My\\View\\Filter\\'])));

        $view->setFilterPath(__DIR__ . '/_stubs/HelperDir2/', 'Other\\View\\Filter');
        $filterPaths = $view->getFilterPaths();
        $this->assertTrue(array_key_exists('Other\\View\\Filter\\', $filterPaths));
        $this->assertEquals($this->_filterPath(__DIR__ . '/_stubs/HelperDir2/'), $this->_filterPath(current($filterPaths['Other\\View\\Filter\\'])));
    }

    public function testAssignThrowsExceptionsOnBadValues()
    {
        $view = new View();
        try {
            $view->assign('_path', __DIR__ . '/_stubs/HelperDir2/');
            $this->fail('Protected/private properties cannot be assigned');
        } catch (\Exception $e) {
            // success
            // @todo  assert something?
        }

        try {
            $view->assign(array('_path' => __DIR__ . '/_stubs/HelperDir2/'));
            $this->fail('Protected/private properties cannot be assigned');
        } catch (\Exception $e) {
            // success
            // @todo  assert something?
        }

        try {
            $view->assign($this);
            $this->fail('Assign spec requires string or array');
        } catch (\Exception $e) {
            // success
            // @todo  assert something?
        }
    }

    public function testEscape()
    {
        $view = new View();
        $original = "Me, Myself, & I";
        $escaped  = $view->escape($original);
        $this->assertNotEquals($original, $escaped);
        $this->assertEquals("Me, Myself, &amp; I", $escaped);
    }

    public function testCustomEscape()
    {
        $view = new View();
        $view->setEscape('strip_tags');
        $original = "<p>Some text</p>";
        $escaped  = $view->escape($original);
        $this->assertNotEquals($original, $escaped);
        $this->assertEquals("Some text", $escaped);
    }

    /**
     * @group ZF-9295
     */
    public function testEscapeShouldAllowAndUseMoreThanOneArgument()
    {
        $view = new View();
        $view->setEscape(array($this, 'escape'));
        $this->assertEquals('foobar', $view->escape('foo', 'bar'));
    }

    public function escape($value, $additional = '')
    {
        return $value . $additional;
    }

    public function testZf995UndefinedPropertiesReturnNull()
    {
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', true);
        $view = new View();
        $view->setScriptPath(__DIR__ . '/_templates');

        ob_start();
        echo $view->render('testZf995.phtml');
        $content = ob_get_flush();
        $this->assertTrue(empty($content));
    }

    public function testInit()
    {
        $view = new Extension();
        $this->assertEquals('bar', $view->foo);
        $paths = $view->getScriptPaths();
        $this->assertEquals(1, count($paths));
        $this->assertEquals(__DIR__ . '/_templates/', $paths[0]);
    }

    public function testHelperViewAccessor()
    {
        $view = new View();
        $view->addHelperPath(__DIR__ . '/_stubs/HelperDir2/');
        $view->stub2();

        $helpers = $view->getHelpers();
        $this->assertEquals(1, count($helpers));
        $this->assertTrue(isset($helpers['Stub2']));
        $stub2 = $helpers['Stub2'];
        $this->assertTrue($stub2 instanceof \Zend\View\Helper\Stub2);
        $this->assertTrue(isset($stub2->view));
        $this->assertSame($view, $stub2->view);
    }

    public function testSetBasePath()
    {
        $view = new View();
        $base = __DIR__;
        $view->setBasePath($base);
        $this->_testBasePath($view, $base);
    }

    public function testAddBasePath()
    {
        $view = new View();
        $base = __DIR__;
        $view->addBasePath($base);
        $this->_testBasePath($view, $base);

        $base = __DIR__ . DIRECTORY_SEPARATOR . 'View2';
        $view->addBasePath($base);
        $this->_testBasePath($view, $base);
    }

    public function testAddBasePathWithClassPrefix()
    {
        $view = new View();
        $base = __DIR__;
        $view->addBasePath($base, 'My\\Foo');
        $this->_testBasePath($view, $base, 'My\\Foo');
    }

    public function testSetBasePathFromConstructor()
    {
        $base = __DIR__ . '/View';
        $view = new View(array('basePath' => $base));
        $this->_testBasePath($view, $base);
    }

    public function testSetBasePathWithClassPrefix()
    {
        $view = new View();
        $base = __DIR__;
        $view->setBasePath($base, 'My\\Foo');
        $this->_testBasePath($view, $base, 'My\\Foo');
    }

    public function testSetBasePathFromConstructorWithClassPrefix()
    {
        $base = __DIR__;
        $view = new View(array('basePath' => $base, 'basePathPrefix' => 'My\\Foo'));
        $this->_testBasePath($view, $base);
    }

    protected function _filterPath($path)
    {
        if (is_array($path)) {
            foreach ($path as $k => $p) {
                $path[$k] = $this->_filterPath($p);
            }
            return $path;
        }

        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $path = str_replace('//', '/', $path);
        $path = rtrim($path, '/');
        return $path;
    }

    protected function _testBasePath(View $view, $base, $classPrefix = null)
    {
        $base        = $this->_filterPath($base);
        $scriptPaths = $this->_filterPath($view->getScriptPaths());
        $helperPaths = $this->_filterPath($view->getHelperPaths());
        $filterPaths = $this->_filterPath($view->getFilterPaths());
        $this->assertContains($base  . '/scripts', $scriptPaths);

        $found  = false;
        $prefix = false;
        foreach ($helperPaths as $pathPrefix => $paths) {
            foreach ($paths as $path) {
                $path = $this->_filterPath($path);
                if ($path == $base . '/helpers') {
                    $found  = true;
                    $prefix = $pathPrefix;
                    break;
                }
            }
        }
        $this->assertTrue($found, var_export($helperPaths, 1));
        if (null !== $classPrefix) {
            $this->assertTrue($prefix !== false);
            $this->assertEquals($classPrefix . '\\Helper\\', $prefix);
        }

        $found  = false;
        $prefix = false;
        foreach ($filterPaths as $pathPrefix => $paths) {
            foreach ($paths as $path) {
                $path = $this->_filterPath($path);
                if ($path == $base . '/filters') {
                    $found  = true;
                    $prefix = $pathPrefix;
                    break;
                }
            }
        }
        $this->assertTrue($found, var_export($filterPaths, 1));
        if (null !== $classPrefix) {
            $this->assertTrue($prefix !== false);
            $this->assertEquals($classPrefix . '\\Filter\\', $prefix);
        }
    }

    public function handleNotices($errno, $errstr, $errfile, $errline)
    {
        if (!isset($this->notices)) {
            $this->notices = array();
        }
        if ($errno === E_USER_NOTICE) {
            $this->notices[] = $errstr;
        }
    }

    public function testStrictVars()
    {
        $view = new View();
        $view->setScriptPath(__DIR__ . DIRECTORY_SEPARATOR . '_templates');
        $view->strictVars(true);
        set_error_handler(array($this, 'handleNotices'), E_USER_NOTICE);
        $content = $view->render('testStrictVars.phtml');
        restore_error_handler();
        foreach (array('foo', 'bar') as $key) {
            $this->assertContains('Key "' . $key . '" does not exist', $this->notices);
        }
    }

    public function testGetScriptPath()
    {
        $view = new View();
        $base = __DIR__ . '/_templates';
        $view->setScriptPath($base);
        $path = $view->getScriptPath('test.phtml');
        $this->assertEquals($base . '/test.phtml', $path);
    }

    public function testGetHelper()
    {
        $view = new View();
        $view->declareVars();
        $helper = $view->getHelper('declareVars');
        $this->assertTrue($helper instanceof DeclareVars);
    }

    public function testGetHelperPath()
    {
        $reflection = new \ReflectionClass('Zend\\View\\Helper\\DeclareVars');
        $expected   = $reflection->getFileName();

        $view = new View();
        $view->declareVars();
        $helperPath = $view->getHelperPath('declareVars');
        $this->assertContains($expected, $helperPath);
    }

    public function testGetFilter()
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR;
        require_once $base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1' . DIRECTORY_SEPARATOR . 'Foo.php';

        $view = new View();
        $view->setScriptPath($base . '_templates');
        $view->addFilterPath($base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1', 'ZendTest\\View\\Filter');

        $filter = $view->getFilter('foo');
        $this->assertTrue($filter instanceof Filter\Foo);
    }

    public function testGetFilterPath()
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR;
        $expected = $base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1' . DIRECTORY_SEPARATOR . 'Foo.php';

        $view = new View();
        $view->setScriptPath($base . '_templates');
        $view->addFilterPath($base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1', 'ZendTest\\View\\Filter');

        $filterPath = $view->getFilterPath('foo');
        $this->assertEquals($expected, $filterPath, var_export($filterPath, 1));
    }

    public function testGetFilters()
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR;

        $view = new View();
        $view->setScriptPath($base . '_templates');
        $view->addFilterPath($base . '_stubs' . DIRECTORY_SEPARATOR . 'FilterDir1');
        $view->addFilter('foo');

        $filters = $view->getFilters();
        $this->assertEquals(1, count($filters));
        $this->assertEquals('foo', $filters[0]);
    }

    public function testMissingViewScriptExceptionText()
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR;
        $view = new View();
        $view->setScriptPath($base . '_templates');

        try {
            $view->render('bazbatNotExists.php.tpl');
            $this->fail('Non-existent view script should cause an exception');
        } catch (\Exception $e) {
            $this->assertContains($base. '_templates', $e->getMessage());
        }
    }

    public function testGetHelperIsCaseInsensitive()
    {
        $view = new View();
        $hidden = $view->formHidden('foo', 'bar');
        $this->assertContains('<input type="hidden"', $hidden);

        $hidden = $view->getHelper('formHidden')->direct('foo', 'bar');
        $this->assertContains('<input type="hidden"', $hidden);

        $hidden = $view->getHelper('FormHidden')->direct('foo', 'bar');
        $this->assertContains('<input type="hidden"', $hidden);
    }

    public function testGetHelperUsingDifferentCasesReturnsSameInstance()
    {
        $view    = new View();
        $helper1 = $view->getHelper('formHidden');
        $helper2 = $view->getHelper('FormHidden');

        $this->assertSame($helper1, $helper2);
    }

    /**
     * @issue ZF-2742
     */
    public function testGetHelperWorksWithPredefinedClassNames()
    {
        $view = new View();

        $view->setHelperPath(__DIR__ . '/_stubs/HelperDir2');
        try {
            $view->setHelperPath(__DIR__ . '/_stubs/HelperDir1', null);
            $this->fail('Exception for empty prefix was expected.');
        } catch (\Exception $e) {
            $this->assertContains('only takes strings', $e->getMessage());
        }

        try {
            $view->setHelperPath(__DIR__ . '/_stubs/HelperDir1', null);
            $this->fail('Exception for empty prefix was expected.');
        } catch (\Exception $e) {
            $this->assertContains('only takes strings', $e->getMessage());
        }


        try {
            $helper = $view->getHelper('Datetime');
        } catch (\Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    public function testUseStreamWrapperFlagShouldDefaultToFalse()
    {
        $this->view = new View();
        $this->assertFalse($this->view->useStreamWrapper());
    }

    public function testUseStreamWrapperStateShouldBeConfigurable()
    {
        $this->testUseStreamWrapperFlagShouldDefaultToFalse();
        $this->view->setUseStreamWrapper(true);
        $this->assertTrue($this->view->useStreamWrapper());
        $this->view->setUseStreamWrapper(false);
        $this->assertFalse($this->view->useStreamWrapper());
    }

    /**
     * @group ZF-5748
     */
    public function testRenderShouldNotAllowScriptPathsContainingParentDirectoryTraversal()
    {
        $view = new View();
        try {
            $view->render('../foobar.html');
            $this->fail('Should not allow parent directory traversal');
        } catch (\Zend\View\Exception $e) {
            $this->assertContains('parent directory traversal', $e->getMessage());
        }

        try {
            $view->render('foo/../foobar.html');
            $this->fail('Should not allow parent directory traversal');
        } catch (\Zend\View\Exception $e) {
            $this->assertContains('parent directory traversal', $e->getMessage());
        }

        try {
            $view->render('foo/..\foobar.html');
            $this->fail('Should not allow parent directory traversal');
        } catch (\Zend\View\Exception $e) {
            $this->assertContains('parent directory traversal', $e->getMessage());
        }
    }

    /**
     * @group ZF-5748
     */
    public function testLfiProtectionFlagShouldBeEnabledByDefault()
    {
        $view = new View();
        $this->assertTrue($view->isLfiProtectionOn());
    }

    /**
     * @group ZF-5748
     */
    public function testLfiProtectionFlagMayBeDisabledViaConstructorOption()
    {
        $view = new View(array('lfiProtectionOn' => false));
        $this->assertFalse($view->isLfiProtectionOn());
    }

    /**
     * @group ZF-5748
     */
    public function testLfiProtectionFlagMayBeDisabledViaMethodCall()
    {
        $view = new View();
        $view->setLfiProtection(false);
        $this->assertFalse($view->isLfiProtectionOn());
    }

    /**
     * @group ZF-5748
     */
    public function testDisablingLfiProtectionAllowsParentDirectoryTraversal()
    {
        $view = new View(array(
            'lfiProtectionOn' => false,
            'scriptPath'      => __DIR__ . '/_templates/',
        ));
        try {
            $test = $view->render('../_stubs/scripts/LfiProtectionCheck.phtml');
            $this->assertContains('LFI', $test);
        } catch (Zend_View_Exception $e) {
            $this->fail('LFI attack failed: ' . $e->getMessage());
        }
    }

    /**
     * @group ZF-6087
     */
    public function testConstructorShouldAllowPassingArrayOfHelperPaths()
    {
        $view = new View(array(
            'helperPath' => array(
                'My\\View'   => 'My/',
            ),
        ));
        $paths = $view->getHelperPaths();
        $this->assertTrue(array_key_exists('My\\View\\', $paths), var_export($paths, 1));
    }

    /**
     * @group ZF-6087
     */
    public function testConstructorShouldAllowPassingArrayOfFilterPaths()
    {
        $view = new View(array(
            'filterPath' => array(
                'My\\View'   => 'My/',
            ),
        ));
        $paths = $view->getFilterPaths();
        $this->assertTrue(array_key_exists('My\\View\\', $paths), var_export($paths, 1));
    }
    
    /**
     * @group ZF-8177
     */
    public function testRegisterHelperShouldRegisterHelperWithView()
    {
    	require_once __DIR__ . '/_stubs/HelperDir1/Stub1.php';
    	
    	$view = new View();
    	$helper = new \Foo\View\Helper\Stub1();
    	$view->registerHelper($helper, 'stub1');
    	
    	$this->assertEquals($view->getHelper('stub1'), $helper);
    	$this->assertEquals($view->stub1(), 'foo');
    }

    /**
     * @group ZF-8177
     * @expectedException Zend\View\Exception
     */
    public function testRegisterHelperShouldThrowExceptionIfNotProvidedAnObject()
    {
        $view = new View();
        $view->registerHelper('Foo', 'foo');
    }

    /**
     * @group ZF-8177
     * @expectedException Zend\View\Exception
     */
    public function testRegisterHelperShouldThrowExceptionIfProvidedANonHelperObject()
    {
        $view   = new View();
        $helper = new \stdClass;
        $view->registerHelper($helper, 'foo');
    }

    /**
     * @group ZF-8177
     */
    public function testRegisterHelperShouldRegisterViewObjectWithHelper()
    {
    	$view = new View();
    	$helper = new Doctype();
    	$view->registerHelper($helper, 'doctype');
        $this->assertSame($view, $helper->view);
    }

    /**
     * @group ZF-9000
     */
    public function testAddingStreamSchemeAsScriptPathShouldNotReverseSlashesOnWindows()
    {
        if (false === strstr(strtolower(PHP_OS), 'windows')) {
            $this->markTestSkipped('Windows-only test');
        }
    	$view = new View();
        $path = rtrim('file://' . str_replace('\\', '/', realpath(__DIR__)), '/') . '/';
        $view->addScriptPath($path);
        $paths = $view->getScriptPaths();
        $this->assertContains($path, $paths, var_export($paths, 1));
    }
}

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Extension extends View
{
    public function init()
    {
        $this->assign('foo', 'bar');
        $this->setScriptPath(__DIR__ . '/_templates');
    }
}
