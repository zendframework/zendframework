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
     * @var SharedEventManagerInterface
     */
    protected $sharedEventManager = null;

    /**
     * Inject a SharedEventManager instance
     *
     * @param SharedEventManagerInterface $sharedEventManager
     * @return mixed
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager)
    {
        $this->sharedEventManager = $sharedEventManager;

        return $this;
    }

    /**
     * Get shared collections container
     *
     * @return SharedEventManagerInterface
     */
    public function getSharedManager()
    {
        return $this->sharedEventManager;
    }

    /**
     * Remove any shared collections
     *
     * @return mixed
     */
    public function unsetSharedManager()
    {
        $this->sharedEventManager = null;

        return $this;
    }
}
