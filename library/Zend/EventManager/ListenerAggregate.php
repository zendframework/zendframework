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
 * @package    Zend_EventManager
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\EventManager;

/**
 * Interface for self-registering event listeners.
 *
 * Classes implementing this interface may be registered by name or instance
 * with an EventManager, without an event name. The {@link attach()} method will
 * then be called with the current EventManager instance, allowing the class to
 * wire up one or more listeners.
 *
 * @category   Zend
 * @package    Zend_EventManager
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ListenerAggregate
{
    /**
     * Attach one or more listeners
     *
     * @param EventCollection $events
     */
    public function attach(EventCollection $events);

    /**
     * Detach all previously attached listeners
     *
     * @param EventCollection $events
     */
    public function detach(EventCollection $events);
}
