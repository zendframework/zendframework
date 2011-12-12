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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Plugin;

use Traversable,
    Zend\EventManager\EventCollection,
    Zend\Cache\Exception,
    Zend\Cache\Storage\Adapter,
    Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\PostEvent;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ClearByFactor implements Plugin
{
    /**
     * Automatic clearing factor
     *
     * @var int
     */
    protected $clearingFactor = 0;

    /**
     * Flag to clear items by namespace
     *
     * @var boolean
     */
    protected $clearByNamespace = true;

    /**
     * Handles
     *
     * @var array
     */
    protected $handles = array();

    /**
     * Constructor
     *
     * @param array|Traversable $options
     * @return void
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set options
     *
     * @param  array|Traversable $options
     * @return ClearByFactor
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        foreach ($options as $name => $value) {
            $m = 'set' . str_replace('_', '', $name);
            if (!method_exists($this, $m)) {
                continue;
            }
            $this->$m($value);
        }
        return $this;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            'clearing_factor'    => $this->getClearingFactor(),
            'clear_by_namespace' => $this->getClearByNamespace(),
        );
    }

    /**
     * Get automatic clearing factor
     *
     * @return int
     */
    public function getClearingFactor()
    {
        return $this->clearingFactor;
    }

    /**
     * Set automatic clearing factor
     *
     * @param  int $factor
     * @return ClearByFactor Fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setClearingFactor($factor)
    {
        $factor = (int) $factor;
        if ($factor < 0) {
            throw new Exception\InvalidArgumentAxception(
                "Invalid clearing factor '{$factor}': must be greater or equal 0"
            );
        }
        $this->clearingFactor = $factor;

        return $this;
    }

    /**
     * Get flag to cleat items by namespace
     *
     * @return boolean
     */
    public function getClearByNamespace()
    {
        return $this->clearByNamespace;
    }

    /**
     * Set flag to clear items by namespace
     *
     * @param  boolean $flag
     * @return ClearByFactor Fluent interface
     */
    public function setClearByNamespace($flag)
    {
        $this->clearByNamespace = (bool) $flag;
        return $this;
    }

    /**
     * Attach
     *
     * @param  EventCollection $eventCollection
     * @return ClearByFactor
     * @throws Exception\LogicException
     */
    public function attach(EventCollection $eventCollection)
    {
        $index = spl_object_hash($eventCollection);
        if (isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin already attached');
        }

        $handles = array();
        $this->handles[$index] = & $handles;

        $handles[] = $eventCollection->attach('setItem.post',  array($this, 'clearByFactor'));
        $handles[] = $eventCollection->attach('setItems.post', array($this, 'clearByFactor'));
        $handles[] = $eventCollection->attach('addItem.post',  array($this, 'clearByFactor'));
        $handles[] = $eventCollection->attach('addItems.post', array($this, 'clearByFactor'));

        return $this;
    }

    /**
     * Detach
     *
     * @param  EventCollection $eventCollection
     * @return ClearByFactor
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
     * Clear storage by factor on a success _RESULT_
     *
     * @param  PostEvent $event
     * @return void
     */
    public function clearByFactor(PostEvent $event)
    {
        $factor = $this->getClearingFactor();
        if ($factor && $event->getResult() && mt_rand(1, $factor) == 1) {
            $params = $event->getParams();
            if ($this->getClearByNamespace()) {
                $event->getStorage()->clearByNamespace(Adapter::MATCH_EXPIRED, $params['options']);
            } else {
                $event->getStorage()->clear(Adapter::MATCH_EXPIRED, $params['options']);
            }
        }
    }
}
