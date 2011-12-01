<?php

namespace ZendTest\Cache\Storage\Plugin;
use Zend\Cache,
    Zend\Cache\Storage\PostEvent,
    ZendTest\Cache\Storage\TestAsset\MockAdapter;

class ClearByFactorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The storage adapter
     *
     * @var ZendTest\Cache\Storage\TestAsset\MockAdapter
     */
    protected $_adapter;

    /**
     * The serializer plugin
     *
     * @var Zend\Cache\Storage\Plugin\OptimizeByFactor
     */
    protected $_plugin;

    public function setUp()
    {
        $this->_adapter = new MockAdapter();
        $this->_plugin  = new Cache\Storage\Plugin\ClearByFactor(array(
            'clearing_factor' => 1,
        ));
    }

    public function testAddPlugin()
    {
        $this->_adapter->addPlugin($this->_plugin);

        // check attached callbacks
        $expectedListeners = array(
            'setItem.post'        => 'clearByFactor',
            'setItems.post'       => 'clearByFactor',
            'addItem.post'        => 'clearByFactor',
            'addItems.post'       => 'clearByFactor',
        );
        foreach ($expectedListeners as $eventName => $expectedCallbackMethod) {
            $listeners = $this->_adapter->events()->getListeners($eventName);

            // event should attached only once
            $this->assertSame(1, $listeners->count());

            // check expected callback method
            $cb = $listeners->top()->getCallback();
            $this->assertArrayHasKey(0, $cb);
            $this->assertSame($this->_plugin, $cb[0]);
            $this->assertArrayHasKey(1, $cb);
            $this->assertSame($expectedCallbackMethod, $cb[1]);
        }
    }

    public function testRemovePlugin()
    {
        $this->_adapter->addPlugin($this->_plugin);
        $this->_adapter->removePlugin($this->_plugin);

        // no events should be attached
        $this->assertEquals(0, count($this->_adapter->events()->getEvents()));
    }

    public function testClearByFactorUsingNamespace()
    {
        $adapter = $this->getMock(get_class($this->_adapter), array('clearByNamespace'));
        $this->_plugin->setClearingFactor(1);
        $this->_plugin->setClearByNamespace(true);

        // test optimize will be called
        $adapter
            ->expects($this->once())
            ->method('clearByNamespace')
            ->will($this->returnValue(true));

        // call event callback
        $event = new PostEvent('setItem.post', $adapter, array(
            'options'  => array(),
        ));
        $result = true;
        $event->setResult($result);
        $this->_plugin->clearByFactor($event);

        $this->assertTrue($event->getResult());
    }

    public function testClearByFactorAllNamespaces()
    {
        $adapter = $this->getMock(get_class($this->_adapter), array('clear'));
        $this->_plugin->setClearingFactor(1);
        $this->_plugin->setClearByNamespace(false);

        // test optimize will be called
        $adapter
            ->expects($this->once())
            ->method('clear')
            ->will($this->returnValue(true));

        // call event callback
        $event = new PostEvent('setItem.post', $adapter, array(
            'options'  => array(),
        ));
        $result = true;
        $event->setResult($result);
        $this->_plugin->clearByFactor($event);

        $this->assertTrue($event->getResult());
    }

}
