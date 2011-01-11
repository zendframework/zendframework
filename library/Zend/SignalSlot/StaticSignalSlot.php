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
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\SignalSlot;

/**
 * Static version of Signals
 *
 * @category   Zend
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticSignalSlot implements StaticSignalManager
{
    /**
     * @var StaticSignalSlot
     */
    protected static $instance;

    /**
     * Identifiers with signal connections
     * @var array
     */
    protected $identifiers = array();

    /**
     * Singleton
     * 
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * Retrieve signals instance
     * 
     * @return SignalSlot
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Reset the singleton instance
     * 
     * @return void
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    /**
     * Attach a slot to a signal
     *
     * Allows attaching a callback to a signal offerred by one or more 
     * identifying components. As an example, the following connects to the 
     * "getAll" signal of both an AbstractResource and EntityResource:
     *
     * <code>
     * StaticSignalSlot::getInstance()->connect(
     *     array('My\Resource\AbstractResource', 'My\Resource\EntityResource'),
     *     'getOne',
     *     function ($resource, array $params) use ($cache) {
     *         $id = $params['id'] ?: false;
     *         if (!$id) {
     *             return;
     *         }
     *         if (!$data = $cache->load(get_class($resource) . '::getOne::' . $id )) {
     *             return;
     *         }
     *         return $data;
     *     }
     * );
     * </code>
     * 
     * @param  string|array $id Identifier(s) for signal emitting component(s)
     * @param  string|SignalAggregate $signal 
     * @param  null|callback $callback PHP Callback
     * @param  int $priority Priority at which slot should execute
     * @return void
     */
    public function connect($id, $signalOrAggregate, $callback = null, $priority = 1000)
    {
        $ids = (array) $id;
        foreach ($ids as $id) {
            if (!array_key_exists($id, $this->identifiers)) {
                $this->identifiers[$id] = new SignalSlot();
            }
            $this->identifiers[$id]->connect($signalOrAggregate, $callback, $priority);
        }
    }

    /**
     * Detach a slot from a signal offered by a given resource
     * 
     * @param  string|int $id
     * @param  SignalAggregate|\Zend\Stdlib\CallbackHandler $slot 
     * @return bool Returns true if signal and slot found, and unsubscribed; returns false if either signal or slot not found
     */
    public function detach($id, $slot)
    {
        if (!array_key_exists($id, $this->identifiers)) {
            return false;
        }
        return $this->identifiers[$id]->detach($slot);
    }

    /**
     * Retrieve all registered signals
     * 
     * @param  string|int $id
     * @return array
     */
    public function getSignals($id)
    {
        if (!array_key_exists($id, $this->identifiers)) {
            return false;
        }
        return $this->identifiers[$id]->getSignals();
    }

    /**
     * Retrieve all slots for a given identifier and signal
     * 
     * @param  string|int $id
     * @param  string|int $signal 
     * @return false|\Zend\Stdlib\PriorityQueue
     */
    public function getSlots($id, $signal)
    {
        if (!array_key_exists($id, $this->identifiers)) {
            return false;
        }
        return $this->identifiers[$id]->getSlots($signal);
    }

    /**
     * Clear all slots for a given identifier, optionally for a specific signal
     * 
     * @param  string|int $id 
     * @param  null|string $signal 
     * @return bool
     */
    public function clearSlots($id, $signal = null)
    {
        if (!array_key_exists($id, $this->identifiers)) {
            return false;
        }

        if (null === $signal) {
            unset($this->identifiers[$id]);
            return true;
        }

        return $this->identifiers[$id]->clearSlots($signal);
    }
}
