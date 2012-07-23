<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_DeveloperGarden
 */
class Zend_Service_DeveloperGarden_OfflineCredentialTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_OfflineCredential_Mock
     */
    protected $_service = null;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $this->service = new Zend_Service_DeveloperGarden_OfflineCredential_Mock();
    }

    public function testUserName()
    {
        $this->assertNull($this->service->getUsername());
        $this->assertNotNull(
            $this->service->setUsername(TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN)
        );
        $this->assertEquals(
            TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            $this->service->getUsername()
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testInvalidUsername()
    {
        $this->service->setUsername(null);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testInvalidUsernameType()
    {
        $this->service->setUsername(1000);
    }

    public function testUsernameWithRealmDefault()
    {
        $username = $this->service->getUsername();
        $realm = $this->service->getRealm();
        $str = "$username@$realm";
        $this->assertEquals($str, $this->service->getUsername(true));
    }

    public function testUsernameWithRealmCustomized()
    {
        $username = 'MyOwnUsername';
        $realm = 'framework.zend.com';
        $str = "$username@$realm";
        $this->assertNotNull(
            $this->service->setUsername($username)
        );
        $this->assertNotNull(
            $this->service->setRealm($realm)
        );
        $this->assertEquals($str, $this->service->getUsername(true));
    }

    public function testPassword()
    {
        $this->assertNull($this->service->getPassword());
        $this->assertNotNull(
            $this->service->setPassword(TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN)
        );
        $this->assertEquals(
            TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            $this->service->getPassword()
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testInvalidPassword()
    {
        $this->service->setPassword(null);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testInvalidPasswordType()
    {
        $this->service->setPassword(1000);
    }

    public function testRealm()
    {
        $this->assertEquals('t-online.de', $this->service->getRealm());
        $this->assertNotNull($this->service->setRealm('framework.zend.com'));
        $this->assertEquals('framework.zend.com', $this->service->getRealm());
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testInvalidRealm()
    {
        $this->service->setRealm(null);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testInvalidRealmType()
    {
        $this->service->setRealm(1000);
    }

    public function testSetGetUsername()
    {
        $this->assertNull($this->service->getUsername());

        $this->assertNotNull(
            $this->service->setUsername('MarcoKaiser')
        );
        $this->assertEquals('MarcoKaiser', $this->service->getUsername());
    }

    public function testSetGetPassword()
    {
        $this->assertNull($this->service->getPassword());

        $this->assertNotNull(
            $this->service->setPassword('S3cr37P4ssw0rd')
        );
        $this->assertEquals('S3cr37P4ssw0rd', $this->service->getPassword());
    }

    public function testConstructor()
    {
        $username = 'MyUser';
        $password = 'MyPassword';
        $realm = 'Zend.Com';

        $service = new Zend_Service_DeveloperGarden_OfflineCredential_Mock($username, $password, $realm);
        $this->assertEquals($username, $service->getUsername());
        $this->assertEquals($password, $service->getPassword());
        $this->assertEquals($realm, $service->getRealm());
    }
}

class Zend_Service_DeveloperGarden_OfflineCredential_Mock
    extends Zend_Service_DeveloperGarden_Credential
{

}
