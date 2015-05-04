<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;
use Zend\Validator\Csrf;

/**
 * Zend\Csrf
 *
 * @group      Zend_Validator
 */
class CsrfTest extends \PHPUnit_Framework_TestCase
{
    /** @var Csrf */
    public $validator;

    /** @var TestAsset\SessionManager */
    public $sessionManager;

    public function setUp()
    {
        // Setup session handling
        $_SESSION = array();
        $sessionConfig = new StandardConfig(array(
            'storage' => 'Zend\Session\Storage\ArrayStorage',
        ));
        $sessionManager       = new TestAsset\SessionManager($sessionConfig);
        $this->sessionManager = $sessionManager;
        Container::setDefaultManager($sessionManager);

        $this->validator = new Csrf;
    }

    public function tearDown()
    {
        $_SESSION = array();
        Container::setDefaultManager(null);
    }

    public function testSaltHasDefaultValueIfNotSet()
    {
        $this->assertEquals('salt', $this->validator->getSalt());
    }

    public function testSaltIsMutable()
    {
        $this->validator->setSalt('pepper');
        $this->assertEquals('pepper', $this->validator->getSalt());
    }

    public function testSessionContainerIsLazyLoadedIfNotSet()
    {
        $container = $this->validator->getSession();
        $this->assertInstanceOf('Zend\Session\Container', $container);
    }

    public function testSessionContainerIsMutable()
    {
        $container = new Container('foo', $this->sessionManager);
        $this->validator->setSession($container);
        $this->assertSame($container, $this->validator->getSession());
    }

    public function testNameHasDefaultValue()
    {
        $this->assertEquals('csrf', $this->validator->getName());
    }

    public function testNameIsMutable()
    {
        $this->validator->setName('foo');
        $this->assertEquals('foo', $this->validator->getName());
    }

    public function testTimeoutHasDefaultValue()
    {
        $this->assertEquals(300, $this->validator->getTimeout());
    }

    public function timeoutValuesDataProvider()
    {
        return array(
            //    timeout  expected
            array(600,     600),
            array(null,    null),
            array("0",     0),
            array("100",   100),
        );
    }

    /**
     * @dataProvider timeoutValuesDataProvider
     */
    public function testTimeoutIsMutable($timeout, $expected)
    {
        $this->validator->setTimeout($timeout);
        $this->assertEquals($expected, $this->validator->getTimeout());
    }

    public function testAllOptionsMayBeSetViaConstructor()
    {
        $container = new Container('foo', $this->sessionManager);
        $options   = array(
            'name'    => 'hash',
            'salt'    => 'hashful',
            'session' => $container,
            'timeout' => 600,
        );
        $validator = new Csrf($options);
        foreach ($options as $key => $value) {
            if ($key == 'session') {
                $this->assertSame($container, $value);
                continue;
            }
            $method = 'get' . $key;
            $this->assertEquals($value, $validator->$method());
        }
    }

    public function testHashIsGeneratedOnFirstRetrieval()
    {
        $hash = $this->validator->getHash();
        $this->assertNotEmpty($hash);
        $test = $this->validator->getHash();
        $this->assertEquals($hash, $test);
    }

    public function testSessionNameIsDerivedFromClassSaltAndName()
    {
        $class = get_class($this->validator);
        $class = str_replace('\\', '_', $class);
        $expected = sprintf('%s_%s_%s', $class, $this->validator->getSalt(), $this->validator->getName());
        $this->assertEquals($expected, $this->validator->getSessionName());
    }

    public function testSessionNameRemainsValidForElementBelongingToFieldset()
    {
        $this->validator->setName('fieldset[csrf]');
        $class = get_class($this->validator);
        $class = str_replace('\\', '_', $class);
        $name = strtr($this->validator->getName(), array('[' => '_', ']' => ''));
        $expected = sprintf('%s_%s_%s', $class, $this->validator->getSalt(), $name);
        $this->assertEquals($expected, $this->validator->getSessionName());
    }

    public function testIsValidReturnsFalseWhenValueDoesNotMatchHash()
    {
        $this->assertFalse($this->validator->isValid('foo'));
    }

