<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace ZendTest\Test\PHPUnit\Controller;

use Zend\EventManager\StaticEventManager;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Stdlib\Glob;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class AbstractHttpControllerTestCaseTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.php'
        );
        parent::setUp();
    }

    public function testUseOfRouter()
    {
       $this->assertEquals(false, $this->useConsoleRequest);
    }

    public function testAssertHeader()
    {
        $this->dispatch('/tests');
        $this->assertHeader('Content-Type');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertHeader('Unknow-header');
    }

    public function testAssertNotHeader()
    {
        $this->dispatch('/tests');
        $this->assertNotHeader('Unknow-header');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotHeader('Content-Type');
    }

    public function testAssertHeaderContains()
    {
        $this->dispatch('/tests');
        $this->assertHeaderContains('Content-Type', 'text/html');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "text/html"' // check actual content is display
        );
        $this->assertHeaderContains('Content-Type', 'text/json');
    }

    public function testAssertNotHeaderContains()
    {
        $this->dispatch('/tests');
        $this->assertNotHeaderContains('Content-Type', 'text/json');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotHeaderContains('Content-Type', 'text/html');
    }

    public function testAssertHeaderRegex()
    {
        $this->dispatch('/tests');
        $this->assertHeaderRegex('Content-Type', '#html$#');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "text/html"' // check actual content is display
        );
        $this->assertHeaderRegex('Content-Type', '#json#');
    }

    public function testAssertNotHeaderRegex()
    {
        $this->dispatch('/tests');
        $this->assertNotHeaderRegex('Content-Type', '#json#');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotHeaderRegex('Content-Type', '#html$#');
    }

    public function testAssertRedirect()
    {
        $this->dispatch('/redirect');
        $this->assertRedirect();

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual redirection is "http://www.zend.com"' // check actual redirection is display
        );
        $this->assertNotRedirect();
    }

    public function testAssertNotRedirect()
    {
        $this->dispatch('/test');
        $this->assertNotRedirect();

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertRedirect();
    }

    public function testAssertRedirectTo()
    {
        $this->dispatch('/redirect');
        $this->assertRedirectTo('http://www.zend.com');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual redirection is "http://www.zend.com"' // check actual redirection is display
        );
        $this->assertRedirectTo('http://www.zend.fr');
    }

    public function testAssertNotRedirectTo()
    {
        $this->dispatch('/redirect');
        $this->assertNotRedirectTo('http://www.zend.fr');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotRedirectTo('http://www.zend.com');
    }

    public function testAssertRedirectRegex()
    {
        $this->dispatch('/redirect');
        $this->assertRedirectRegex('#zend\.com$#');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual redirection is "http://www.zend.com"' // check actual redirection is display
        );
        $this->assertRedirectRegex('#zend\.fr$#');
    }

    public function testAssertNotRedirectRegex()
    {
        $this->dispatch('/redirect');
        $this->assertNotRedirectRegex('#zend\.fr#');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotRedirectRegex('#zend\.com$#');
    }
}
