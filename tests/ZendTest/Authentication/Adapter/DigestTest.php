<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\Adapter;

use Zend\Authentication\Adapter;
use Zend\Authentication;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @group      Zend_Auth
 */
class DigestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filesPath = __DIR__ . '/TestAsset/Digest';
    }

    /**
     * Ensures that the adapter throws an exception when authentication is attempted before
     * setting a required option
     *
     * @return void
     */
    public function testOptionRequiredException()
    {
        $adapter = new Adapter\Digest();
        try {
            $adapter->authenticate();
            $this->fail('Expected Zend_Auth_Adapter_Exception not thrown upon authentication attempt before setting '
                      . 'a required option');
        } catch (Adapter\Exception\ExceptionInterface $e) {
            $this->assertContains('must be set before authentication', $e->getMessage());
        }
    }

    /**
     * Ensures that an exception is thrown upon authenticating against a nonexistent file
     *
     * @return void
     */
    public function testFileNonExistentException()
    {
        $adapter = new Adapter\Digest('nonexistent', 'realm', 'username', 'password');
        try {
            $adapter->authenticate();
            $this->fail('Expected Zend_Auth_Adapter_Exception not thrown upon authenticating against nonexistent '
                      . 'file');
        } catch (Adapter\Exception\ExceptionInterface $e) {
            $this->assertContains('Cannot open', $e->getMessage());
        }
    }

    /**
     * Ensures expected behavior upon realm not found for existing user
     *
     * @return void
     */
    public function testUserExistsRealmNonexistent()
    {
        $filename = "$this->_filesPath/htdigest.1";
        $realm    = 'Nonexistent Realm';
        $username = 'someUser';
        $password = 'somePassword';

        $adapter = new Adapter\Digest($filename, $realm, $username, $password);

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());

        $messages = $result->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals($result->getCode(), Authentication\Result::FAILURE_IDENTITY_NOT_FOUND);
        $this->assertContains('combination not found', $messages[0]);

        $identity = $result->getIdentity();
        $this->assertEquals($identity['realm'], $realm);
        $this->assertEquals($identity['username'], $username);
    }

    /**
     * Ensures expected behavior upon user not found in existing realm
     *
     * @return void
     */
    public function testUserNonexistentRealmExists()
    {
        $filename = "$this->_filesPath/htdigest.1";
        $realm    = 'Some Realm';
        $username = 'nonexistentUser';
        $password = 'somePassword';

        $adapter = new Adapter\Digest($filename, $realm, $username, $password);

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertEquals($result->getCode(), Authentication\Result::FAILURE_IDENTITY_NOT_FOUND);

        $messages = $result->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('combination not found', $messages[0]);

        $identity = $result->getIdentity();
        $this->assertEquals($identity['realm'], $realm);
        $this->assertEquals($identity['username'], $username);
    }

    /**
     * Ensures expected behavior upon incorrect password
     *
     * @return void
     */
    public function testIncorrectPassword()
    {
        $filename = "$this->_filesPath/htdigest.1";
        $realm    = 'Some Realm';
        $username = 'someUser';
        $password = 'incorrectPassword';

        $adapter = new Adapter\Digest($filename, $realm, $username, $password);

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertEquals($result->getCode(), Authentication\Result::FAILURE_CREDENTIAL_INVALID);

        $messages = $result->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('Password incorrect', $messages[0]);

        $identity = $result->getIdentity();
        $this->assertEquals($identity['realm'], $realm);
        $this->assertEquals($identity['username'], $username);
    }

    /**
     * Ensures that successful authentication works as expected
     *
     * @return void
     */
    public function testAuthenticationSuccess()
    {
        $filename = "$this->_filesPath/htdigest.1";
        $realm    = 'Some Realm';
        $username = 'someUser';
        $password = 'somePassword';

        $adapter = new Adapter\Digest($filename, $realm, $username, $password);

        $result = $adapter->authenticate();

        $this->assertTrue($result->isValid());
        $this->assertEquals($result->getCode(), Authentication\Result::SUCCESS);

        $this->assertEquals(array(), $result->getMessages());

        $identity = $result->getIdentity();
        $this->assertEquals($identity['realm'], $realm);
        $this->assertEquals($identity['username'], $username);
    }

    /**
     * Ensures that getFilename() returns expected default value
     *
     * @return void
     */
    public function testGetFilename()
    {
        $adapter = new Adapter\Digest();
        $this->assertEquals(null, $adapter->getFilename());
    }

    /**
     * Ensures that getRealm() returns expected default value
     *
     * @return void
     */
    public function testGetRealm()
    {
        $adapter = new Adapter\Digest();
        $this->assertEquals(null, $adapter->getRealm());
    }

    /**
     * Ensures that getUsername() returns expected default value
     *
     * @return void
     */
    public function testGetUsername()
    {
        $adapter = new Adapter\Digest();
        $this->assertEquals(null, $adapter->getUsername());
    }

    /**
     * Ensures that getPassword() returns expected default value
     *
     * @return void
     */
    public function testGetPassword()
    {
        $adapter = new Adapter\Digest();
        $this->assertEquals(null, $adapter->getPassword());
    }
}
