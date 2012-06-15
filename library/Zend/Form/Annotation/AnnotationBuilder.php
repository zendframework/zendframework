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

use ArrayObject;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Factory;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AnnotationBuilder implements EventManagerAwareInterface
{
    protected $annotationManager;
    protected $events;
    protected $formFactory;

    /**
     * Set form factory to use when building form from annotations
     * 
     * @param  Factory $formFactory 
     * @return AnnotationBuilder
     */
    public function setFormFactory(Factory $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * Set annotation manager to use when building form from annotations
     * 
     * @param  AnnotationManager $annotationManager 
     * @return AnnotationBuilder
     */
    public function setAnnotationManager(AnnotationManager $annotationManager)
    {
        $this->annotationManager = $annotationManager;
        return $this;
    }

    /**
     * Set event manager instance
     * 
     * @param  EventManagerInterface $events 
     * @return AnnotationBuilder
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_class($this),
        ));
        $events->attach(new ElementAnnotationsListener());
        $events->attach(new FormAnnotationsListener());
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve form factory
     *
     * Lazy-loads the default form factory if none is currently set.
     * 
     * @return Factory
     */
    public function getFormFactory()
    {
        if ($this->formFactory) {
            return $this->formFactory;
        }

        $this->formFactory = new Factory();
        return $this->formFactory;
    }

    /**
     * Retrieve annotation manager
     *
     * If none is currently set, creates one with default annotations.
     * 
     * @return AnnotationManager
     */
    public function getAnnotationManager()
    {
        if ($this->annotationManager) {
            return $this->annotationManager;
        }

        $this->annotationManager = new AnnotationManager(array(
            new AllowEmpty(),
            new Attributes(),
            new ErrorMessage(),
            new Exclude(),
            new Filter(),
            new Flags(),
            new Hydrator(),
            new Input(),
            new InputFilter(),
            new Name(),
            new Required(),
            new Type(),
            new Validator(),
        ));
        return $this->annotationManager;
    }

    /**
     * Get event manager
     * 
     * @return EventManagerInterface
     */
    public function events()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * Creates and returns a form specification for use with a factory
     *
     * Parses the object provided, and processes annotations for the class and 
     * all properties. Information from annotations is then used to create 
     * specfications for a form, its elements, and its input filter.
     * 
     * @param  object $entity 
     * @return array
     */
    public function getFormSpecification($entity)
    {
        if (!is_object($entity)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an object; received "%s"',
                __METHOD__,
                gettype($entity)
            ));
        }

        $annotationManager = $this->getAnnotationManager();
        $formSpec          = new ArrayObject();
        $filterSpec        = new ArrayObject();

        $reflection  = new ClassReflection($entity);
        $annotations = $reflection->getAnnotations($annotationManager);

        if ($annotations instanceof AnnotationCollection) {
            $this->configureForm($annotations, $reflection, $formSpec, $filterSpec);
        }

        foreach ($reflection->getProperties() as $property) {
            $annotations = $property->getAnnotations($annotationManager);

            if ($annotations instanceof AnnotationCollection) {
                $this->configureElement($annotations, $property, $formSpec, $filterSpec);
            }
        }

        if (!isset($formSpec['input_filter'])) {
            $formSpec['input_filter'] = $filterSpec;
        }

        return ArrayUtils::iteratorToArray($formSpec);
    }

    /**
     * Create a form from an object.
     *
     * @param  object $entity 
     * @return \Zend\Form\Form
     */
    public function createForm($entity)
    {
        $formSpec    = $this->getFormSpecification($entity);
        $formFactory = $this->getFormFactory();
        return $formFactory->createForm($formSpec);
    }

    /**
     * Configure the form specification from annotations
     * 
     * @param  AnnotationCollection $annotations 
     * @param  ClassReflection $reflection 
     * @param  ArrayObject $formSpec 
     * @param  ArrayObject $filterSpec 
     * @return void
     * @triggers discoverName
     * @triggers configureForm
     */
    protected function configureForm($annotations, $reflection, $formSpec, $filterSpec)
    {
        $name                   = $this->discoverName($annotations, $reflection);
        $formSpec['name']       = $name;
        $formSpec['attributes'] = array();
        $formSpec['elements']   = array();
        $formSpec['fieldsets']  = array();

        $events = $this->events();
        foreach ($annotations as $annotation) {
            $events->trigger(__FUNCTION__, $this, array(
                'annotation' => $annotation, 
                'name'        => $name,
                'formSpec'   => $formSpec, 
                'filterSpec' => $filterSpec,
            ));
        }
    }

    /**
     * Configure an element from annotations
     * 
     * @param  AnnotationCollection $annotations 
     * @param  \Zend\Code\Reflection\PropertyReflection $reflection 
     * @param  ArrayObject $formSpec 
     * @param  ArrayObject $filterSpec 
     * @return void
     * @triggers discoverName
     * @triggers configureElement
     */
    protected function configureElement($annotations, $reflection, $formSpec, $filterSpec)
    {
        // If the element is marked as exclude, return early
        if ($annotations->hasAnnotation('Zend\Form\Annotation\Exclude')) {
            return;
        }

        $events = $this->events();
        $name   = $this->discoverName($annotations, $reflection);

        $elementSpec = new ArrayObject(array(
            'flags' => array(),
            'spec'  => array(
                'name' => $name
            ),
        ));
        $inputSpec   = new ArrayObject(array('name' => $name));

        foreach ($annotations as $annotation) {
            $events->trigger(__FUNCTION__, $this, array(
                'annotation'  => $annotation, 
                'name'        => $name,
                'elementSpec' => $elementSpec,
                'inputSpec'   => $inputSpec,
                'formSpec'    => $formSpec, 
                'filterSpec'  => $filterSpec,
            ));
        }

        $filterSpec[$name] = $inputSpec;

        if (!isset($formSpec['elements'])) {
            $formSpec['elements'] = array();
        }
        $formSpec['elements'][] = $elementSpec;
    }

    /**
     * Discover the name of the given form or element
     * 
     * @param  AnnotationCollection $annotations 
     * @param  \Reflector $reflection 
     * @return string
     */
    protected function discoverName($annotations, $reflection)
    {
        $results = $this->events()->trigger('discoverName', $this, array(
            'annotations' => $annotations,
            'reflection'  => $reflection,
        ), function ($r) {
            return (is_string($r) && !empty($r));
        });
        return $results->last();
    }
}
