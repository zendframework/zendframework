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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Plugin;

use Traversable,
    Zend\Cache\Exception,
    Zend\Cache\Storage\OptimizableInterface,
    Zend\Cache\Storage\PostEvent,
    Zend\EventManager\EventManagerInterface;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class OptimizeByFactor extends AbstractPlugin
{
    /**
     * Handles
     *
     * @var array
     */
    protected $handles = array();

    /**
     * Attach
     *
     * @param  EventManagerInterface $events
     * @param  int                   $priority
     * @return OptimizeByFactor
     * @throws Exception\LogicException
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $index = spl_object_hash($events);
        if (isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin already attached');
        }

        $handles = array();
        $this->handles[$index] = & $handles;

        $callback = array($this, 'optimizeByFactor');
        $handles[] = $events->attach('removeItem.post',  $callback, $priority);
        $handles[] = $events->attach('removeItems.post', $callback, $priority);

        return $this;
    }

    /**
     * Detach
     *
     * @param  EventManagerInterface $events
     * @return OptimizeByFactor
     * @throws Exception\LogicException
     */
    public function detach(EventManagerInterface $events)
    {
        $index = spl_object_hash($events);
        if (!isset($this->handles[$index])) {
            throw new Exception\LogicException('Plugin not attached');
        }

        // detach all handles of this index
        foreach ($this->handles[$index] as $handle) {
            $events->detach($handle);
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
        $storage = $event->getStorage();
        if ( !($storage instanceof OptimizableInterface) ) {
            return;
        }

        $factor = $this->getOptions()->getOptimizingFactor();
        if ($factor && mt_rand(1, $factor) == 1) {
            $storage->optimize();
        }
    }
}
