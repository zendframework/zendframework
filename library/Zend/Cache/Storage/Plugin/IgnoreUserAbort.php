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

use Zend\Cache\Exception,
    Zend\Cache\Storage\Adapter,
    Zend\Cache\Storage\Event,
    Zend\EventManager\EventCollection;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IgnoreUserAbort extends AbstractPlugin
{
    /**
     * Handles
     *
     * @var array
     */
    protected $handles = array();

    /**
     * The storage adapter target who activated ignore_user_abort.
     *
     * @var null|Adapter
     */
    protected $activatedTarget = null;

    /**
     * Attach
     *
     * @param  EventCollection $eventCollection
     * @return Serializer
     * @throws Exception\LogicException
     */
    public function attach(EventCollection $events)
    {
        $index = spl_object_hash($events);
        if (isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin already attached');
        }

        $handles = array();
        $this->handles[$index] = & $handles;

        $cbOnBefore = array($this, 'onBefore');
        $cbOnAfter  = array($this, 'onAfter');

        $handles[] = $events->attach('setItem.pre',       $cbOnBefore);
        $handles[] = $events->attach('setItem.post',      $cbOnAfter);
        $handles[] = $events->attach('setItem.exception', $cbOnAfter);

        $handles[] = $events->attach('setItems.pre',       $cbOnBefore);
        $handles[] = $events->attach('setItems.post',      $cbOnAfter);
        $handles[] = $events->attach('setItems.exception', $cbOnAfter);

        $handles[] = $events->attach('addItem.pre',       $cbOnBefore);
        $handles[] = $events->attach('addItem.post',      $cbOnAfter);
        $handles[] = $events->attach('addItem.exception', $cbOnAfter);

        $handles[] = $events->attach('addItems.pre',       $cbOnBefore);
        $handles[] = $events->attach('addItems.post',      $cbOnAfter);
        $handles[] = $events->attach('addItems.exception', $cbOnAfter);

        $handles[] = $events->attach('replaceItem.pre',       $cbOnBefore);
        $handles[] = $events->attach('replaceItem.post',      $cbOnAfter);
        $handles[] = $events->attach('replaceItem.exception', $cbOnAfter);

        $handles[] = $events->attach('replaceItems.pre',       $cbOnBefore);
        $handles[] = $events->attach('replaceItems.post',      $cbOnAfter);
        $handles[] = $events->attach('replaceItems.exception', $cbOnAfter);

        $handles[] = $events->attach('checkAndSetItem.pre',       $cbOnBefore);
        $handles[] = $events->attach('checkAndSetItem.post',      $cbOnAfter);
        $handles[] = $events->attach('checkAndSetItem.exception', $cbOnAfter);

        // increment / decrement item(s)
        $handles[] = $events->attach('incrementItem.pre',       $cbOnBefore);
        $handles[] = $events->attach('incrementItem.post',      $cbOnAfter);
        $handles[] = $events->attach('incrementItem.exception', $cbOnAfter);

        $handles[] = $events->attach('incrementItems.pre',       $cbOnBefore);
        $handles[] = $events->attach('incrementItems.post',      $cbOnAfter);
        $handles[] = $events->attach('incrementItems.exception', $cbOnAfter);

        $handles[] = $events->attach('decrementItem.pre',       $cbOnBefore);
        $handles[] = $events->attach('decrementItem.post',      $cbOnAfter);
        $handles[] = $events->attach('decrementItem.exception', $cbOnAfter);

        $handles[] = $events->attach('decrementItems.pre',       $cbOnBefore);
        $handles[] = $events->attach('decrementItems.post',      $cbOnAfter);
        $handles[] = $events->attach('decrementItems.exception', $cbOnAfter);

        return $this;
    }

    /**
     * Detach
     *
     * @param  EventCollection $events
     * @return Serializer
     * @throws Exception\LogicException
     */
    public function detach(EventCollection $events)
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
     * Activate ignore_user_abort if not already done
     * and save the target who activated it.
     *
     * @param  Event $event
     * @return void
     */
    public function onBefore(Event $event)
    {
        if ($this->activatedTarget === null && !ignore_user_abort(true)) {
            $this->activatedTarget = $event->getTarget();
        }
    }

    /**
     * Reset ignore_user_abort if it's activated and if it's the same target
     * who activated it.
     *
     * If exit_on_abort is enabled and the connection has been aborted
     * exit the script.
     *
     * @param  Event $event
     * @return void
     */
    public function onAfter(Event $event)
    {
        if ($this->activatedTarget === $event->getTarget()) {
            // exit if connection aborted
            if ($this->getOptions()->getExitOnAbort() && connection_aborted()) {
                exit;
            }

            // reset ignore_user_abort
            ignore_user_abort(false);

            // remove activated target
            $this->activatedTarget = null;
        }
    }
}
