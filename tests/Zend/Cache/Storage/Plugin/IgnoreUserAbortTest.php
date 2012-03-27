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
 */

namespace ZendTest\Cache\Storage\Plugin;
use Zend\Cache,
    Zend\Cache\Storage\Event,
    Zend\Cache\Storage\PostEvent,
    Zend\Serializer,
    ArrayObject;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

}
