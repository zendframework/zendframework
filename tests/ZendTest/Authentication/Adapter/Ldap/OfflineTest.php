<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\Adapter\Ldap;

use Zend\Authentication\Adapter;
use Zend\Ldap;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @group      Zend_Auth
 */
class OfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Authentication adapter instance
     *
     * @var Adapter\Ldap
     */
    protected $adapter = null;

    /**
     * Setup operations run prior to each test method:
     *
     * * Creates an instance of Zend\Authentication\Adapter\Ldap
     *
     * @return void
     */
    public function setUp()
    {
        $this->adapter = new Adapter\Ldap();
    }

    public function testGetSetLdap()
    {
        if (!extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP is not enabled');
        }
        $this->adapter->setLdap(new Ldap\Ldap());
        $this->assertInstanceOf('Zend\Ldap\Ldap', $this->adapter->getLdap());
    }

    public function testUsernameIsNullIfNotSet()
    {
        $this->assertNull($this->adapter->getUsername());
    }

    public function testPasswordIsNullIfNotSet()
    {
        $this->assertNull($this->adapter->getPassword());
    }

    public function testSetAndGetUsername()
    {
        $usernameExpected = 'someUsername';
        $usernameActual = $this->adapter->setUsername($usernameExpected)
                                         ->getUsername();
        $this->assertSame($usernameExpected, $usernameActual);
    }

    public function testSetAndGetPassword()
    {
        $passwordExpected = 'somePassword';
        $passwordActual = $this->adapter->setPassword($passwordExpected)
                                         ->getPassword();
        $this->assertSame($passwordExpected, $passwordActual);
    }

    public function testSetIdentityProxiesToSetUsername()
    {
        $usernameExpected = 'someUsername';
        $usernameActual = $this->adapter->setIdentity($usernameExpected)
                                         ->getUsername();
        $this->assertSame($usernameExpected, $usernameActual);
    }

    public function testSetCredentialProxiesToSetPassword()
    {
        $passwordExpected = 'somePassword';
        $passwordActual = $this->adapter->setCredential($passwordExpected)
                                         ->getPassword();
        $this->assertSame($passwordExpected, $passwordActual);
    }
}
