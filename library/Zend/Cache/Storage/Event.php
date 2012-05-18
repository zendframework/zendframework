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

namespace Zend\Cache\Storage;

use ArrayObject,
    Zend\EventManager\Event as BaseEvent;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Event extends BaseEvent
{
    /**
     * Constructor
     *
     * Accept a storage adapter and its parameters.
     *
     * @param  string $name Event name
     * @param  Adapter\AdapterInterface $storage
     * @param  ArrayObject $params
     * @return void
     */
    public function __construct($name, Adapter\AdapterInterface $storage, ArrayObject $params)
    {
        parent::__construct($name, $storage, $params);
    }

    /**
     * Set the event target/context
     *
     * @param  Adapter $target
     * @return Event
     * @see    \Zend\EventManager\Event::setTarget()
     */
    public function setTarget($target)
    {
        return $this->setStorage($target);
    }

    /**
     * Alias of setTarget
     *
     * @param  Adapter\AdapterInterface $adapter
     * @return Event
     * @see    \Zend\EventManager\Event::setTarget()
     */
    public function setStorage(Adapter\AdapterInterface $adapter)
    {
        $this->target = $adapter;
        return $this;
    }

    /**
     * Alias of getTarget
     *
     * @return Adapter\AdapterInterface
     */
    public function getStorage()
    {
        return $this->getTarget();
    }
}