    public function testValidationErrorMatchesNotSameConstantAndRelatedMessage()
    {
        $this->validator->isValid('foo');
        $messages = $this->validator->getMessages();
        $this->assertArrayHasKey(Csrf::NOT_SAME, $messages);
        $this->assertEquals("The form submitted did not originate from the expected site", $messages[Csrf::NOT_SAME]);
    }

    public function testIsValidReturnsTrueWhenValueMatchesHash()
    {
        $hash = $this->validator->getHash();
        $this->assertTrue($this->validator->isValid($hash));
    }

    public function testSessionContainerContainsHashAfterHashHasBeenGenerated()
    {
        $hash        = $this->validator->getHash();
        $container   = $this->validator->getSession();
        $test        = $container->hash; // Doing this, as expiration hops are 1; have to grab on first access
        $this->assertEquals($hash, $test);
    }

    public function testSettingNewSessionContainerSetsHashInNewContainer()
    {
        $hash        = $this->validator->getHash();
        $container   = new Container('foo', $this->sessionManager);
        $this->validator->setSession($container);
        $test        = $container->hash; // Doing this, as expiration hops are 1; have to grab on first access
        $this->assertEquals($hash, $test);
    }

    public function testMultipleValidatorsSharingContainerGenerateDifferentHashes()
    {
        $validatorOne = new Csrf();
        $validatorTwo = new Csrf();

        $containerOne = $validatorOne->getSession();
        $containerTwo = $validatorOne->getSession();

        $this->assertSame($containerOne, $containerTwo);

        $hashOne = $validatorOne->getHash();
        $hashTwo = $validatorTwo->getHash();
        $this->assertNotEquals($hashOne, $hashTwo);
    }

    public function testCanValidateAnyHashWithinTheSameContainer()
    {
        $validatorOne = new Csrf();
        $validatorTwo = new Csrf();

        $hashOne = $validatorOne->getHash();
        $hashTwo = $validatorTwo->getHash();

        $this->assertTrue($validatorOne->isValid($hashOne));
        $this->assertTrue($validatorOne->isValid($hashTwo));
        $this->assertTrue($validatorTwo->isValid($hashOne));
        $this->assertTrue($validatorTwo->isValid($hashTwo));
    }

    public function testCannotValidateHashesOfOtherContainers()
    {
        $validatorOne = new Csrf();
        $validatorTwo = new Csrf(array('name' => 'foo'));

        $containerOne = $validatorOne->getSession();
        $containerTwo = $validatorTwo->getSession();

        $this->assertNotSame($containerOne, $containerTwo);

        $hashOne = $validatorOne->getHash();
        $hashTwo = $validatorTwo->getHash();

        $this->assertTrue($validatorOne->isValid($hashOne));
        $this->assertFalse($validatorOne->isValid($hashTwo));
        $this->assertFalse($validatorTwo->isValid($hashOne));
        $this->assertTrue($validatorTwo->isValid($hashTwo));
    }

    public function testCannotReValidateAnExpiredHash()
    {
        $hash = $this->validator->getHash();

        $this->assertTrue($this->validator->isValid($hash));

        $this->sessionManager->getStorage()->setMetadata(
            $this->validator->getSession()->getName(),
            array('EXPIRE' => $_SERVER['REQUEST_TIME'] - 18600)
        );

        $this->assertFalse($this->validator->isValid($hash));
    }

    public function testCanValidateHasheWithoutId()
    {
        $method = new \ReflectionMethod(get_class($this->validator), 'getTokenFromHash');
        $method->setAccessible(true);

        $hash = $this->validator->getHash();
        $bareToken = $method->invoke($this->validator, $hash);

        $this->assertTrue($this->validator->isValid($bareToken));
    }

    public function fakeValuesDataProvider()
    {
        return array(
            array(''),
            array('-fakeTokenId'),
            array('fakeTokenId-fakeTokenId'),
            array('fakeTokenId-'),
            array('fakeTokenId'),
            array(md5(uniqid()) . '-'),
            array(md5(uniqid()) . '-' . md5(uniqid())),
            array('-' . md5(uniqid()))
        );
    }

    /**
     * @dataProvider fakeValuesDataProvider
     */
    public function testWithFakeValues($value)
    {
        $validator = new Csrf();
        $this->assertFalse($validator->isValid($value));
    }
}
