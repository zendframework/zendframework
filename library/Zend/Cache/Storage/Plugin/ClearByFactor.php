<?php

namespace Zend\Cache\Storage\Plugin;

use Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\Adapter,
    Zend\Cache\Storage\PostEvent,
    Zend\Cache\InvalidArgumentAxception,
    Zend\EventManager\EventCollection;

class ClearByFactor implements Plugin
{

    /**
     * Automatic clearing factor
     *
     * @var int
     */
    protected $_clearingFactor = 0;

    /**
     * Flag to clear items by namespace
     *
     * @var boolean
     */
    protected $_clearByNamespace = true;

    protected $handles = array();

    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        foreach ($options as $name => $value) {
            $m = 'set' . str_replace('_', '', $name);
            $this->$m($value);
        }
    }

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
        return $this->_clearingFactor;
    }

    /**
     * Set automatic clearing factor
     *
     * @param int $factor
     * @return Zend\Cache\Storage\Plugin\ClearByFactor Fluent interface
     */
    public function setClearingFactor($factor)
    {
        $factor = (int)$factor;
        if ($factor < 0) {
            throw new InvalidArgumentAxception("Invalid clearing factor '{$factor}': must be greater or equal 0");
        }
        $this->_clearingFactor = $factor;

        return $this;
    }

    /**
     * Get flag to cleat items by namespace
     *
     * @return boolean
     */
    public function getClearByNamespace()
    {
        return $this->_clearByNamespace;
    }

    /**
     * Set flag to clear items by namespace
     *
     * @param boolean $flag
     * @return Zend\Cache\Storage\Plugin\ClearByFactor Fluent interface
     */
    public function setClearByNamespace($flag)
    {
        $this->_clearByNamespace = $flag;
        return $this;
    }

    public function attach(EventCollection $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        if (isset($this->handles[$index])) {
            throw new LogicException('Plugin already attached');
        }

        $handles = array();
        $this->handles[$index] = & $handles;

        $handles[] = $eventCollection->attach('setItem.post',  array($this, 'clearByFactor'));
        $handles[] = $eventCollection->attach('setItems.post', array($this, 'clearByFactor'));
        $handles[] = $eventCollection->attach('addItem.post',  array($this, 'clearByFactor'));
        $handles[] = $eventCollection->attach('addItems.post', array($this, 'clearByFactor'));

        return $this;
    }

    public function detach(EventCollection $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        if (!isset($this->handles[$index])) {
            throw new LogicException('Plugin not attached');
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
     * @param Zend\Cache\Storage\PostEvent $event
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
