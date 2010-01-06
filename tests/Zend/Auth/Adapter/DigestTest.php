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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * Zend_Auth_Adapter_Digest
 */
require_once 'Zend/Auth/Adapter/Digest.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Auth
 */
class Zend_Auth_Adapter_DigestTest extends PHPUnit_Framework_TestCase
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
    public function __construct()
    {
        $this->_filesPath = dirname(__FILE__) . '/Digest/_files';
    }

    /**
     * Ensures that the adapter throws an exception when authentication is attempted before
     * setting a required option
     *
     * @return void
     */
    public function testOptionRequiredException()
    {
        $adapter = new Zend_Auth_Adapter_Digest();
        try {
            $adapter->authenticate();
            $this->fail('Expected Zend_Auth_Adapter_Exception not thrown upon authentication attempt before setting '
                      . 'a required option');
        } catch (Zend_Auth_Adapter_Exception $e) {
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
        $adapter = new Zend_Auth_Adapter_Digest('nonexistent', 'realm', 'username', 'password');
        try {
            $adapter->authenticate();
            $this->fail('Expected Zend_Auth_Adapter_Exception not thrown upon authenticating against nonexistent '
                      . 'file');
        } catch (Zend_Auth_Adapter_Exception $e) {
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
        $filename = "$this->_filesPath/.htdigest.1";
        $realm    = 'Nonexistent Realm';
        $username = 'someUser';
        $password = 'somePassword';

        $adapter = new Zend_Auth_Adapter_Digest($filename, $realm, $username, $password);

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());

        $messages = $result->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals($result->getCode(), Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND);
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
        $filename = "$this->_filesPath/.htdigest.1";
        $realm    = 'Some Realm';
        $username = 'nonexistentUser';
        $password = 'somePassword';

        $adapter = new Zend_Auth_Adapter_Digest($filename, $realm, $username, $password);

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertEquals($result->getCode(), Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND);

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
        $filename = "$this->_filesPath/.htdigest.1";
        $realm    = 'Some Realm';
        $username = 'someUser';
        $password = 'incorrectPassword';

        $adapter = new Zend_Auth_Adapter_Digest($filename, $realm, $username, $password);

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());
        $this->assertEquals($result->getCode(), Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID);

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
        $filename = "$this->_filesPath/.htdigest.1";
        $realm    = 'Some Realm';
        $username = 'someUser';
        $password = 'somePassword';

        $adapter = new Zend_Auth_Adapter_Digest($filename, $realm, $username, $password);

        $result = $adapter->authenticate();

        $this->assertTrue($result->isValid());
        $this->assertEquals($result->getCode(), Zend_Auth_Result::SUCCESS);

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
        $adapter = new Zend_Auth_Adapter_Digest();
        $this->assertEquals(null, $adapter->getFilename());
    }

    /**
     * Ensures that getRealm() returns expected default value
     *
     * @return void
     */
    public function testGetRealm()
    {
        $adapter = new Zend_Auth_Adapter_Digest();
        $this->assertEquals(null, $adapter->getRealm());
    }

    /**
     * Ensures that getUsername() returns expected default value
     *
     * @return void
     */
    public function testGetUsername()
    {
        $adapter = new Zend_Auth_Adapter_Digest();
        $this->assertEquals(null, $adapter->getUsername());
    }

    /**
     * Ensures that getPassword() returns expected default value
     *
     * @return void
     */
    public function testGetPassword()
    {
        $adapter = new Zend_Auth_Adapter_Digest();
        $this->assertEquals(null, $adapter->getPassword());
    }
}
