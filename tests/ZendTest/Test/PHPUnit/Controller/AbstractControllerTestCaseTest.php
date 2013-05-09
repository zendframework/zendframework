<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace ZendTest\Test\PHPUnit\Controller;

use Zend\Console\Console;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class AbstractControllerTestCaseTest extends AbstractHttpControllerTestCase
{
    public function tearDownCacheDir()
    {
        $cacheDir = sys_get_temp_dir() . '/zf2-module-test';
        if (is_dir($cacheDir)) {
            static::rmdir($cacheDir);
        }
    }

    public static function rmdir($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? static::rmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function setUp()
    {
        $this->tearDownCacheDir();
        Console::overrideIsConsole(null);
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.php'
        );
        parent::setUp();
    }

    public function tearDown()
    {
        $this->tearDownCacheDir();
        parent::tearDown();
    }

    public function testModuleCacheIsDisabled()
    {
        $config = $this->getApplicationConfig();
        $config = $config['module_listener_options']['cache_dir'];
        $this->assertEquals(0, count(glob($config . '/*.php')));
    }

    public function testCanNotDefineApplicationConfigWhenApplicationIsBuilt()
    {
        // cosntruct app
        $this->getApplication();

        $this->setExpectedException('Zend\Stdlib\Exception\LogicException');
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.php'
        );
    }

    public function testUseOfRouter()
    {
       // default value
       $this->assertEquals(false, $this->useConsoleRequest);
    }

    public function testApplicationClass()
    {
        $applicationClass = get_class($this->getApplication());
        $this->assertEquals($applicationClass, 'Zend\Mvc\Application');
    }

    public function testApplicationClassAndTestRestoredConsoleFlag()
    {
        $this->assertTrue(Console::isConsole(), '1. Console::isConsole returned false in initial test');
        $this->getApplication();
        $this->assertFalse(Console::isConsole(), '2. Console::isConsole returned true after retrieving application');
        $this->tearDown();
        $this->assertTrue(Console::isConsole(), '3. Console::isConsole returned false after tearDown');

        Console::overrideIsConsole(false);
        parent::setUp();

        $this->assertFalse(Console::isConsole(), '4. Console::isConsole returned true after parent::setUp');
        $this->getApplication();
        $this->assertFalse(Console::isConsole(), '5. Console::isConsole returned true after retrieving application');

        parent::tearDown();

        $this->assertFalse(Console::isConsole(), '6. Console.isConsole returned true after parent::tearDown');
    }

    public function testApplicationServiceLocatorClass()
    {
        $smClass = get_class($this->getApplicationServiceLocator());
        $this->assertEquals($smClass, 'Zend\ServiceManager\ServiceManager');
    }

    public function testAssertApplicationRequest()
    {
        $this->assertEquals(true, $this->getRequest() instanceof RequestInterface);
    }

    public function testAssertApplicationResponse()
    {
        $this->assertEquals(true, $this->getResponse() instanceof ResponseInterface);
    }

    public function testAssertModuleName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertModuleName('baz');
        $this->assertModuleName('Baz');
        $this->assertModuleName('BAz');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual module name is "baz"' // check actual module is display
        );
        $this->assertModuleName('Application');
    }

    public function testAssertNotModuleName()
    {
        $this->dispatch('/tests');
        $this->assertNotModuleName('Application');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotModuleName('baz');
    }

    public function testAssertControllerClass()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertControllerClass('IndexController');
        $this->assertControllerClass('Indexcontroller');
        $this->assertControllerClass('indexcontroller');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual controller class is "indexcontroller"' // check actual controller class is display
        );
        $this->assertControllerClass('Index');
    }

    public function testAssertNotControllerClass()
    {
        $this->dispatch('/tests');
        $this->assertNotControllerClass('Index');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotControllerClass('IndexController');
    }

    public function testAssertControllerName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertControllerName('baz_index');
        $this->assertControllerName('Baz_index');
        $this->assertControllerName('BAz_index');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual controller name is "baz_index"' // check actual controller name is display
        );
        $this->assertControllerName('baz');
    }

    public function testAssertNotControllerName()
    {
        $this->dispatch('/tests');
        $this->assertNotControllerName('baz');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotControllerName('baz_index');
    }

    public function testAssertActionName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertActionName('unittests');
        $this->assertActionName('unitTests');
        $this->assertActionName('UnitTests');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual action name is "unittests"' // check actual action name is display
        );
        $this->assertActionName('unit');
    }

    public function testAssertNotActionName()
    {
        $this->dispatch('/tests');
        $this->assertNotActionName('unit');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotActionName('unittests');
    }

    public function testAssertMatchedRouteName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertMatchedRouteName('myroute');
        $this->assertMatchedRouteName('myRoute');
        $this->assertMatchedRouteName('MyRoute');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual matched route name is "myroute"' // check actual matched route name is display
        );
        $this->assertMatchedRouteName('route');
    }

    public function testAssertNotMatchedRouteName()
    {
        $this->dispatch('/tests');
        $this->assertNotMatchedRouteName('route');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotMatchedRouteName('myroute');
    }

    /**
     * Sample tests on Application errors events
     */
    public function testAssertApplicationErrorsEvents()
    {
        $this->url('/bad-url');
        $result = $this->triggerApplicationEvent(MvcEvent::EVENT_ROUTE);
        $this->assertEquals(true, $result->stopped());
        $this->assertEquals(Application::ERROR_ROUTER_NO_MATCH, $this->getApplication()->getMvcEvent()->getError());
    }

    public function testDispatchRequestUri()
    {
        $this->dispatch('/tests');
        $this->assertEquals('/tests', $this->getApplication()->getRequest()->getRequestUri());
    }

    public function testDefaultDispatchMethod()
    {
        $this->dispatch('/tests');
        $this->assertEquals('GET', $this->getRequest()->getMethod());
    }

    public function testDispatchMethodSetOnRequest()
    {
        $this->getRequest()->setMethod('POST');
        $this->dispatch('/tests');
        $this->assertEquals('POST', $this->getRequest()->getMethod());
    }

    public function testExplicitDispatchMethodOverrideRequestMethod()
    {
        $this->getRequest()->setMethod('POST');
        $this->dispatch('/tests', 'GET');
        $this->assertEquals('GET', $this->getRequest()->getMethod());
    }

    public function testPutRequestParams()
    {
        $this->dispatch('/tests', 'PUT', array('a' => 1));
        $this->assertEquals('a=1', $this->getRequest()->getContent());
    }

    public function testPreserveContentOfPutRequest()
    {
        $this->getRequest()->setMethod('PUT');
        $this->getRequest()->setContent('my content');
        $this->dispatch('/tests');
        $this->assertEquals('my content', $this->getRequest()->getContent());
    }

    public function testExplicityPutParamsOverrideRequestContent()
    {
        $this->getRequest()->setContent('my content');
        $this->dispatch('/tests', 'PUT', array('a' => 1));
        $this->assertEquals('a=1', $this->getRequest()->getContent());
    }
}
