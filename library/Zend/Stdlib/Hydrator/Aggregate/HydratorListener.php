<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Aggregate;


use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class HydratorListener extends AbstractListenerAggregate
{
    /**
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(HydrateEvent::EVENT_HYDRATE, array($this, 'onHydrate'));
        $this->listeners[] = $events->attach(ExtractEvent::EVENT_EXTRACT, array($this, 'onExtract'));
    }

    /**
     * @internal
     */
    public function onHydrate(HydrateEvent $event)
    {
        $object = $this->hydrator->hydrate($event->getHydrationData(), $event->getHydratedObject());

        $event->setHydratedObject($object);

        return $object;
    }

    /**
     * @internal
     */
    public function onExtract(ExtractEvent $event)
    {
        $data = $this->hydrator->extract($event->getExtractionObject());

        $event->mergeExtractedData($data);

        return $data;
    }
}
