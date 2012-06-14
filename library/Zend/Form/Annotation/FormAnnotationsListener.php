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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormAnnotationsListener implements ListenerAggregateInterface
{
    protected $listeners = array();

    /**
     * Attach listeners
     * 
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleFieldsetAnnotation'));
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleFormAnnotation'));
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleInputFilterAnnotation'));
    }

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

    public function handleFieldsetAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Fieldset) {
            return;
        }

        $formSpec = $e->getParam('formSpec');

        foreach ($annotation->getSpecification() as $key => $value) {
            $formSpec[$key] = $value;
        }
    }

    public function handleFormAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Form) {
            return;
        }

        $formSpec = $e->getParam('formSpec');

        foreach ($annotation->getSpecification() as $key => $value) {
            $formSpec[$key] = $value;
        }
    }

    public function handleInputFilterAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof InputFilter) {
            return;
        }

        $formSpec = $e->getParam('formSpec');
        $formSpec['input_filter'] = $annotation->getInputFilter();
    }
}
