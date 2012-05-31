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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Amf;
use Zend\Acl,
    Zend\Amf\Value,
    Zend\Amf\Value\Messaging,
    Zend\Amf\Request,
    Zend\Acl\Role,
    Zend\Amf,
    Zend\Authentication;

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Enter description here...
     *
     * @var Zend_Amf_Server
     */
    protected $_server;

    public function setUp()
    {
        $this->_server = new Amf\Server();
        $this->_server->setProduction(false);
        \Zend\Amf\Parser\TypeLoader::resetMap();
        $this->_acl = new Acl\Acl();
    }

    protected function tearDown()
    {
        unset($this->_server);
    }
    protected function _addServiceCall($request, $class = 'ZendTest\\Amf\\TestAsset\\Authentication\\testclass', $method = 'hello')
    {
        $data[] = "12345";
        $this->_server->setClass($class);
        $newBody = new Value\MessageBody("$class.$method","/1",$data);
        $request->addAmfBody($newBody);
    }

    protected function _addLogin($request, $username, $password)
    {
        $cmdBody = new Value\MessageBody("","/1","");
        $loginCmd = new Messaging\CommandMessage();
        $cmdBody->setData($loginCmd);
        $loginCmd->operation = Messaging\CommandMessage::LOGIN_OPERATION;
        $loginCmd->body = "$username:$password";
        $request->addAmfBody($cmdBody);
    }

    protected function _addLogout($request)
    {
        $cmdBody = new Value\MessageBody("","/1","");
        $loginCmd = new Messaging\CommandMessage();
        $cmdBody->setData($loginCmd);
        $loginCmd->operation = Messaging\CommandMessage::LOGOUT_OPERATION;
        $request->addAmfBody($cmdBody);
    }

    protected function _callService($class = 'ZendTest\\Amf\\TestAsset\\Authentication\\testclass', $method = 'hello')
    {
        $request = new Request\StreamRequest();
        $request->setObjectEncoding(0x03);
        $this->_addServiceCall($request, $class, $method);
        $this->_server->handle($request);
        $response = $this->_server->getResponse();
        $responseBody = $response->getAmfBodies();
        return $responseBody[0]->getData();
    }

    protected function _callServiceAuth($username, $password, $class = 'ZendTest\\Amf\\TestAsset\\Authentication\\testclass', $method = 'hello')
    {
        $request = new Request\StreamRequest();
        $request->setObjectEncoding(0x03);
        $this->_addLogin($request, $username, $password);
        $this->_addServiceCall($request, $class, $method);
        $this->_server->handle($request);
        return $this->_server->getResponse()->getAmfBodies();
    }

    public function testService()
    {
        $resp = $this->_callService();
        $this->assertContains("hello", $resp);
    }


    public function testUnauthenticated()
    {
        $this->_server->setAuth(new TestAsset\Authentication\WrongPassword());
        $this->_server->setAcl($this->_acl);
        $data = $this->_callService();
        $this->assertTrue($data instanceof Messaging\ErrorMessage);
        $this->assertContains("not allowed", $data->faultString);
    }

    public function testAnonymousDenied()
    {
        $this->_server->setAuth(new TestAsset\Authentication\WrongPassword());
        $this->_acl->addRole(new Role\GenericRole(Amf\Constants::GUEST_ROLE));
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callService();
        $this->assertTrue($resp instanceof Messaging\ErrorMessage);
        $this->assertContains("not allowed", $resp->faultString);
    }

    public function testAnonymousOK()
    {
        $this->_server->setAuth(new TestAsset\Authentication\WrongPassword());
        $this->_acl->addRole(new Role\GenericRole(Amf\Constants::GUEST_ROLE));
        $this->_acl->allow(Amf\Constants::GUEST_ROLE, null, null);
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callService();
        $this->assertContains("hello", $resp);
    }

    public function testNoUsername()
    {
        $this->_server->setAuth(new TestAsset\Authentication\WrongPassword());
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("", "");
        $data = $resp[0]->getData();
        $this->assertTrue($data instanceof Messaging\ErrorMessage);
        $this->assertContains("username not supplied", $data->faultString);
    }

    public function testWrongPassword()
    {
        $this->_server->setAuth(new TestAsset\Authentication\WrongPassword());
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("testuser", "");
        $data = $resp[0]->getData();
        $this->assertTrue($data instanceof Messaging\ErrorMessage);
        $this->assertContains("Wrong Password", $data->faultString);
    }

    public function testRightPassword()
    {
        $this->_server->setAuth(new TestAsset\Authentication\RightPassword("testuser", "testrole"));
        $this->_acl->addRole(new Role\GenericRole("testrole"));
        $this->_acl->allow("testrole", null, null);
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("testuser", "");
        $this->assertTrue($resp[0]->getData() instanceof Messaging\AcknowledgeMessage);
        $this->assertContains("hello", $resp[1]->getData());
    }

    // no ACL to allow access to this method
    public function testNoAcl()
    {
        $this->_server->setAuth(new TestAsset\Authentication\RightPassword("testuser", "testrole"));
        $this->_acl->addRole(new Role\GenericRole("testrole"));
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("testuser", "");
        $this->assertTrue($resp[0]->getData() instanceof Messaging\AcknowledgeMessage);
        $data = $resp[1]->getData();
        $this->assertTrue($data instanceof Messaging\ErrorMessage);
        $this->assertContains("not allowed", $data->faultString);
    }

    // Class allows everybody to access, even though no ACL is defined
    public function testNoClassAcl()
    {
        $this->_server->setAuth(new TestAsset\Authentication\RightPassword("testuser", "testrole"));
        $this->_acl->addRole(new Role\GenericRole("testrole"));
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("testuser", "", 'ZendTest\\Amf\\TestAsset\\Authentication\\NoAcl');
        $this->assertTrue($resp[0]->getData() instanceof Messaging\AcknowledgeMessage);
        $this->assertTrue(isset($resp[1]));
        $this->assertTrue(is_object($resp[1]));
        $this->assertTrue(method_exists($resp[1], 'getData'));
        $data = $resp[1]->getData();
        $this->assertInternalType('string', $data);
        $this->assertContains("hello", $data);
    }

    // Class-defined ACL
    public function testClassAclAllowed()
    {
        $this->_server->setAuth(new TestAsset\Authentication\RightPassword("testuser", "testrole"));
        $this->_acl->addRole(new Role\GenericRole("testrole"));
        $this->_acl->addRole(new Role\GenericRole("testrole2"));
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("testuser", "", 'ZendTest\\Amf\\TestAsset\\Authentication\\Acl');
        $this->assertTrue($resp[0]->getData() instanceof Messaging\AcknowledgeMessage);
        $this->assertContains("hello", $resp[1]->getData());
    }

    // Class-defined ACL
    public function testClassAclDenied()
    {
        $this->_server->setAuth(new TestAsset\Authentication\RightPassword("testuser", "testrole2"));
        $this->_acl->addRole(new Role\GenericRole("testrole"));
        $this->_acl->addRole(new Role\GenericRole("testrole2"));
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("testuser", "", 'ZendTest\\Amf\\TestAsset\\Authentication\\Acl');
        $this->assertTrue($resp[0]->getData() instanceof Messaging\AcknowledgeMessage);
        $data = $resp[1]->getData();
        $this->assertTrue($data instanceof Messaging\ErrorMessage);
        $this->assertContains("not allowed", $data->faultString);
    }

    // Class-defined ACL
    public function testClassAclAllowed2()
    {
        $this->_server->setAuth(new TestAsset\Authentication\RightPassword("testuser", "testrole2"));
        $this->_acl->addRole(new Role\GenericRole("testrole"));
        $this->_acl->addRole(new Role\GenericRole("testrole2"));
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("testuser", "", 'ZendTest\\Amf\\TestAsset\\Authentication\\Acl', 'hello2');
        $this->assertTrue($resp[0]->getData() instanceof Messaging\AcknowledgeMessage);
        $this->assertContains("hello", $resp[1]->getData());
    }

    public function testLogout()
    {
        $this->_server->setAuth(new TestAsset\Authentication\RightPassword("testuser", "testrole"));
        $this->_acl->addRole(new Role\GenericRole("testrole"));
        $this->_acl->allow("testrole", null, null);
        $this->_server->setAcl($this->_acl);
        $resp = $this->_callServiceAuth("testuser", "");
        $this->assertTrue($resp[0]->getData() instanceof Messaging\AcknowledgeMessage);
        $this->assertContains("hello", $resp[1]->getData());

        // After logout same request should not be allowed
        $this->setUp();
        $this->_server->setAuth(new TestAsset\Authentication\RightPassword("testuser", "testrole"));
        $this->_server->setAcl($this->_acl);
        $request = new Request\StreamRequest();
        $request->setObjectEncoding(0x03);
        $this->_addLogout($request);
        $this->_addServiceCall($request);
        $this->_server->handle($request);
        $resp = $this->_server->getResponse()->getAmfBodies();

        $this->assertTrue($resp[0]->getData() instanceof Messaging\AcknowledgeMessage);
        $data = $resp[1]->getData();
        $this->assertTrue($data instanceof Messaging\ErrorMessage);
        $this->assertContains("not allowed", $data->faultString);
    }
}
