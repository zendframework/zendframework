<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;
use Zend\Cache\Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Mock of the abstract storage adapter
     *
     * @var Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    protected $_storage;

    public function setUp()
    {
        $this->_options = new Cache\Storage\Adapter\AdapterOptions();
    }

    public function testGetOptions()
    {
        $this->_storage = $this->getMockForAbstractAdapter();

        $options = $this->_storage->getOptions();
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\AdapterOptions', $options);
        $this->assertInternalType('boolean', $options->getWritable());
        $this->assertInternalType('boolean', $options->getReadable());
        $this->assertInternalType('integer', $options->getTtl());
        $this->assertInternalType('string', $options->getNamespace());
        $this->assertInternalType('string', $options->getKeyPattern());
    }

    public function testSetWritable()
    {
        $this->_options->setWritable(true);
        $this->assertTrue($this->_options->getWritable());

        $this->_options->setWritable(false);
        $this->assertFalse($this->_options->getWritable());
    }

    public function testSetReadable()
    {
        $this->_options->setReadable(true);
        $this->assertTrue($this->_options->getReadable());

        $this->_options->setReadable(false);
        $this->assertFalse($this->_options->getReadable());
    }

    public function testSetTtl()
    {
        $this->_options->setTtl('123');
        $this->assertSame(123, $this->_options->getTtl());
    }

    public function testSetTtlThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setTtl(-1);
    }

    public function testGetDefaultNamespaceNotEmpty()
    {
        $ns = $this->_options->getNamespace();
        $this->assertNotEmpty($ns);
    }

    public function testSetNamespace()
    {
        $this->_options->setNamespace('new_namespace');
        $this->assertSame('new_namespace', $this->_options->getNamespace());
    }

    public function testSetNamespace0()
    {
        $this->_options->setNamespace('0');
        $this->assertSame('0', $this->_options->getNamespace());
    }

    public function testSetKeyPattern()
    {
        $this->_options->setKeyPattern('/^[key]+$/Di');
        $this->assertEquals('/^[key]+$/Di', $this->_options->getKeyPattern());
    }

    public function testUnsetKeyPattern()
    {
        $this->_options->setKeyPattern(null);
        $this->assertSame('', $this->_options->getKeyPattern());
    }

    public function testSetKeyPatternThrowsExceptionOnInvalidPattern()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setKeyPattern('#');
    }

    public function testPluginRegistry()
    {
        $this->_storage = $this->getMockForAbstractAdapter();

        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();

        // no plugin registered
        $this->assertFalse($this->_storage->hasPlugin($plugin));
        $this->assertEquals(0, count($this->_storage->getPluginRegistry()));
        $this->assertEquals(0, count($plugin->getHandles()));

        // register a plugin
        $this->assertSame($this->_storage, $this->_storage->addPlugin($plugin));
        $this->assertTrue($this->_storage->hasPlugin($plugin));
        $this->assertEquals(1, count($this->_storage->getPluginRegistry()));

        // test registered callback handles
        $handles = $plugin->getHandles();
        $this->assertEquals(1, count($handles));
        $this->assertEquals(count($plugin->getEventCallbacks()), count(current($handles)));

        // test unregister a plugin
        $this->assertSame($this->_storage, $this->_storage->removePlugin($plugin));
        $this->assertFalse($this->_storage->hasPlugin($plugin));
        $this->assertEquals(0, count($this->_storage->getPluginRegistry()));
        $this->assertEquals(0, count($plugin->getHandles()));
    }

    public function testInternalTriggerPre()
    {
        $this->_storage = $this->getMockForAbstractAdapter();

        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();
        $this->_storage->addPlugin($plugin);

        $params = new \ArrayObject(array(
            'key'   => 'key1',
            'value' => 'value1'
        ));

        // call protected method
        $method = new \ReflectionMethod(get_class($this->_storage), 'triggerPre');
        $method->setAccessible(true);
        $rsCollection = $method->invoke($this->_storage, 'setItem', $params);
        $this->assertInstanceOf('Zend\EventManager\ResponseCollection', $rsCollection);

        // test called event
        $calledEvents = $plugin->getCalledEvents();
        $this->assertEquals(1, count($calledEvents));

        $event = current($calledEvents);
        $this->assertInstanceOf('Zend\Cache\Storage\Event', $event);
        $this->assertEquals('setItem.pre', $event->getName());
        $this->assertSame($this->_storage, $event->getTarget());
        $this->assertSame($params, $event->getParams());
    }

    public function testInternalTriggerPost()
    {
        $this->_storage = $this->getMockForAbstractAdapter();

        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();
        $this->_storage->addPlugin($plugin);

        $params = new \ArrayObject(array(
            'key'   => 'key1',
            'value' => 'value1'
        ));
        $result = true;

        // call protected method
        $method = new \ReflectionMethod(get_class($this->_storage), 'triggerPost');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->_storage, array('setItem', $params, &$result));

        // test called event
        $calledEvents = $plugin->getCalledEvents();
        $this->assertEquals(1, count($calledEvents));
        $event = current($calledEvents);

        // return value of triggerPost and the called event should be the same
        $this->assertSame($result, $event->getResult());

        $this->assertInstanceOf('Zend\Cache\Storage\PostEvent', $event);
        $this->assertEquals('setItem.post', $event->getName());
        $this->assertSame($this->_storage, $event->getTarget());
        $this->assertSame($params, $event->getParams());
        $this->assertSame($result, $event->getResult());
    }

    public function testInternalTriggerExceptionThrowRuntimeException()
    {
        $this->_storage = $this->getMockForAbstractAdapter();

        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();
        $this->_storage->addPlugin($plugin);

        $result = null;
        $params = new \ArrayObject(array(
            'key'   => 'key1',
            'value' => 'value1'
        ));

        // call protected method
        $method = new \ReflectionMethod(get_class($this->_storage), 'triggerException');
        $method->setAccessible(true);

        $this->setExpectedException('Zend\Cache\Exception\RuntimeException', 'test');
        $method->invokeArgs($this->_storage, array('setItem', $params, & $result, new Exception\RuntimeException('test')));
    }

    public function testGetItemCallsInternalGetItem()
    {
        $this->_storage = $this->getMockForAbstractAdapter(array('internalGetItem'));

        $key    = 'key1';
        $result = 'value1';

        $this->_storage
            ->expects($this->once())
            ->method('internalGetItem')
            ->with($this->equalTo($key))
            ->will($this->returnValue($result));

        $rs = $this->_storage->getItem($key);
        $this->assertEquals($result, $rs);
    }

    public function testGetItemsCallsInternalGetItems()
    {
        $this->_storage = $this->getMockForAbstractAdapter(array('internalGetItems'));

        $keys   = array('key1', 'key2');
        $result = array('key2' => 'value2');

        $this->_storage
            ->expects($this->once())
            ->method('internalGetItems')
            ->with($this->equalTo($keys))
            ->will($this->returnValue($result));

        $rs = $this->_storage->getItems($keys);
        $this->assertEquals($result, $rs);
    }

    public function testInternalGetItemsCallsInternalGetItemForEachKey()
    {
        $this->markTestSkipped(
            "This test doesn't work because of an issue with PHPUnit: "
            . 'https://github.com/sebastianbergmann/phpunit-mock-objects/issues/81'
        );

        $this->_storage = $this->getMockForAbstractAdapter(array('internalGetItem'));

        $items  = array('key1' => 'value1', 'notFound' => false, 'key2' => 'value2');
        $result = array('key1' => 'value1', 'key2' => 'value2');

        $i = 0; // method call counter
        foreach ($items as $k => $v) {
            $this->_storage->expects($this->at($i++))
                ->method('internalGetItem')
                ->with(
                    $this->equalTo($k),
                    $this->equalTo(null),
                    $this->equalTo(null)
                )
                ->will($this->returnCallback(function ($k, & $success, & $casToken) use ($items) {
                    if ($items[$k]) {
                        $success = true;
                        return $items[$k];
                    } else {
                        $success = false;
                        return null;
                    }
                }));
        }

        $rs = $this->_storage->getItems(array_keys($items), $options);
        $this->assertEquals($result, $rs);
    }

    public function testHasItemCallsInternalHasItem()
    {
        $this->_storage = $this->getMockForAbstractAdapter(array('internalHasItem'));

        $key    = 'key1';
        $result = true;

        $this->_storage
            ->expects($this->once())
            ->method('internalHasItem')
            ->with($this->equalTo($key))
            ->will($this->returnValue($result));

        $rs = $this->_storage->hasItem($key);
        $this->assertSame($result, $rs);
    }

    public function testHasItemsCallsInternalHasItems()
    {
        $this->_storage = $this->getMockForAbstractAdapter(array('internalHasItems'));

        $keys   = array('key1', 'key2');
        $result = array('key2');

        $this->_storage
            ->expects($this->once())
            ->method('internalHasItems')
            ->with($this->equalTo($keys))
            ->will($this->returnValue($result));

        $rs = $this->_storage->hasItems($keys);
        $this->assertEquals($result, $rs);
    }

    public function testInternalHasItemsCallsInternalHasItem()
    {
        $this->_storage = $this->getMockForAbstractAdapter(array('internalHasItem'));

        $items  = array('key1' => true);

        $this->_storage
            ->expects($this->atLeastOnce())
            ->method('internalHasItem')
            ->with($this->equalTo('key1'))
            ->will($this->returnValue(true));

        $rs = $this->_storage->hasItems(array_keys($items));
        $this->assertEquals(array('key1'), $rs);
    }

    public function testGetMetadataCallsInternalGetMetadata()
    {
        $this->_storage = $this->getMockForAbstractAdapter(array('internalGetMetadata'));

        $key    = 'key1';
        $result = array();

        $this->_storage
            ->expects($this->once())
            ->method('internalGetMetadata')
            ->with($this->equalTo($key))
            ->will($this->returnValue($result));

        $rs = $this->_storage->getMetadata($key);
        $this->assertSame($result, $rs);
    }

