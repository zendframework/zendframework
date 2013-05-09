<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Aggregate;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\Exception;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\HydratorInterface;

class AggregateHydrator implements HydratorInterface, EventManagerAwareInterface
{
    const DEFAULT_PRIORITY = 1;

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
        $event = new ExtractEvent($this, $object);

        $this->getEventManager()->trigger($event);

        return $event->getExtractedData();
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        $event = new HydrateEvent($this, $object, $data);

        $this->getEventManager()->trigger($event);

        return $event->getHydratedObject();
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
