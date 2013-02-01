<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\Validator;

use Zend\Authentication\Validator\Authentication as AuthenticationValidator;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication as Auth;
use ZendTest\Authentication as AuthTest;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class AthenticationTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new AuthenticationValidator();
        $this->authService = new AuthenticationService();
        $this->authAdapter = new AuthTest\TestAsset\SuccessAdapter();
    }

    public function testOptions()
    {
        $auth = new AuthenticationValidator(array(
            'adapter' => $this->authAdapter,
            'service' => $this->authService,
            'identity' => 'username',
            'credential' => 'password',
        ));
        $this->assertSame($auth->getAdapter(), $this->authAdapter);
        $this->assertSame($auth->getService(), $this->authService);
        $this->assertSame($auth->getIdentity(), 'username');
        $this->assertSame($auth->getCredential(), 'password');
    }

    public function testSetters()
    {
        $this->validator->setAdapter($this->authAdapter);
        $this->validator->setService($this->authService);
        $this->validator->setIdentity('username');
        $this->validator->setCredential('credential');
        $this->assertSame($this->validator->getAdapter(), $this->authAdapter);
        $this->assertSame($this->validator->getService(), $this->authService);
        $this->assertSame($this->validator->getIdentity(), 'username');
        $this->assertSame($this->validator->getCredential(), 'credential');
    }

    public function testNoIdentityThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException', 'Identity must be set prior to validation');
        $this->validator->isValid('password');
    }

    public function testNoAdapterThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException', 'Adapter must be set prior to validation');
        $this->validator->setIdentity('username');
        $this->validator->isValid('password');
    }

    public function testNoServiceThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException', 'AuthenticationService must be set prior to validation');
        $this->validator->setIdentity('username');
        $this->validator->setAdapter($this->authAdapter);
        $this->validator->isValid('password');
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testWithoutContext()
    {
        $this->validator->setAdapter($this->authAdapter);
        $this->validator->setService($this->authService);
        $this->validator->setIdentity('username');
        $this->validator->setCredential('credential');

        $this->assertEquals('username', $this->validator->getIdentity());
        $this->assertEquals('credential', $this->validator->getCredential());
        $this->assertTrue($this->validator->isValid());
    }

    public function testWithContext()
    {
        $this->validator->setAdapter($this->authAdapter);
        $this->validator->setService($this->authService);
        $this->validator->setIdentity('username');
        $this->validator->isValid('password', array(
            'username' => 'myusername',
            'password' => 'mypassword',
        ));
        $adapter = $this->validator->getAdapter();
        $this->assertEquals('myusername', $adapter->getIdentity());
        $this->assertEquals('mypassword', $adapter->getCredential());
    }
}