/*
    public function testGetMetadatas()
    {
        $options    = array('ttl' => 123);
        $items      = array(
            'key1'  => array('meta1' => 1),
            'dKey1' => false,
            'key2'  => array('meta2' => 2),
        );

        $i = 0;
        foreach ($items as $k => $v) {
            $this->storage->expects($this->at($i++))
                ->method('getMetadata')
                ->with($this->equalTo($k), $this->equalTo($options))
                ->will($this->returnValue($v));
        }

        $rs = $this->storage->getMetadatas(array_keys($items), $options);

        // remove missing items from array to test
        $expected = $items;
        foreach ($expected as $key => $value) {
            if (false === $value) {
                unset($expected[$key]);
            }
        }

        $this->assertEquals($expected, $rs);
    }

    public function testSetItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        $this->storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->storage->setItems($items, $options));
    }

    public function testSetItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        $this->storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->storage->setItems($items, $options));
    }

    public function testAddItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        // add -> has -> get -> set
        $this->storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(false));
        $this->storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->storage->addItems($items, $options));
    }

    public function testAddItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        // add -> has -> get -> set
        $this->storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(false));
        $this->storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->storage->addItems($items, $options));
    }

    public function testReplaceItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        // replace -> has -> get -> set
        $this->storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(true));
        $this->storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->storage->replaceItems($items, $options));
    }

    public function testReplaceItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        // replace -> has -> get -> set
        $this->storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(true));
        $this->storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->storage->replaceItems($items, $options));
    }

    public function testRemoveItems()
    {
        $options = array('ttl' => 123);
        $keys    = array('key1', 'key2');

        foreach ($keys as $i => $key) {
            $this->storage->expects($this->at($i))
                           ->method('removeItem')
                           ->with($this->equalTo($key), $this->equalTo($options))
                           ->will($this->returnValue(true));
        }

        $this->assertTrue($this->storage->removeItems($keys, $options));
    }

    public function testRemoveItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array('key1', 'key2', 'key3');

        $this->storage->expects($this->at(0))
                       ->method('removeItem')
                       ->with($this->equalTo('key1'), $this->equalTo($options))
                       ->will($this->returnValue(true));
        $this->storage->expects($this->at(1))
                       ->method('removeItem')
                       ->with($this->equalTo('key2'), $this->equalTo($options))
                       ->will($this->returnValue(false)); // -> fail
        $this->storage->expects($this->at(2))
                       ->method('removeItem')
                       ->with($this->equalTo('key3'), $this->equalTo($options))
                       ->will($this->returnValue(true));

        $this->assertFalse($this->storage->removeItems($items, $options));
    }
*/
    // TODO: incrementItem[s] + decrementItem[s]
    // TODO: touchItem[s]

    public function testPreEventsCanChangeArguments()
    {
        // getItem(s)
        $this->checkPreEventCanChangeArguments('getItem', array(
            'key' => 'key'
        ), array(
            'key' => 'changedKey',
        ));

        $this->checkPreEventCanChangeArguments('getItems', array(
            'keys' => array('key')
        ), array(
            'keys' => array('changedKey'),
        ));

        // hasItem(s)
        $this->checkPreEventCanChangeArguments('hasItem', array(
            'key' => 'key'
        ), array(
            'key' => 'changedKey',
        ));

        $this->checkPreEventCanChangeArguments('hasItems', array(
            'keys' => array('key'),
        ), array(
            'keys' => array('changedKey'),
        ));

        // getMetadata(s)
        $this->checkPreEventCanChangeArguments('getMetadata', array(
            'key' => 'key'
        ), array(
            'key' => 'changedKey',
        ));

        $this->checkPreEventCanChangeArguments('getMetadatas', array(
            'keys' => array('key'),
        ), array(
            'keys' => array('changedKey'),
        ));

        // setItem(s)
        $this->checkPreEventCanChangeArguments('setItem', array(
            'key'   => 'key',
            'value' => 'value',
        ), array(
            'key'   => 'changedKey',
            'value' => 'changedValue',
        ));

        $this->checkPreEventCanChangeArguments('setItems', array(
            'keyValuePairs' => array('key' => 'value'),
        ), array(
            'keyValuePairs' => array('changedKey' => 'changedValue'),
        ));

        // addItem(s)
        $this->checkPreEventCanChangeArguments('addItem', array(
            'key'   => 'key',
            'value' => 'value',
        ), array(
            'key'   => 'changedKey',
            'value' => 'changedValue',
        ));

        $this->checkPreEventCanChangeArguments('addItems', array(
            'keyValuePairs' => array('key' => 'value'),
        ), array(
            'keyValuePairs' => array('changedKey' => 'changedValue'),
        ));

        // replaceItem(s)
        $this->checkPreEventCanChangeArguments('replaceItem', array(
            'key'   => 'key',
            'value' => 'value',
        ), array(
            'key'   => 'changedKey',
            'value' => 'changedValue',
        ));

        $this->checkPreEventCanChangeArguments('replaceItems', array(
            'keyValuePairs' => array('key' => 'value'),
        ), array(
            'keyValuePairs' => array('changedKey' => 'changedValue'),
        ));

        // CAS
        $this->checkPreEventCanChangeArguments('checkAndSetItem', array(
            'token' => 'token',
            'key'   => 'key',
            'value' => 'value',
        ), array(
            'token' => 'changedToken',
            'key'   => 'changedKey',
            'value' => 'changedValue',
        ));

        // touchItem(s)
        $this->checkPreEventCanChangeArguments('touchItem', array(
            'key' => 'key',
        ), array(
            'key' => 'changedKey',
        ));

        $this->checkPreEventCanChangeArguments('touchItems', array(
            'keys' => array('key'),
        ), array(
            'keys' => array('changedKey'),
        ));

        // removeItem(s)
        $this->checkPreEventCanChangeArguments('removeItem', array(
            'key' => 'key',
        ), array(
            'key' => 'changedKey',
        ));

        $this->checkPreEventCanChangeArguments('removeItems', array(
            'keys' => array('key'),
        ), array(
            'keys' => array('changedKey'),
        ));

        // incrementItem(s)
        $this->checkPreEventCanChangeArguments('incrementItem', array(
            'key'   => 'key',
            'value' => 1
        ), array(
            'key'   => 'changedKey',
            'value' => 2,
        ));

        $this->checkPreEventCanChangeArguments('incrementItems', array(
            'keyValuePairs' => array('key' => 1),
        ), array(
            'keyValuePairs' => array('changedKey' => 2),
        ));

        // decrementItem(s)
        $this->checkPreEventCanChangeArguments('decrementItem', array(
            'key'   => 'key',
            'value' => 1
        ), array(
            'key'   => 'changedKey',
            'value' => 2,
        ));

        $this->checkPreEventCanChangeArguments('decrementItems', array(
            'keyValuePairs' => array('key' => 1),
        ), array(
            'keyValuePairs' => array('changedKey' => 2),
        ));
    }

    protected function checkPreEventCanChangeArguments($method, array $args, array $expectedArgs)
    {
        $internalMethod = 'internal' . ucfirst($method);
        $eventName      = $method . '.pre';

        // init mock
        $this->_storage = $this->getMockForAbstractAdapter(array($internalMethod));
        $this->_storage->getEventManager()->attach($eventName, function ($event) use ($expectedArgs) {
            $params = $event->getParams();
            foreach ($expectedArgs as $k => $v) {
                $params[$k] = $v;
            }
        });

        // set expected arguments of internal method call
        $tmp = $this->_storage->expects($this->once())->method($internalMethod);
        $equals = array();
        foreach ($expectedArgs as $v) {
            $equals[] = $this->equalTo($v);
        }
        call_user_func_array(array($tmp, 'with'), $equals);

        // run
        call_user_func_array(array($this->_storage, $method), $args);
    }

    /**
     * Generates a mock of the abstract storage adapter by mocking all abstract and the given methods
     * Also sets the adapter options
     *
     * @param array $methods
     * @return \Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    protected function getMockForAbstractAdapter(array $methods = array())
    {
        $class = 'Zend\Cache\Storage\Adapter\AbstractAdapter';

        if (!$methods) {
            $adapter = $this->getMockForAbstractClass($class);
        } else {
            $reflection = new \ReflectionClass('Zend\Cache\Storage\Adapter\AbstractAdapter');
            foreach ($reflection->getMethods() as $method) {
                if ($method->isAbstract()) {
                    $methods[] = $method->getName();
                }
            }
            $adapter = $this->getMockBuilder($class)->setMethods(array_unique($methods))->getMock();
        }

        $adapter->setOptions($this->_options);
        return $adapter;
    }
}
