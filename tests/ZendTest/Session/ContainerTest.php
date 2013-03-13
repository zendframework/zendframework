<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session;

use Zend\Session\Container;
use Zend\Session\Config\StandardConfig;
use Zend\Session\ManagerInterface as Manager;
use Zend\Session;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->forceAutoloader();
        $_SESSION = array();
        Container::setDefaultManager(null);

        $config = new StandardConfig(array(
            'storage' => 'Zend\\Session\\Storage\\ArrayStorage',
        ));

        $this->manager = $manager = new TestAsset\TestManager($config);
        $this->container = new Container('Default', $manager);
    }

    public function tearDown()
    {
        $_SESSION = array();
        Container::setDefaultManager(null);
    }

    protected function forceAutoloader()
    {
        $splAutoloadFunctions = spl_autoload_functions();
        if (!$splAutoloadFunctions || !in_array('ZendTest_Autoloader', $splAutoloadFunctions)) {
            include __DIR__ . '/../../_autoload.php';
        }
    }

    /**
     * Hack to allow running tests in separate processes
     *
     * @see    http://matthewturland.com/2010/08/19/process-isolation-in-phpunit/
     * @param  \PHPUnit_Framework_TestResult $result
     * @return void
     */
    public function run(\PHPUnit_Framework_TestResult $result = NULL)
    {
        $this->setPreserveGlobalState(false);

        return parent::run($result);
    }

    public function testInstantiationStartsSession()
    {
        $this->manager->destroy();
        $container = new Container('Default', $this->manager);
        $this->assertTrue($this->manager->started);
    }

    public function testInstantiatingContainerWithoutNameUsesDefaultAsName()
    {
        $this->assertEquals('Default', $this->container->getName());
    }

    public function testPassingNameToConstructorInstantiatesContainerWithThatName()
    {
        $container = new Container('foo', $this->manager);
        $this->assertEquals('foo', $container->getName());
    }

    public function testUsingOldZF1NameIsStillValid()
    {
        $container = new Container('Zend_Foo', $this->manager);
        $this->assertEquals('Zend_Foo', $container->getName());
    }

    public function testUsingNewZF2NamespaceIsValid()
    {
        $container = new Container('Zend\Foo', $this->manager);
        $this->assertEquals('Zend\Foo', $container->getName());
    }

    public function testPassingInvalidNameToConstructorRaisesException()
    {
        $tries = array(
            'f!',
            'foo bar',
            '_foo',
            '__foo',
            '0foo',
            '\foo',
            '\\foo'
        );
        foreach ($tries as $try) {
            try {
                $container = new Container($try);
                $this->fail('Invalid container name should raise exception');
            } catch (\Zend\Session\Exception\InvalidArgumentException $e) {
                $this->assertContains('invalid', $e->getMessage());
            }
        }
    }

    public function testContainerActsAsArray()
    {
        $this->container['foo'] = 'bar';
        $this->assertTrue(isset($this->container['foo']));
        $this->assertEquals('bar', $this->container['foo']);
        unset($this->container['foo']);
        $this->assertFalse(isset($this->container['foo']));
    }

    public function testContainerActsAsObject()
    {
        $this->container->foo = 'bar';
        $this->assertTrue(isset($this->container->foo));
        $this->assertEquals('bar', $this->container->foo);
        unset($this->container->foo);
        $this->assertFalse(isset($this->container->foo));
    }

    public function testDefaultManagerIsAlwaysPopulated()
    {
        $manager = Container::getDefaultManager();
        $this->assertTrue($manager instanceof Manager);
    }

    public function testCanSetDefaultManager()
    {
        $manager = new TestAsset\TestManager;
        Container::setDefaultManager($manager);
        $this->assertSame($manager, Container::getDefaultManager());
    }

    public function testCanSetDefaultManagerToNull()
    {
        $manager = new TestAsset\TestManager;
        Container::setDefaultManager($manager);
        Container::setDefaultManager(null);
        $this->assertNotSame($manager, Container::getDefaultManager());
    }

    public function testDefaultManagerUsedWhenNoManagerProvided()
    {
        $manager = Container::getDefaultManager();
        $container = new Container();
        $this->assertSame($manager, $container->getManager());
    }

    public function testContainerInstantiatesManagerWithDefaultsWhenNotInjected()
    {
        $container = new Container();
        $manager   = $container->getManager();
        $this->assertTrue($manager instanceof Manager);
        $config  = $manager->getConfig();
        $this->assertTrue($config instanceof Session\Config\SessionConfig);
        $storage = $manager->getStorage();
        $this->assertTrue($storage instanceof Session\Storage\SessionArrayStorage);
    }

    public function testContainerAllowsInjectingManagerViaConstructor()
    {
        $config = new StandardConfig(array(
            'storage' => 'Zend\\Session\\Storage\\ArrayStorage',
        ));
        $manager = new TestAsset\TestManager($config);
        $container = new Container('Foo', $manager);
        $this->assertSame($manager, $container->getManager());
    }

    public function testContainerWritesToStorage()
    {
        $this->container->foo = 'bar';
        $storage = $this->manager->getStorage();
        $this->assertTrue(isset($storage['Default']));
        $this->assertTrue(isset($storage['Default']['foo']));
        $this->assertEquals('bar', $storage['Default']['foo']);

        unset($this->container->foo);
        $this->assertFalse(isset($storage['Default']['foo']));
    }

    public function testSettingExpirationSecondsUpdatesStorageMetadataForFullContainer()
    {
        $this->container->setExpirationSeconds(3600);
        $storage = $this->manager->getStorage();
        $metadata = $storage->getMetadata($this->container->getName());
        $this->assertTrue(array_key_exists('EXPIRE', $metadata));
        $this->assertEquals($_SERVER['REQUEST_TIME'] + 3600, $metadata['EXPIRE']);
    }

    public function testSettingExpirationSecondsForIndividualKeyUpdatesStorageMetadataForThatKey()
    {
        $this->container->foo = 'bar';
        $this->container->setExpirationSeconds(3600, 'foo');
        $storage = $this->manager->getStorage();
        $metadata = $storage->getMetadata($this->container->getName());
        $this->assertTrue(array_key_exists('EXPIRE_KEYS', $metadata));
        $this->assertTrue(array_key_exists('foo', $metadata['EXPIRE_KEYS']));
        $this->assertEquals($_SERVER['REQUEST_TIME'] + 3600, $metadata['EXPIRE_KEYS']['foo']);
    }

    public function testMultipleCallsToExpirationSecondsAggregates()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $this->container->baz = 'bat';
        $this->container->bat = 'bas';
        $this->container->setExpirationSeconds(3600);
        $this->container->setExpirationSeconds(1800, 'foo');
        $this->container->setExpirationSeconds(900, array('baz', 'bat'));
        $storage = $this->manager->getStorage();
        $metadata = $storage->getMetadata($this->container->getName());
        $this->assertEquals($_SERVER['REQUEST_TIME'] + 1800, $metadata['EXPIRE_KEYS']['foo']);
        $this->assertEquals($_SERVER['REQUEST_TIME'] +  900, $metadata['EXPIRE_KEYS']['baz']);
        $this->assertEquals($_SERVER['REQUEST_TIME'] +  900, $metadata['EXPIRE_KEYS']['bat']);
        $this->assertEquals($_SERVER['REQUEST_TIME'] + 3600, $metadata['EXPIRE']);
    }

    public function testPassingUnsetKeyToSetExpirationSecondsDoesNothing()
    {
        $this->container->setExpirationSeconds(3600, 'foo');
        $storage = $this->manager->getStorage();
        $metadata = $storage->getMetadata($this->container->getName());
        $this->assertFalse(isset($metadata['EXPIRE_KEYS']['foo']));
    }

    public function testPassingUnsetKeyInArrayToSetExpirationSecondsDoesNothing()
    {
        $this->container->setExpirationSeconds(3600, array('foo'));
        $storage = $this->manager->getStorage();
        $metadata = $storage->getMetadata($this->container->getName());
        $this->assertFalse(isset($metadata['EXPIRE_KEYS']['foo']));
    }

    public function testGetKeyWithContainerExpirationInPastResetsToNull()
    {
        $this->container->foo = 'bar';
        $storage = $this->manager->getStorage();
        $storage->setMetadata('Default', array('EXPIRE' => $_SERVER['REQUEST_TIME'] - 18600));
        $this->assertNull($this->container->foo);
    }

    public function testGetKeyWithExpirationInPastResetsToNull()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $storage = $this->manager->getStorage();
        $storage->setMetadata('Default', array('EXPIRE_KEYS' => array('foo' => $_SERVER['REQUEST_TIME'] - 18600)));
        $this->assertNull($this->container->foo);
        $this->assertEquals('baz', $this->container->bar);
    }

    public function testKeyExistsWithContainerExpirationInPastReturnsFalse()
    {
        $this->container->foo = 'bar';
        $storage = $this->manager->getStorage();
        $storage->setMetadata('Default', array('EXPIRE' => $_SERVER['REQUEST_TIME'] - 18600));
        $this->assertFalse(isset($this->container->foo));
    }

    public function testKeyExistsWithExpirationInPastReturnsFalse()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $storage = $this->manager->getStorage();
        $storage->setMetadata('Default', array('EXPIRE_KEYS' => array('foo' => $_SERVER['REQUEST_TIME'] - 18600)));
        $this->assertFalse(isset($this->container->foo));
        $this->assertTrue(isset($this->container->bar));
    }

    public function testKeyExistsWithContainerExpirationInPastWithSetExpirationSecondsReturnsFalse()
    {
        $this->container->foo = 'bar';
        $storage = $this->manager->getStorage();
        $storage->setMetadata('Default', array('EXPIRE' => $_SERVER['REQUEST_TIME'] - 18600));
        $this->container->setExpirationSeconds(1);
        $this->assertFalse(isset($this->container->foo));
    }

    public function testSettingExpiredKeyOverwritesExpiryMetadataForThatKey()
    {
        $this->container->foo = 'bar';
        $storage = $this->manager->getStorage();
        $storage->setMetadata('Default', array('EXPIRE' => $_SERVER['REQUEST_TIME'] - 18600));
        $this->container->foo = 'baz';
        $this->assertTrue(isset($this->container->foo));
        $this->assertEquals('baz', $this->container->foo);
        $metadata = $storage->getMetadata('Default');
        $this->assertFalse(isset($metadata['EXPIRE_KEYS']['foo']));
    }

    public function testSettingExpirationHopsWithNoVariablesMarksContainerByWritingToStorage()
    {
        $this->container->setExpirationHops(2);
        $storage = $this->manager->getStorage();
        $metadata = $storage->getMetadata('Default');
        $this->assertTrue(array_key_exists('EXPIRE_HOPS', $metadata));
        $this->assertEquals(
            array('hops' => 2, 'ts' => $storage->getRequestAccessTime()),
            $metadata['EXPIRE_HOPS']
        );
    }

    public function testSettingExpirationHopsWithSingleKeyMarksContainerByWritingToStorage()
    {
        $this->container->foo = 'bar';
        $this->container->setExpirationHops(2, 'foo');
        $storage = $this->manager->getStorage();
        $metadata = $storage->getMetadata('Default');
        $this->assertTrue(array_key_exists('EXPIRE_HOPS_KEYS', $metadata));
        $this->assertTrue(array_key_exists('foo', $metadata['EXPIRE_HOPS_KEYS']));
        $this->assertEquals(
            array('hops' => 2, 'ts' => $storage->getRequestAccessTime()),
            $metadata['EXPIRE_HOPS_KEYS']['foo']
        );
    }

    public function testSettingExpirationHopsWithMultipleKeysMarksContainerByWritingToStorage()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $this->container->baz = 'bat';
        $this->container->setExpirationHops(2, array('foo', 'baz'));
        $storage = $this->manager->getStorage();
        $metadata = $storage->getMetadata('Default');
        $this->assertTrue(array_key_exists('EXPIRE_HOPS_KEYS', $metadata));

        $hops     = $metadata['EXPIRE_HOPS_KEYS'];
        $ts       = $storage->getRequestAccessTime();
        $expected = array(
            'foo' => array(
                'hops' => 2,
                'ts'   => $ts,
            ),
            'baz' => array(
                'hops' => 2,
                'ts'   => $ts,
            ),
        );
        $this->assertEquals($expected, $hops);
    }

    public function testContainerExpiresAfterSpecifiedHops()
    {
        $this->container->foo = 'bar';
        $this->container->setExpirationHops(1);

        $storage = $this->manager->getStorage();
        $ts = $storage->getRequestAccessTime();

        $storage->setMetadata('_REQUEST_ACCESS_TIME', $ts + 60);
        $this->assertEquals('bar', $this->container->foo);

        $storage->setMetadata('_REQUEST_ACCESS_TIME', $ts + 120);
        $this->assertNull($this->container->foo);
    }

    public function testInstantiatingMultipleContainersInSameRequestDoesNotCreateExtraHops()
    {
        $this->container->foo = 'bar';
        $this->container->setExpirationHops(1);

        $container = new Container('Default', $this->manager);
        $this->assertEquals('bar', $container->foo);
        $this->assertEquals('bar', $this->container->foo);
    }

    public function testKeyExpiresAfterSpecifiedHops()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $this->container->setExpirationHops(1, 'foo');

        $storage = $this->manager->getStorage();
        $ts = $storage->getRequestAccessTime();

        $storage->setMetadata('_REQUEST_ACCESS_TIME', $ts + 60);
        $this->assertEquals('bar', $this->container->foo);
        $this->assertEquals('baz', $this->container->bar);

        $storage->setMetadata('_REQUEST_ACCESS_TIME', $ts + 120);
        $this->assertNull($this->container->foo);
        $this->assertEquals('baz', $this->container->bar);
    }

    public function testInstantiatingMultipleContainersInSameRequestDoesNotCreateExtraKeyHops()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $this->container->setExpirationHops(1, 'foo');

        $container = new Container('Default', $this->manager);
        $this->assertEquals('bar', $container->foo);
        $this->assertEquals('bar', $this->container->foo);
        $this->assertEquals('baz', $container->bar);
        $this->assertEquals('baz', $this->container->bar);
    }

    public function testKeysExpireAfterSpecifiedHops()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $this->container->baz = 'bat';
        $this->container->setExpirationHops(1, array('foo', 'baz'));

        $storage = $this->manager->getStorage();
        $ts = $storage->getRequestAccessTime();

        $storage->setMetadata('_REQUEST_ACCESS_TIME', $ts + 60);
        $this->assertEquals('bar', $this->container->foo);
        $this->assertEquals('baz', $this->container->bar);
        $this->assertEquals('bat', $this->container->baz);

        $storage->setMetadata('_REQUEST_ACCESS_TIME', $ts + 120);
        $this->assertNull($this->container->foo);
        $this->assertEquals('baz', $this->container->bar);
        $this->assertNull($this->container->baz);
    }

    public function testCanIterateOverContainer()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $this->container->baz = 'bat';
        $expected = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        );
        $test = array();
        foreach ($this->container as $key => $value) {
            $test[$key] = $value;
        }
        $this->assertSame($expected, $test);
    }

    public function testIterationHonorsExpirationHops()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $this->container->baz = 'bat';
        $this->container->setExpirationHops(1, array('foo', 'baz'));

        $storage = $this->manager->getStorage();
        $ts = $storage->getRequestAccessTime();

        // First hop
        $storage->setMetadata('_REQUEST_ACCESS_TIME', $ts + 60);
        $expected = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
        );
        $test = array();
        foreach ($this->container as $key => $value) {
            $test[$key] = $value;
        }
        $this->assertSame($expected, $test);

        // Second hop
        $storage->setMetadata('_REQUEST_ACCESS_TIME', $ts + 120);
        $expected = array('bar' => 'baz');
        $test = array();
        foreach ($this->container as $key => $value) {
            $test[$key] = $value;
        }
        $this->assertSame($expected, $test);
    }

    public function testIterationHonorsExpirationTimestamps()
    {
        $this->container->foo = 'bar';
        $this->container->bar = 'baz';
        $storage = $this->manager->getStorage();
        $storage->setMetadata('Default', array('EXPIRE_KEYS' => array('foo' => $_SERVER['REQUEST_TIME'] - 18600)));
        $expected = array('bar' => 'baz');
        $test     = array();
        foreach ($this->container as $key => $value) {
            $test[$key] =  $value;
        }
        $this->assertSame($expected, $test);
    }

    /**
     * @group ZF-10706
     */
    public function testValidationShouldNotRaiseErrorForMissingResponseObject()
    {
        $session = new Container('test');
        $session->test = 42;
        $this->assertEquals(42, $session->test);
    }

    public function testExchangeArray()
    {
        $this->container->offsetSet('old', 'old');
        $this->assertTrue($this->container->offsetExists('old'));

        $old = $this->container->exchangeArray(array('new' => 'new'));
        $this->assertArrayHasKey('old', $old, "'exchangeArray' doesn't return an array of old items");
        $this->assertFalse($this->container->offsetExists('old'), "'exchangeArray' doesn't remove old items");
        $this->assertTrue($this->container->offsetExists('new'), "'exchangeArray' doesn't add the new array items");
    }

    public function testExchangeArrayObject()
    {
        $this->container->offsetSet('old', 'old');
        $this->assertTrue($this->container->offsetExists('old'));

        $old = $this->container->exchangeArray(new \Zend\Stdlib\ArrayObject(array('new' => 'new')));
        $this->assertArrayHasKey('old', $old, "'exchangeArray' doesn't return an array of old items");
        $this->assertFalse($this->container->offsetExists('old'), "'exchangeArray' doesn't remove old items");
        $this->assertTrue($this->container->offsetExists('new'), "'exchangeArray' doesn't add the new array items");
    }

    public function testMultiDimensionalUnset()
    {
        if (version_compare(PHP_VERSION, '5.3.4') < 0) {
            $this->markTestSkipped('Known issue on versions of PHP less than 5.3.4');
        }
        $this->container->foo = array('bar' => 'baz');
        unset($this->container['foo']['bar']);
        $this->assertSame(array(), $this->container->foo);
    }

    public function testUpgradeBehaviors()
    {
        $storage = $this->manager->getStorage();
        $storage['foo'] = new \ArrayObject(array('bar' => 'baz'));

        $container = new Container('foo', $this->manager);
        $this->assertEquals('baz', $container->bar);
        $container->baz = 'boo';
        $this->assertEquals('boo', $storage['foo']['baz']);
    }
}
