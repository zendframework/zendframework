<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
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
