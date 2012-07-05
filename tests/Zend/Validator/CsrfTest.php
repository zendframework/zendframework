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
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\Session\Configuration\StandardConfiguration;
use Zend\Session\Container;
use Zend\Validator\Csrf;

/**
 * Zend\Csrf
 *
 * @category   Zend
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $sessionConfig = new StandardConfiguration(array(
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

    public function testTimeoutIsMutable()
    {
        $this->validator->setTimeout(600);
        $this->assertEquals(600, $this->validator->getTimeout());
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
        $this->assertFalse(empty($hash));
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
}
