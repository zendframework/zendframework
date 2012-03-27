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
    Zend\EventManager\EventCollection;

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
     * @param  EventCollection $eventCollection
     * @return ExceptionHandler
     * @throws Exception\LogicException
     */
    public function attach(EventCollection $eventCollection)
    {
        $index = spl_object_hash($eventCollection);
        if (isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin already attached');
        }

        $callback = array($this, 'onException');
        $handles  = array();
        $this->handles[$index] = & $handles;

        // read
        $handles[] = $eventCollection->attach('getItem.exception', $callback);
        $handles[] = $eventCollection->attach('getItems.exception', $callback);

        $handles[] = $eventCollection->attach('hasItem.exception', $callback);
        $handles[] = $eventCollection->attach('hasItems.exception', $callback);

        $handles[] = $eventCollection->attach('getMetadata.exception', $callback);
        $handles[] = $eventCollection->attach('getMetadatas.exception', $callback);

        // non-blocking
        $handles[] = $eventCollection->attach('getDelayed.exception', $callback);
        $handles[] = $eventCollection->attach('find.exception', $callback);

        $handles[] = $eventCollection->attach('fetch.exception', $callback);
        $handles[] = $eventCollection->attach('fetchAll.exception', $callback);

        // write
        $handles[] = $eventCollection->attach('setItem.exception', $callback);
        $handles[] = $eventCollection->attach('setItems.exception', $callback);

        $handles[] = $eventCollection->attach('addItem.exception', $callback);
        $handles[] = $eventCollection->attach('addItems.exception', $callback);

        $handles[] = $eventCollection->attach('replaceItem.exception', $callback);
        $handles[] = $eventCollection->attach('replaceItems.exception', $callback);

        $handles[] = $eventCollection->attach('touchItem.exception', $callback);
        $handles[] = $eventCollection->attach('touchItems.exception', $callback);

        $handles[] = $eventCollection->attach('removeItem.exception', $callback);
        $handles[] = $eventCollection->attach('removeItems.exception', $callback);

        $handles[] = $eventCollection->attach('checkAndSetItem.exception', $callback);

        // increment / decrement item(s)
        $handles[] = $eventCollection->attach('incrementItem.exception', $callback);
        $handles[] = $eventCollection->attach('incrementItems.exception', $callback);

        $handles[] = $eventCollection->attach('decrementItem.exception', $callback);
        $handles[] = $eventCollection->attach('decrementItems.exception', $callback);

        // clear
        $handles[] = $eventCollection->attach('clear.exception', $callback);
        $handles[] = $eventCollection->attach('clearByNamespace.exception', $callback);

        // additional
        $handles[] = $eventCollection->attach('optimize.exception', $callback);
        $handles[] = $eventCollection->attach('getCapacity.exception', $callback);

        return $this;
    }

    /**
     * Detach
     *
     * @param  EventCollection $eventCollection
     * @return ExceptionHandler
     * @throws Exception\LogicException
     */
    public function detach(EventCollection $eventCollection)
    {
        $index = spl_object_hash($eventCollection);
        if (!isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin not attached');
        }

        // detach all handles of this index
        foreach ($this->handles[$index] as $handle) {
            $eventCollection->detach($handle);
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
