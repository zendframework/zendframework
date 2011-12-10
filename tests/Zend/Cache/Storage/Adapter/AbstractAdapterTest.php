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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache\Storage\Adapter;
use Zend\Cache,
    Zend\Cache\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $this->_storage = $this->getMockForAbstractClass('Zend\Cache\Storage\Adapter\AbstractAdapter');
    }

    public function testGetOptions()
    {
        $options = $this->_storage->getOptions();

        $this->assertArrayHasKey('writable', $options);
        $this->assertInternalType('boolean', $options['writable']);

        $this->assertArrayHasKey('readable', $options);
        $this->assertInternalType('boolean', $options['readable']);

        $this->assertArrayHasKey('ttl', $options);
        $this->assertInternalType('integer', $options['ttl']);

        $this->assertArrayHasKey('namespace', $options);
        $this->assertInternalType('string', $options['namespace']);

        $this->assertArrayHasKey('namespace_pattern', $options);
        $this->assertInternalType('string', $options['namespace_pattern']);

        $this->assertArrayHasKey('key_pattern', $options);
        $this->assertInternalType('string', $options['key_pattern']);

        $this->assertArrayHasKey('ignore_missing_items', $options);
        $this->assertInternalType('boolean', $options['ignore_missing_items']);
    }

    public function testSetWritable()
    {
        $this->_storage->setWritable(true);
        $this->assertTrue($this->_storage->getWritable());

        $this->_storage->setWritable(false);
        $this->assertFalse($this->_storage->getWritable());
    }

    public function testSetReadable()
    {
        $this->_storage->setReadable(true);
        $this->assertTrue($this->_storage->getReadable());

        $this->_storage->setReadable(false);
        $this->assertFalse($this->_storage->getReadable());
    }

    public function testSetTtl()
    {
        $this->_storage->setTtl('123');
        $this->assertSame(123, $this->_storage->getTtl());
    }

    public function testSetTtlThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_storage->setTtl(-1);
    }

    public function testGetDefaultNamespaceNotEmpty()
    {
        $ns = $this->_storage->getNamespace();
        $this->assertNotEmpty($ns);
    }

    public function testSetNamespace()
    {
        $this->_storage->setNamespace('new_namespace');
        $this->assertSame('new_namespace', $this->_storage->getNamespace());
    }

    public function testSetNamespacePattern()
    {
        $pattern = '/^.*$/';
        $this->_storage->setNamespacePattern($pattern);
        $this->assertEquals($pattern, $this->_storage->getNamespacePattern());
    }

    public function testUnsetNamespacePattern()
    {
        $this->_storage->setNamespacePattern(null);
        $this->assertSame('', $this->_storage->getNamespacePattern());
    }

    public function testSetNamespace0()
    {
        $this->_storage->setNamespace('0');
        $this->assertSame('0', $this->_storage->getNamespace());
    }

    public function testSetEmptyNamespaceThrowsException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_storage->setNamespace('');
    }

    public function testSetNamespacePatternThrowsExceptionOnInvalidPattern()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_storage->setNamespacePattern('#');
    }

    public function testSetNamespacePatternThrowsExceptionOnInvalidNamespace()
    {
        $this->_storage->setNamespace('ns');
        $this->setExpectedException('Zend\Cache\Exception\RuntimeException');
        $this->_storage->setNamespacePattern('/[abc]/');
    }

    public function testSetKeyPattern()
    {
        $this->_storage->setKeyPattern('/^[key]+$/Di');
        $this->assertEquals('/^[key]+$/Di', $this->_storage->getKeyPattern());
    }

    public function testUnsetKeyPattern()
    {
        $this->_storage->setKeyPattern(null);
        $this->assertSame('', $this->_storage->getKeyPattern());
    }

    public function testSetKeyPatternThrowsExceptionOnInvalidPattern()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_storage->setKeyPattern('#');
    }

    public function testSetIgnoreMissingItems()
    {
        $this->_storage->setIgnoreMissingItems(true);
        $this->assertTrue($this->_storage->getIgnoreMissingItems());

        $this->_storage->setIgnoreMissingItems(false);
        $this->assertFalse($this->_storage->getIgnoreMissingItems());
    }

    public function testPluginRegistry()
    {
        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();

        // no plugin registered
        $this->assertFalse($this->_storage->hasPlugin($plugin));
        $this->assertEquals(0, count($this->_storage->getPlugins()));
        $this->assertEquals(0, count($plugin->getHandles()));

        // register a plugin
        $this->assertSame($this->_storage, $this->_storage->addPlugin($plugin));
        $this->assertTrue($this->_storage->hasPlugin($plugin));
        $this->assertEquals(1, count($this->_storage->getPlugins()));

        // test registered callback handles
        $handles = $plugin->getHandles();
        $this->assertEquals(1, count($handles));
        $this->assertEquals(count($plugin->getEventCallbacks()), count(current($handles)));

        // test unregister a plugin
        $this->assertSame($this->_storage, $this->_storage->removePlugin($plugin));
        $this->assertFalse($this->_storage->hasPlugin($plugin));
        $this->assertEquals(0, count($this->_storage->getPlugins()));
        $this->assertEquals(0, count($plugin->getHandles()));

        // test plugin already unregistered
        $this->setExpectedException('Zend\Cache\Exception\LogicException');
        $this->_storage->removePlugin($plugin);
    }

    public function testInternalTriggerPre()
    {
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
        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();
        $this->_storage->addPlugin($plugin);

        $params = new \ArrayObject(array(
            'key'   => 'key1',
            'value' => 'value1'
        ));

        // call protected method
        $method = new \ReflectionMethod(get_class($this->_storage), 'triggerException');
        $method->setAccessible(true);

        $this->setExpectedException('Zend\Cache\Exception\RuntimeException', 'test');
        $method->invokeArgs($this->_storage, array('setItem', $params, new RuntimeException('test')));
    }

    public function testGetItems()
    {
        $options    = array('ttl' => 123);
        $items      = array(
            'key1'  => 'value1',
            'dKey1' => false,
            'key2'  => 'value2',
        );

        $i = 0;
        foreach ($items as $k => $v) {
            $this->_storage->expects($this->at($i++))
                ->method('getItem')
                ->with($this->equalTo($k), $this->equalTo($options))
                ->will($this->returnValue($v));
        }

        $rs = $this->_storage->getItems(array_keys($items), $options);

        // remove missing items from arrray to test
        array_walk($items, function ($v, $k) use (&$items) {
            if ($v === false) {
                unset($items[$k]);
            }
        });

        $this->assertEquals($items, $rs);
    }

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
            $this->_storage->expects($this->at($i++))
                ->method('getMetadata')
                ->with($this->equalTo($k), $this->equalTo($options))
                ->will($this->returnValue($v));
        }

        $rs = $this->_storage->getMetadatas(array_keys($items), $options);

        // remove missing items from arrray to test
        array_walk($items, function ($v, $k) use (&$items) {
            if ($v === false) {
                unset($items[$k]);
            }
        });

        $this->assertEquals($items, $rs);
    }

    public function testHasItem()
    {
        $this->_storage->expects($this->at(0))
                       ->method('getItem')
                       ->with($this->equalTo('key'))
                       ->will($this->returnValue('value'));

        $this->assertTrue($this->_storage->hasItem('key'));
    }

    public function testHasItems()
    {
        $keys = array('key1', 'key2', 'key3');

        foreach ($keys as $i => $key) {
            $this->_storage->expects($this->at($i))
                           ->method('getItem')
                           ->with($this->equalTo($key))
                           ->will(
                               ($i % 2) ? $this->returnValue('value')
                                        : $this->returnValue(false)
                           );
        }

        $rs = $this->_storage->hasItems($keys);
        $this->assertInternalType('array', $rs);
        $this->assertEquals(floor(count($keys) / 2), count($rs));
    }

    public function testSetItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_storage->setItems($items, $options));
    }

    public function testSetItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->_storage->setItems($items, $options));
    }

    public function testAddItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        // add -> has -> get -> set
        $this->_storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(false));
        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_storage->addItems($items, $options));
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
        $this->_storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(false));
        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->_storage->addItems($items, $options));
    }

    public function testReplaceItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        // replace -> has -> get -> set
        $this->_storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(true));
        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_storage->replaceItems($items, $options));
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
        $this->_storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(true));
        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->_storage->replaceItems($items, $options));
    }

    public function testRemoveItems()
    {
        $options = array('ttl' => 123);
        $keys    = array('key1', 'key2');

        foreach ($keys as $i => $key) {
            $this->_storage->expects($this->at($i))
                           ->method('removeItem')
                           ->with($this->equalTo($key), $this->equalTo($options))
                           ->will($this->returnValue(true));
        }

        $this->assertTrue($this->_storage->removeItems($keys, $options));
    }

    public function testRemoveItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array('key1', 'key2', 'key3');

        $this->_storage->expects($this->at(0))
                       ->method('removeItem')
                       ->with($this->equalTo('key1'), $this->equalTo($options))
                       ->will($this->returnValue(true));
        $this->_storage->expects($this->at(1))
                       ->method('removeItem')
                       ->with($this->equalTo('key2'), $this->equalTo($options))
                       ->will($this->returnValue(false)); // -> fail
        $this->_storage->expects($this->at(2))
                       ->method('removeItem')
                       ->with($this->equalTo('key3'), $this->equalTo($options))
                       ->will($this->returnValue(true));

        $this->assertFalse($this->_storage->removeItems($items, $options));
    }

    // TODO: getDelayed + fatch[All]
    // TODO: incrementItem[s] + decrementItem[s]
    // TODO: touchItem[s]

}
