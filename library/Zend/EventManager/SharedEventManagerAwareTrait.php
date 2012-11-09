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

use \Zend\EventManager\SharedEventManagerInterface;

/**
 * @category Zend
 * @package  Zend_EventManager
 */
trait SharedEventManagerAwareTrait
{
    /**
     * @var \Zend\EventManager\SharedEventManagerInterface
     */
    protected $shared_event_manager = null;

    /**
     * setSharedManager
     *
     * @param \Zend\EventManager\SharedEventManagerInterface $sharedEventManager
     * @return
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager)
    {
        $this->shared_event_manager = $sharedEventManager;

        return $this;
    }

    /**
     * getSharedManager
     *
     * @return \Zend\EventManager\SharedEventManagerInterface
     */
    public function getSharedManager()
    {
        return $this->shared_event_manager;
    }

    /**
     * unsetSharedManager
     *
     * @return
     */
    public function unsetSharedManager()
    {
        $this->shared_event_manager = null;

        return $this;
    }
}
