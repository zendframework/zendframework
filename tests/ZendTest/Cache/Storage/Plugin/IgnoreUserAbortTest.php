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

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class IgnoreUserAbortTest extends CommonPluginTest
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
        $this->_plugin  = new Cache\Storage\Plugin\IgnoreUserAbort();
        $this->_plugin->setOptions($this->_options);
    }

    public function testAddPlugin()
    {
        $this->_adapter->addPlugin($this->_plugin);

        // check attached callbacks
        $expectedListeners = array(
            'setItem.pre'       => 'onBefore',
            'setItem.post'      => 'onAfter',
            'setItem.exception' => 'onAfter',

            'setItems.pre'       => 'onBefore',
            'setItems.post'      => 'onAfter',
            'setItems.exception' => 'onAfter',

            'addItem.pre'       => 'onBefore',
            'addItem.post'      => 'onAfter',
            'addItem.exception' => 'onAfter',

            'addItems.pre'       => 'onBefore',
            'addItems.post'      => 'onAfter',
            'addItems.exception' => 'onAfter',

            'replaceItem.pre'       => 'onBefore',
            'replaceItem.post'      => 'onAfter',
            'replaceItem.exception' => 'onAfter',

            'replaceItems.pre'       => 'onBefore',
            'replaceItems.post'      => 'onAfter',
            'replaceItems.exception' => 'onAfter',

            'checkAndSetItem.pre'       => 'onBefore',
            'checkAndSetItem.post'      => 'onAfter',
            'checkAndSetItem.exception' => 'onAfter',

            'incrementItem.pre'       => 'onBefore',
            'incrementItem.post'      => 'onAfter',
            'incrementItem.exception' => 'onAfter',

            'incrementItems.pre'       => 'onBefore',
            'incrementItems.post'      => 'onAfter',
            'incrementItems.exception' => 'onAfter',

            'decrementItem.pre'       => 'onBefore',
            'decrementItem.post'      => 'onAfter',
            'decrementItem.exception' => 'onAfter',

            'decrementItems.pre'       => 'onBefore',
            'decrementItems.post'      => 'onAfter',
            'decrementItems.exception' => 'onAfter',
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
}
