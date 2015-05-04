<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Authentication;

use Zend\Authentication\AuthenticationService;

/**
 * @group      Zend_Auth
 */
class AuthenticationServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->auth = new AuthenticationService();
    }

    /**
     * Ensures that getStorage() returns Zend_Auth_Storage_Session
     *
     * @return void
     */
    public function testGetStorage()
    {
        $storage = $this->auth->getStorage();
        $this->assertInstanceOf('Zend\Authentication\Storage\Session', $storage);
    }

    public function testAdapter()
    {
        $this->assertNull($this->auth->getAdapter());
        $successAdapter = new TestAsset\SuccessAdapter();
        $ret = $this->auth->setAdapter($successAdapter);
        $this->assertSame($ret, $this->auth);
        $this->assertSame($successAdapter, $this->auth->getAdapter());
    }

    /**
     * Ensures expected behavior for successful authentication
     *
     * @return void
     */
    public function testAuthenticate()
    {
        $result = $this->authenticate();
        $this->assertInstanceOf('Zend\Authentication\Result', $result);
        $this->assertTrue($this->auth->hasIdentity());
        $this->assertEquals('someIdentity', $this->auth->getIdentity());
    }

    public function testAuthenticateSetAdapter()
    {
        $result = $this->authenticate(new TestAsset\SuccessAdapter());
        $this->assertInstanceOf('Zend\Authentication\Result', $result);
        $this->assertTrue($this->auth->hasIdentity());
        $this->assertEquals('someIdentity', $this->auth->getIdentity());
    }

    /**
     * Ensures expected behavior for clearIdentity()
     *
     * @return void
     */
    public function testClearIdentity()
    {
        $this->authenticate();
        $this->auth->clearIdentity();
        $this->assertFalse($this->auth->hasIdentity());
        $this->assertEquals(null, $this->auth->getIdentity());
    }

    protected function authenticate($adapter = null)
    {
        if ($adapter === null) {
            $adapter = new TestAsset\SuccessAdapter();
        }
        return $this->auth->authenticate($adapter);
    }
}
