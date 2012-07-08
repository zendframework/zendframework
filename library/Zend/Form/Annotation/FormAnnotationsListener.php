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

/**
 * Default listeners for form annotations
 *
 * Defines and attaches a set of default listeners for form annotations
 * (which are defined on object properties). These include:
 *
 * - Attributes
 * - Flags
 * - Hydrator
 * - InputFilter
 * - Type
 *
 * See the individual annotation classes for more details. The handlers 
 * registered work with the annotation values, as well as the form 
 * specification passed in the event object.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormAnnotationsListener extends AbstractAnnotationsListener
{
    /**
     * Attach listeners
     * 
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleAttributesAnnotation'));
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleFlagsAnnotation'));
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleHydratorAnnotation'));
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleInputFilterAnnotation'));
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleOptionsAnnotation'));
        $this->listeners[] = $events->attach('configureForm', array($this, 'handleTypeAnnotation'));

        $this->listeners[] = $events->attach('discoverName', array($this, 'handleNameAnnotation'));
        $this->listeners[] = $events->attach('discoverName', array($this, 'discoverFallbackName'));
    }

    /**
     * Handle the Attributes annotation
     *
     * Sets the attributes key of the form specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleAttributesAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Attributes) {
            return;
        }

        $formSpec = $e->getParam('formSpec');
        $formSpec['attributes'] = $annotation->getAttributes();
    }

    /**
     * Handle the Flags annotation
     *
     * Sets the flags key of the form specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleFlagsAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Flags) {
            return;
        }

        $formSpec = $e->getParam('formSpec');
        $formSpec['flags'] = $annotation->getFlags();
    }

    /**
     * Handle the Hydrator annotation
     *
     * Sets the hydrator class to use in the form specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleHydratorAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Hydrator) {
            return;
        }

        $formSpec = $e->getParam('formSpec');
        $formSpec['hydrator'] = $annotation->getHydrator();
    }

    /**
     * Handle the InputFilter annotation
     *
     * Sets the input filter class to use in the form specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleInputFilterAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof InputFilter) {
            return;
        }

        $formSpec = $e->getParam('formSpec');
        $formSpec['input_filter'] = $annotation->getInputFilter();
    }

    /**
     * Handle the Options annotation
     *
     * Sets the options key of the form specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleOptionsAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Options) {
            return;
        }

        $formSpec = $e->getParam('formSpec');
        $formSpec['options'] = $annotation->getOptions();
    }

    /**
     * Handle the Type annotation
     *
     * Sets the form class to use in the form specification.
     * 
     * @param  \Zend\EventManager\EventInterface $e 
     * @return void
     */
    public function handleTypeAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Type) {
            return;
        }

        $formSpec = $e->getParam('formSpec');
        $formSpec['type'] = $annotation->getType();
    }
}
