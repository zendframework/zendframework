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
            include __DIR__ . '/../../_files/application.config.php'
        );
        parent::setUp();
    }

    public function testUseOfRouter()
    {
       $this->assertEquals(true, $this->useConsoleRequest);
    }

    public function testAssertResponseStatusCode()
    {
        $this->dispatch('--console');
        $this->assertResponseStatusCode(0);

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual status code is "0"' // check actual status code is display
        );
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
        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'Console status code assert value must be O (valid) or 1 (error)'
        );
        $this->assertResponseStatusCode(2);
    }

    public function testAssertNotResponseStatusCodeWithBadCode()
    {
        $this->dispatch('--console');
        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'Console status code assert value must be O (valid) or 1 (error)'
        );
        $this->assertNotResponseStatusCode(2);
    }

    public function testAssertConsoleOutputContains()
    {
        $this->dispatch('--console');
        $this->assertConsoleOutputContains('foo');
        $this->assertConsoleOutputContains('foo, bar');

        $this->setExpectedException(
            'PHPUnit_Framework_ExpectationFailedException',
            'actual content is "foo, bar"' // check actual content is display
        );
        $this->assertConsoleOutputContains('baz');
    }

    public function testNotAssertConsoleOutputContains()
    {
        $this->dispatch('--console');
        $this->assertNotConsoleOutputContains('baz');

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotConsoleOutputContains('foo');
    }

    public function testAssertMatchedArgumentsWithValue()
    {
        $this->dispatch('filter --date="2013-03-07 00:00:00" --id=10 --text="custom text"');
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $this->assertEquals("2013-03-07 00:00:00", $routeMatch->getParam('date'));
        $this->assertEquals("10", $routeMatch->getParam('id'));
        $this->assertEquals("custom text", $routeMatch->getParam('text'));
    }

    public function testAssertMatchedArgumentsWithValueWithoutEqualsSign()
    {
        $this->dispatch('filter --date "2013-03-07 00:00:00" --id=10 --text="custom text"');
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $this->assertEquals("2013-03-07 00:00:00", $routeMatch->getParam('date'));
        $this->assertEquals("10", $routeMatch->getParam('id'));
        $this->assertEquals("custom text", $routeMatch->getParam('text'));
    }
}
