<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace Zend\EventManager;

use Zend\EventManager\EventManagerInterface;

/**
 * @category Zend
 * @package  Zend_EventManager
 */
trait EventManagerAwareTrait
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager = null;

    /**
     * Inject an EventManager instance
     *
     * @param EventManagerInterface $eventManager
     * @return mixed
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}
