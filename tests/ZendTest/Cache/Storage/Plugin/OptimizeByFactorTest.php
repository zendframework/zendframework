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
use Zend\Cache\Storage\PostEvent;
use ZendTest\Cache\Storage\TestAsset\OptimizableMockAdapter;
use ArrayObject;

class OptimizeByFactorTest extends CommonPluginTest
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    protected $_adapter;

    public function setUp()
    {
        $this->_adapter = new OptimizableMockAdapter();
        $this->_options = new Cache\Storage\Plugin\PluginOptions(array(
            'optimizing_factor' => 1,
        ));
        $this->_plugin  = new Cache\Storage\Plugin\OptimizeByFactor();
        $this->_plugin->setOptions($this->_options);
    }

    public function testAddPlugin()
    {
        $this->_adapter->addPlugin($this->_plugin);

        // check attached callbacks
        $expectedListeners = array(
            'removeItem.post'  => 'optimizeByFactor',
            'removeItems.post' => 'optimizeByFactor',
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
        }
    }

    public function testRemovePlugin()
    {
        $this->_adapter->addPlugin($this->_plugin);
        $this->_adapter->removePlugin($this->_plugin);

        // no events should be attached
        $this->assertEquals(0, count($this->_adapter->getEventManager()->getEvents()));
    }

    public function testOptimizeByFactor()
    {
        $adapter = $this->getMock(get_class($this->_adapter), array('optimize'));

        // test optimize will be called
        $adapter
            ->expects($this->once())
            ->method('optimize');

        // call event callback
        $result = true;
        $event = new PostEvent('removeItem.post', $adapter, new ArrayObject(array(
            'options' => array()
        )), $result);

        $this->_plugin->optimizeByFactor($event);

        $this->assertTrue($event->getResult());
    }
}
