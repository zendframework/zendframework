<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Authentication\Adapter;

use Exception;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Authentication\Adapter\Callback;
use Zend\Authentication\Result;

class CallbackTest extends TestCase
{
    /**
     * Callback authentication adapter
     *
     * @var Callback
     */
    protected $adapter = null;

    /**
     * Set up test configuration
     */
    public function setUp()
    {
        $this->setupAuthAdapter();
    }

    public function tearDown()
    {
        $this->adapter = null;
    }

    protected function setupAuthAdapter()
    {
        $this->adapter = new Callback();
    }

    /**
     * Ensures expected behavior for an invalid callback
     */
    public function testSetCallbackThrowsException()
    {
        $this->setExpectedException(
            'Zend\Authentication\Exception\InvalidArgumentException',
            'Invalid callback provided'
        );
        $this->adapter->setCallback('This is not a valid callback');
    }

    /**
     * Ensures setter/getter behaviour for callback
     */
    public function testCallbackSetGetMethods()
    {
        $callback = function () {
        };
        $this->adapter->setCallback($callback);
        $this->assertEquals($callback, $this->adapter->getCallback());
    }

    /**
     * Ensures constructor sets callback if provided
     */
    public function testClassConstructorSetCallback()
    {
        $callback = function () {
        };
        $adapter  = new Callback($callback);
        $this->assertEquals($callback, $adapter->getCallback());
    }

    /**
     * Ensures authenticate throws Exception if no callback is defined
     */
    public function testAuthenticateThrowsException()
    {
        $this->setExpectedException(
            'Zend\Authentication\Exception\RuntimeException',
            'No callback provided'
        );
        $this->adapter->authenticate();
    }

    /**
     * Ensures identity and credential are provided as arguments to callback
     */
    public function testAuthenticateProvidesCallbackWithIdentityAndCredentials()
    {
        $adapter = $this->adapter;
        $adapter->setIdentity('testIdentity');
        $adapter->setCredential('testCredential');
        $that = $this;
        $callback = function ($identity, $credential) use ($that, $adapter) {
            $that->assertEquals($identity, $adapter->getIdentity());
            $that->assertEquals($credential, $adapter->getCredential());
        };
        $this->adapter->setCallback($callback);
        $this->adapter->authenticate();
    }

    /**
     * Ensures authentication result is invalid when callback throws exception
     */
    public function testAuthenticateResultIfCallbackThrows()
    {
        $adapter   = $this->adapter;
        $exception = new Exception('Callback Exception');
        $callback  = function () use ($exception) {
            throw $exception;
        };
        $adapter->setCallback($callback);
        $result = $adapter->authenticate();
        $this->assertFalse($result->isValid());
        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals(array($exception->getMessage()), $result->getMessages());
    }

    /**
     * Ensures authentication result is invalid when callback returns falsy value
     */
    public function testAuthenticateResultIfCallbackReturnsFalsy()
    {
        $that    = $this;
        $adapter = $this->adapter;
        $falsyValues = array(false, null, '', '0', array(), 0, 0.0);
        array_map(function ($falsy) use ($that, $adapter) {
            $callback = function () use ($falsy) {
                return $falsy;
            };
            $adapter->setCallback($callback);
            $result = $adapter->authenticate();
            $that->assertFalse($result->isValid());
            $that->assertEquals(Result::FAILURE, $result->getCode());
            $that->assertEquals(array('Authentication failure'), $result->getMessages());
        }, $falsyValues);
    }

    /**
     * Ensures authentication result is valid when callback returns truthy value
     */
    public function testAuthenticateResultIfCallbackReturnsIdentity()
    {
        $adapter  = $this->adapter;
        $callback = function () {
            return 'identity';
        };
        $adapter->setCallback($callback);
        $result = $adapter->authenticate();
        $this->assertTrue($result->isValid());
        $this->assertEquals(Result::SUCCESS, $result->getCode());
        $this->assertEquals('identity', $result->getIdentity());
        $this->assertEquals(array('Authentication success'), $result->getMessages());
    }
}
