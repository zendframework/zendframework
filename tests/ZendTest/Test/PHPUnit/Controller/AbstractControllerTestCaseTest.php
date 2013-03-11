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
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.php'
        );
        parent::setUp();
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
        $this->assertTrue(Console::isConsole());
        $this->getApplication();
        $this->assertFalse(Console::isConsole());
        $this->tearDown();
        $this->assertTrue(Console::isConsole());

        Console::overrideIsConsole(false);
        parent::setUp();

        $this->assertFalse(Console::isConsole());
        $this->getApplication();
        $this->assertFalse(Console::isConsole());

        parent::tearDown();

        $this->assertFalse(Console::isConsole());
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
}
