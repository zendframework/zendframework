<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Authentication;

use Zend\Authentication\AuthenticationService,
    Zend\Authentication as Auth;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $this->assertTrue($storage instanceof Auth\Storage\Session);
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
        $this->assertTrue($result instanceof Auth\Result);
        $this->assertTrue($this->auth->hasIdentity());
        $this->assertEquals('someIdentity', $this->auth->getIdentity());
    }

    public function testAuthenticateSetAdapter()
    {
        $result = $this->authenticate(new TestAsset\SuccessAdapter());
        $this->assertTrue($result instanceof Auth\Result);
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
