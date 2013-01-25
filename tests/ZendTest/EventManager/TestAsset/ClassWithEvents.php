<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace ZendTest\EventManager\TestAsset;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 */
class ClassWithEvents
{
    protected $events;

    public function getEventManager(EventManagerInterface $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        }
        if (null === $this->events) {
            $this->events = new EventManager(__CLASS__);
        }
        return $this->events;
    }

    public function foo()
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array());
    }
}
