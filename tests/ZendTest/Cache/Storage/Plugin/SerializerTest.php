<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Plugin;

use Zend\Cache;
use Zend\Cache\Storage\Event;
use Zend\Cache\Storage\PostEvent;
use Zend\Serializer;
use ArrayObject;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class SerializerTest extends CommonPluginTest
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    protected $_adapter;

    public function setUp()
    {
        $this->_adapter = $this->getMockForAbstractClass('Zend\Cache\Storage\Adapter\AbstractAdapter');
        $this->_options = new Cache\Storage\Plugin\PluginOptions();
        $this->_plugin  = new Cache\Storage\Plugin\Serializer();
        $this->_plugin->setOptions($this->_options);
    }

    public function testAddPlugin()
    {
        $this->_adapter->addPlugin($this->_plugin, 100);

        // check attached callbacks
        $expectedListeners = array(
            'getItem.post'        => 'onReadItemPost',
            'getItems.post'       => 'onReadItemsPost',

            'setItem.pre'         => 'onWriteItemPre',
            'setItems.pre'        => 'onWriteItemsPre',
            'addItem.pre'         => 'onWriteItemPre',
            'addItems.pre'        => 'onWriteItemsPre',
            'replaceItem.pre'     => 'onWriteItemPre',
            'replaceItems.pre'    => 'onWriteItemsPre',
            'checkAndSetItem.pre' => 'onWriteItemPre',

            'incrementItem.pre'   => 'onIncrementItemPre',
            'incrementItems.pre'  => 'onIncrementItemsPre',
            'decrementItem.pre'   => 'onDecrementItemPre',
            'decrementItems.pre'  => 'onDecrementItemsPre',

            'getCapabilities.post' => 'onGetCapabilitiesPost',
        );
        foreach ($expectedListeners as $eventName => $expectedCallbackMethod) {
            $listeners = $this->_adapter->getEventManager()->getListeners($eventName);

            // event should attached only once
            $this->assertSame(1, $listeners->count());

            // check expected callback method
            $cb = $listeners->top()->getCallback();
            $this->assertArrayHasKey(0, $cb);
            $this->assertSame($this->_plugin, $cb[0]);
            $this->assertArrayHasKey(1, $cb);
            $this->assertSame($expectedCallbackMethod, $cb[1]);

            // check expected priority
            $meta = $listeners->top()->getMetadata();
            $this->assertArrayHasKey('priority', $meta);
            if (substr($eventName, -4) == '.pre') {
                $this->assertSame(100, $meta['priority']);
            } else {
                $this->assertSame(-100, $meta['priority']);
            }
        }
    }

    public function testRemovePlugin()
    {
        $this->_adapter->addPlugin($this->_plugin);
        $this->_adapter->removePlugin($this->_plugin);

        // no events should be attached
        $this->assertEquals(0, count($this->_adapter->getEventManager()->getEvents()));
    }

    public function testUnserializeOnReadItem()
    {
        $value = serialize(123);
        $event = new PostEvent('getItem.post', $this->_adapter, new ArrayObject(), $value);
        $this->_plugin->onReadItemPost($event);

        $this->assertFalse($event->propagationIsStopped());
        $this->assertSame(123, $event->getResult());
    }

    public function testUnserializeOnReadItems()
    {
        $values = array('key1' => serialize(123), 'key2' => serialize(456));
        $event = new PostEvent('getItems.post', $this->_adapter, new ArrayObject(), $values);

        $this->_plugin->onReadItemsPost($event);

        $this->assertFalse($event->propagationIsStopped());

        $values = $event->getResult();
        $this->assertSame(123, $values['key1']);
        $this->assertSame(456, $values['key2']);
    }
}
