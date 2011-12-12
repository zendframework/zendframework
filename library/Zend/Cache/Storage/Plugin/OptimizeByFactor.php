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
    Zend\Cache\Exception,
    Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\PostEvent,
    Zend\EventManager\EventCollection;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class OptimizeByFactor implements Plugin
{
    /**
     * Handles
     *
     * @var array
     */
    protected $handles = array();

    /**
     * Automatic optimizing factor
     *
     * @var int
     */
    protected $optimizingFactor = 0;

    /**
     * Constructor
     *
     * @param  array|Traversable $options
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
     * @return OptimizeByFactor
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
            'optimizing_factor' => $this->getOptimizingFactor(),
        );
    }

    /**
     * Get automatic optimizing factor
     *
     * @return int
     */
    public function getOptimizingFactor()
    {
        return $this->optimizingFactor;
    }

    /**
     * Set automatic optimizing factor
     *
     * @param  int $factor
     * @return OptimizeByFactor
     * @throws Exception\InvalidArgumentAxception
     */
    public function setOptimizingFactor($factor)
    {
        $factor = (int) $factor;
        if ($factor < 0) {
            throw new Exception\InvalidArgumentAxception(
                "Invalid optimizing factor '{$factor}': must be greater or equal 0"
            );
        }
        $this->optimizingFactor = $factor;

        return $this;
    }

    /**
     * Attach
     *
     * @param  EventCollection $eventCollection
     * @return OptimizeByFactor
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

        $handles[] = $eventCollection->attach('removeItem.post',       array($this, 'optimizeByFactor'));
        $handles[] = $eventCollection->attach('removeItems.post',      array($this, 'optimizeByFactor'));
        $handles[] = $eventCollection->attach('clear.post',            array($this, 'optimizeByFactor'));
        $handles[] = $eventCollection->attach('clearByNamespace.post', array($this, 'optimizeByFactor'));

        return $this;
    }

    /**
     * Detach
     *
     * @param  EventCollection $eventCollection
     * @return OptimizeByFactor
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
     * Optimize by factor on a success _RESULT_
     *
     * @param  PostEvent $event
     * @return void
     */
    public function optimizeByFactor(PostEvent $event)
    {
        $factor = $this->getOptimizingFactor();
        if ($factor && $event->getResult() && mt_rand(1, $factor) == 1) {
            $params = $event->getParams();
            $event->getStorage()->optimize($params['options']);
        }
    }
}
