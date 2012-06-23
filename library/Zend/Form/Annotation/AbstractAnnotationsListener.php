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
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Annotation;

use ReflectionClass;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Base annotations listener.
 *
 * Provides an implementation of detach() that should work with any listener. 
 * Also provides listeners for the "Name" annotation -- handleNameAnnotation()
 * will listen for the "Name" annotation, while discoverFallbackName() listens
 * on the "discoverName" event and will use the class or property name, as
 * discovered via reflection, if no other annotation has provided the name
 * already.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAnnotationsListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Detach listeners
     * 
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if (false !== $events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Attempt to discover a name set via annotation
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return false|string
     */
    public function handleNameAnnotation($e)
    {
        $annotations = $e->getParam('annotations');

        if (!$annotations->hasAnnotation('Zend\Form\Annotation\Name')) {
            return false;
        }

        foreach ($annotations as $annotation) {
            if (!$annotation instanceof Name) {
                continue;
            }
            return $annotation->getName();
        }

        return false;
    }

    /**
     * Discover the fallback name via reflection
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return string
     */
    public function discoverFallbackName($e)
    {
        $reflection = $e->getParam('reflection');
        if ($reflection instanceof ReflectionClass) {
            return $reflection->getShortName();
        }

        return $reflection->getName();
    }
}
