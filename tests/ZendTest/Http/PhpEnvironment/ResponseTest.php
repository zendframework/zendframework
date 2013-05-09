<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    Zend_Http
 * @subpackage UnitTest
 */

namespace ZendTest\Http\PhpEnvironment;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\PhpEnvironment\Response;

class ResponseTest extends TestCase
{
    /**
     * Original environemnt
     *
     * @var array
     */
    protected $originalEnvironment;

    /**
     * Save the original environment and set up a clean one.
     */
    public function setUp()
    {
        $this->originalEnvironment = array(
            'post'   => $_POST,
            'get'    => $_GET,
            'cookie' => $_COOKIE,
            'server' => $_SERVER,
            'env'    => $_ENV,
            'files'  => $_FILES,
        );

        $_POST   = array();
        $_GET    = array();
        $_COOKIE = array();
        $_SERVER = array();
        $_ENV    = array();
        $_FILES  = array();
    }

    /**
     * Restore the original environment
     */
    public function tearDown()
    {
        $_POST   = $this->originalEnvironment['post'];
        $_GET    = $this->originalEnvironment['get'];
        $_COOKIE = $this->originalEnvironment['cookie'];
        $_SERVER = $this->originalEnvironment['server'];
        $_ENV    = $this->originalEnvironment['env'];
        $_FILES  = $this->originalEnvironment['files'];
    }

    public function testReturnsOneOhVersionWhenDetectedInServerSuperglobal()
    {
        // HTTP/1.0
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $response = new Response();
        $this->assertSame(Response::VERSION_10, $response->getVersion());
    }

    public function testReturnsOneOneVersionWhenDetectedInServerSuperglobal()
    {
        // HTTP/1.1
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $response = new Response();
        $this->assertSame(Response::VERSION_11, $response->getVersion());
    }

    public function testFallsBackToVersionOneOhWhenServerSuperglobalVersionIsNotRecognized()
    {
        // unknown protocol or version -> fallback to HTTP/1.0
        $_SERVER['SERVER_PROTOCOL'] = 'zf/2.0';
        $response = new Response();
        $this->assertSame(Response::VERSION_10, $response->getVersion());
    }

    public function testFallsBackToVersionOneOhWhenNoVersionDetectedInServerSuperglobal()
    {
        // undefined -> fallback to HTTP/1.0
        unset($_SERVER['SERVER_PROTOCOL']);
        $response = new Response();
        $this->assertSame(Response::VERSION_10, $response->getVersion());
    }

    public function testCanExplicitlySetVersion()
    {
        $response = new Response();
        $response->setVersion(Response::VERSION_11);
        $this->assertSame(Response::VERSION_11, $response->getVersion());

        $response->setVersion(Response::VERSION_10);
        $this->assertSame(Response::VERSION_10, $response->getVersion());

        $this->setExpectedException('Zend\Http\Exception\InvalidArgumentException');
        $response->setVersion('zf/2.0');
    }
}
