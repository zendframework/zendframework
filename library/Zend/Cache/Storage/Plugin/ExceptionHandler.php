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
    Zend\Cache\Exception,
    Zend\Cache\Storage\ExceptionEvent,
    Zend\EventManager\EventManagerInterface;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ExceptionHandler extends AbstractPlugin
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
     * @return ExceptionHandler
     * @throws Exception\LogicException
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $index = spl_object_hash($events);
        if (isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin already attached');
        }

        $callback = array($this, 'onException');
        $handles  = array();
        $this->handles[$index] = & $handles;

        // read
        $handles[] = $events->attach('getItem.exception', $callback, $priority);
        $handles[] = $events->attach('getItems.exception', $callback, $priority);

        $handles[] = $events->attach('hasItem.exception', $callback, $priority);
        $handles[] = $events->attach('hasItems.exception', $callback, $priority);

        $handles[] = $events->attach('getMetadata.exception', $callback, $priority);
        $handles[] = $events->attach('getMetadatas.exception', $callback, $priority);

        // non-blocking
        $handles[] = $events->attach('getDelayed.exception', $callback, $priority);
        $handles[] = $events->attach('find.exception', $callback, $priority);

        $handles[] = $events->attach('fetch.exception', $callback, $priority);
        $handles[] = $events->attach('fetchAll.exception', $callback, $priority);

        // write
        $handles[] = $events->attach('setItem.exception', $callback, $priority);
        $handles[] = $events->attach('setItems.exception', $callback, $priority);

        $handles[] = $events->attach('addItem.exception', $callback, $priority);
        $handles[] = $events->attach('addItems.exception', $callback, $priority);

        $handles[] = $events->attach('replaceItem.exception', $callback, $priority);
        $handles[] = $events->attach('replaceItems.exception', $callback, $priority);

        $handles[] = $events->attach('touchItem.exception', $callback, $priority);
        $handles[] = $events->attach('touchItems.exception', $callback, $priority);

        $handles[] = $events->attach('removeItem.exception', $callback, $priority);
        $handles[] = $events->attach('removeItems.exception', $callback, $priority);

        $handles[] = $events->attach('checkAndSetItem.exception', $callback, $priority);

        // increment / decrement item(s)
        $handles[] = $events->attach('incrementItem.exception', $callback, $priority);
        $handles[] = $events->attach('incrementItems.exception', $callback, $priority);

        $handles[] = $events->attach('decrementItem.exception', $callback, $priority);
        $handles[] = $events->attach('decrementItems.exception', $callback, $priority);

        // clear
        $handles[] = $events->attach('clear.exception', $callback, $priority);
        $handles[] = $events->attach('clearByNamespace.exception', $callback, $priority);

        // additional
        $handles[] = $events->attach('optimize.exception', $callback, $priority);
        $handles[] = $events->attach('getCapacity.exception', $callback, $priority);

        return $this;
    }

    /**
     * Detach
     *
     * @param  EventManagerInterface $events
     * @return ExceptionHandler
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
     * On exception
     *
     * @param  ExceptionEvent $event
     * @return void
     */
    public function onException(ExceptionEvent $event)
    {
        $options  = $this->getOptions();
        $callback = $options->getExceptionCallback();
        if ($callback) {
            call_user_func($callback, $event->getException());
        }

        $event->setThrowException($options->getThrowExceptions());
    }
}
