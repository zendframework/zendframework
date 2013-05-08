<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Aggregate;


use Zend\EventManager\Event;

class HydrateEvent extends Event
{
    const EVENT_HYDRATE = 'hydrate';

    /**
     * {@inheritDoc}
     */
    protected $name = self::EVENT_HYDRATE;

    /**
     * @var object
     */
    protected $hydratedObject;

    /**
     * @var array
     */
    protected $hydrationData;

    public function __construct($target, $hydratedObject, array $hydrationData)
    {
        $this->target         = $target;
        $this->hydratedObject = $hydratedObject;
        $this->hydrationData  = $hydrationData;
    }

    public function getHydratedObject()
    {
        return $this->hydratedObject;
    }

    public function setHydratedObject($hydratedObject)
    {
        $this->hydratedObject = $hydratedObject;
    }

    public function getHydrationData()
    {
        return $this->hydrationData;
    }

    public function setHydrationData(array $hydrationData)
    {
        $this->hydrationData = $hydrationData;
    }
}