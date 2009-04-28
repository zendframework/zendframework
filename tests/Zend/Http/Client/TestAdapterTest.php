<?php

require_once realpath(dirname(__FILE__) . '/../../../') . '/TestHelper.php';

require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Exercises Zend_Http_Client_Adapter_Test
 *
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @version    $Id$
 */
class Zend_Http_Client_TestAdapterTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
        $this->adapter = new Zend_Http_Client_Adapter_Test();
	}

    public function testSetConfigThrowsOnInvalidConfig()
    {
        try {
            $this->adapter->setConfig('foo');
        } catch (Exception $e) {
            $class = 'Zend_Http_Client_Adapter_Exception';
            $this->assertType($class, $e);
            $this->assertRegexp('/expects an array/i', $e->getMessage());
        }
    }

    public function testSetConfigReturnsQuietly()
    {
        $this->adapter->setConfig(array('foo' => 'bar'));
    }

    public function testConnectReturnsQuietly()
    {
        $this->adapter->connect('http://foo');
    }

    public function testCloseReturnsQuietly()
    {
        $this->adapter->close();
    }

    public function testReadDefaultResponse()
    {
        $expected = "HTTP/1.1 400 Bad Request\r\n\r\n";
        $this->assertEquals($expected, $this->adapter->read());
    }

    public function testReadingSingleResponse()
    {
        $expected = "HTTP/1.1 200 OK\r\n\r\n";
        $this->adapter->setResponse($expected);
        $this->assertEquals($expected, $this->adapter->read());
        $this->assertEquals($expected, $this->adapter->read());
    }

    public function testReadingResponseCycles()
    {
        $expected = array("HTTP/1.1 200 OK\r\n\r\n",
                          "HTTP/1.1 302 Moved Temporarily\r\n\r\n");

        $this->adapter->setResponse($expected[0]);
        $this->adapter->addResponse($expected[1]);

        $this->assertEquals($expected[0], $this->adapter->read());
        $this->assertEquals($expected[1], $this->adapter->read());
        $this->assertEquals($expected[0], $this->adapter->read());
    }

    public function testReadingResponseCyclesWhenSetByArray()
    {
        $expected = array("HTTP/1.1 200 OK\r\n\r\n",
                          "HTTP/1.1 302 Moved Temporarily\r\n\r\n");

        $this->adapter->setResponse($expected);

        $this->assertEquals($expected[0], $this->adapter->read());
        $this->assertEquals($expected[1], $this->adapter->read());
        $this->assertEquals($expected[0], $this->adapter->read());
    }

    public function testSettingNextResponseByIndex()
    {
        $expected = array("HTTP/1.1 200 OK\r\n\r\n",
                          "HTTP/1.1 302 Moved Temporarily\r\n\r\n",
                          "HTTP/1.1 404 Not Found\r\n\r\n");

        $this->adapter->setResponse($expected);
        $this->assertEquals($expected[0], $this->adapter->read());

        foreach ($expected as $i => $expected) {
            $this->adapter->setResponseIndex($i);
            $this->assertEquals($expected, $this->adapter->read());
        }
    }

    public function testSettingNextResponseToAnInvalidIndex()
    {
        $indexes = array(-1, 1);
        foreach ($indexes as $i) {
            try {
                $this->adapter->setResponseIndex($i);
                $this->fail();
            } catch (Exception $e) {
                $class = 'Zend_Http_Client_Adapter_Exception';
                $this->assertType($class, $e);
                $this->assertRegexp('/out of range/i', $e->getMessage());
            }
        }
    }
}
