<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\ErrorHandler;

class ErrorHandlerTest extends TestCase
{
    public function tearDown()
    {
        if (ErrorHandler::getNestedLevel()) {
            ErrorHandler::clean();
        }
    }

    public function testNestedLevel()
    {
        $this->assertSame(0, ErrorHandler::getNestedLevel());

        ErrorHandler::start();
        $this->assertSame(1, ErrorHandler::getNestedLevel());

        ErrorHandler::start();
        $this->assertSame(2, ErrorHandler::getNestedLevel());

        ErrorHandler::stop();
        $this->assertSame(1, ErrorHandler::getNestedLevel());

        ErrorHandler::stop();
        $this->assertSame(0, ErrorHandler::getNestedLevel());
    }

    public function testClean()
    {
        ErrorHandler::start();
        $this->assertSame(1, ErrorHandler::getNestedLevel());

        ErrorHandler::start();
        $this->assertSame(2, ErrorHandler::getNestedLevel());

        ErrorHandler::clean();
        $this->assertSame(0, ErrorHandler::getNestedLevel());
    }

    public function testStarted()
    {
        $this->assertFalse(ErrorHandler::started());

        ErrorHandler::start();
        $this->assertTrue(ErrorHandler::started());

        ErrorHandler::stop();
        $this->assertFalse(ErrorHandler::started());
    }

    public function testReturnCatchedError()
    {
        ErrorHandler::start();
        strpos(); // Invalid argument list
        $err = ErrorHandler::stop();

        $this->assertInstanceOf('ErrorException', $err);
    }

    public function testThrowCatchedError()
    {
        ErrorHandler::start();
        strpos(); // Invalid argument list

        $this->setExpectedException('ErrorException');
        ErrorHandler::stop(true);
    }

    public function testAddError()
    {
        ErrorHandler::start();
        ErrorHandler::addError(1, 'test-msg1', 'test-file1', 100);
        ErrorHandler::addError(2, 'test-msg2', 'test-file2', 200);
        $err = ErrorHandler::stop();

        $this->assertInstanceOf('ErrorException', $err);
        $this->assertEquals('test-file2', $err->getFile());
        $this->assertEquals('test-msg2', $err->getMessage());
        $this->assertEquals(200, $err->getLine());
        $this->assertEquals(0, $err->getCode());
        $this->assertEquals(2, $err->getSeverity());

        $previous = $err->getPrevious();
        $this->assertInstanceOf('ErrorException', $previous);
        $this->assertEquals('test-file1', $previous->getFile());
        $this->assertEquals('test-msg1', $previous->getMessage());
        $this->assertEquals(100, $previous->getLine());
        $this->assertEquals(0, $previous->getCode());
        $this->assertEquals(1, $previous->getSeverity());
    }
}
