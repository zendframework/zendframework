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

use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class AbstractConsoleControllerTestCaseTest extends AbstractConsoleControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/_files/application.config.php'
        );
        parent::setUp();
    }

    public function testUseOfRouter()
    {
       $this->assertEquals(true, $this->useConsoleRequest);
    }

    public function testApplicationClass()
    {
        $applicationClass = get_class($this->getApplication());
        $this->assertEquals($applicationClass, 'Zend\Mvc\Application');
    }

    public function testApplicationServiceLocatorClass()
    {
        $smClass = get_class($this->getApplicationServiceLocator());
        $this->assertEquals($smClass, 'Zend\ServiceManager\ServiceManager');
    }

    public function testAssertResponseStatusCode()
    {
        $this->dispatch('--console');
        $this->assertResponseStatusCode(0);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertResponseStatusCode(1);
    }

    public function testAssertNotResponseStatusCode()
    {
        $this->dispatch('--console');
        $this->assertNotResponseStatusCode(1);

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotResponseStatusCode(0);
    }

    public function testAssertResponseStatusCodeWithBadCode()
    {
        $this->dispatch('--console');
        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertResponseStatusCode(2);
    }

    public function testAssertNotResponseStatusCodeWithBadCode()
    {
        $this->dispatch('--console');
        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotResponseStatusCode(2);
    }
}
