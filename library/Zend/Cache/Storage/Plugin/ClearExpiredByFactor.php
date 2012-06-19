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
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Plugin;

use Traversable,
    Zend\EventManager\EventManagerInterface,
    Zend\Cache\Exception,
    Zend\Cache\Storage\ClearExpiredInterface,
    Zend\Cache\Storage\StorageInterface,
    Zend\Cache\Storage\PostEvent;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ClearExpiredByFactor extends AbstractPlugin
{
    /**
     * Handles
     *
     * @var array
     */
    protected $handles = array();

    /**
     * Attach
     *
     * @param  EventManagerInterface $events
     * @param  int                   $priority
     * @return ClearByFactor
     * @throws Exception\LogicException
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $index = spl_object_hash($events);
        if (isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin already attached');
        }

        $handles = array();
        $this->handles[$index] = & $handles;

        $callback = array($this, 'clearExpiredByFactor');
        $handles[] = $events->attach('setItem.post',  $callback, $priority);
        $handles[] = $events->attach('setItems.post', $callback, $priority);
        $handles[] = $events->attach('addItem.post',  $callback, $priority);
        $handles[] = $events->attach('addItems.post', $callback, $priority);

        return $this;
    }

    /**
     * Detach
     *
     * @param  EventManagerInterface $events
     * @return ClearByFactor
     * @throws Exception\LogicException
     */
    public function detach(EventManagerInterface $events)
    {
        $index = spl_object_hash($events);
        if (!isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin not attached');
        }

        // detach all handles of this index
        foreach ($this->handles[$index] as $handle) {
            $events->detach($handle);
        }

        // remove all detached handles
        unset($this->handles[$index]);

        return $this;
    }

    /**
     * Clear expired items by factor after writing new item(s)
     *
     * @param  PostEvent $event
     * @return void
     */
    public function clearExpiredByFactor(PostEvent $event)
    {
        $storage = $event->getStorage();
        if ( !($storage instanceof ClearExpiredInterface) ) {
            return;
        }

        $factor = $this->getOptions()->getClearingFactor();
        if ($factor && mt_rand(1, $factor) == 1) {
            $storage->clearExpired();
        }
    }
}
