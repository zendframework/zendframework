<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Aggregate;

use Traversable;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Exception;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\HydratorInterface;

class AggregateHydrator implements HydratorInterface, EventManagerAwareInterface
{
    const DEFAULT_PRIORITY = 1;
    const EVENT_HYDRATE    = 'hydrate';
    const EVENT_EXTRACT    = 'extract';
    const PARAM_OBJECT     = 'object';
    const PARAM_DATA       = 'data';

    /**
     * @var \Zend\EventManager\EventManagerInterface|null
     */
    protected $eventManager;

    public function add(HydratorInterface $hydrator, $priority = self::DEFAULT_PRIORITY)
    {
        $this->getEventManager()->attachAggregate(new HydratorListener($hydrator), $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        $results = $this->eventManager->trigger(static::EVENT_EXTRACT, $this, array(static::PARAM_OBJECT => $object));
        $data    = array();

        foreach ($results as $result) {
            if ($result instanceof Traversable) {
                $result = ArrayUtils::iteratorToArray($result);
            }

            if (!is_array($result)) {
                continue;
            }

            $data = ArrayUtils::merge($data, $result);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        $result = $this
            ->eventManager
            ->trigger(
                static::EVENT_EXTRACT,
                $this,
                array(
                     static::PARAM_OBJECT => $object,
                     static::PARAM_DATA   => $data,
                )
            )
            ->last();

        return is_object($result) ? $result : $object;
    }

    /**
     * {@inheritDoc}
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->eventManager = new EventManager();
        }

        return $this->eventManager;
    }
}
