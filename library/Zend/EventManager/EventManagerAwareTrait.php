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
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $event_manager = null;

    /**
     * setEventManager
     *
     * @param \Zend\EventManager\EventManagerInterface $eventManager
     * @return
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->event_manager = $eventManager;

        return $this;
    }
}
