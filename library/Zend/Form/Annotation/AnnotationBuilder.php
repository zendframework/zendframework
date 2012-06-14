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
use Zend\EventManager\EventsCapableInterface;
use Zend\Form\Factory;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AnnotationBuilder implements
    EventManagerAwareInterface,
    EventsCapableInterface
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
            new BreakOnFailure(),
            new Element(),
            new ErrorMessage(),
            new Exclude(),
            new Fieldset(),
            new Filter(),
            new Form(),
            new Input(),
            new InputFilter(),
            new Name(),
            new Required(),
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
     * Create a form from an object.
     *
     * Parses the object provided, and processes annotations for the class and 
     * all properties. Information from annotations is then used to create a 
     * form, its elements, and its input filter.
     * 
     * @param  object $entity 
     * @return \Zend\Form\Form
     */
    public function createForm($entity)
    {
        if (!is_object($entity)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an object; received "%s"',
                __METHOD__,
                gettype($entity)
            ));
        }

        $formFactory       = $this->getFormFactory();
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

        $formSpec = ArrayUtils::iteratorToArray($formSpec);
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
     * @triggers configureForm
     */
    protected function configureForm($annotations, $reflection, $formSpec, $filterSpec)
    {
        $name = $reflection->getShortName();
        if ($annotations->hasAnnotation('Zend\Form\Annotation\Name')) {
            $name = $this->getNameFromAnnotation($annotations);
        }
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
     * @triggers configureElement
     */
    protected function configureElement($annotations, $reflection, $formSpec, $filterSpec)
    {
        // If the element is marked as exclude, return early
        if ($annotations->hasAnnotation('Zend\Form\Annotation\Exclude')) {
            return;
        }

        $name = $reflection->getName();
        if ($annotations->hasAnnotation('Zend\Form\Annotation\Name')) {
            $name = $this->getNameFromAnnotation($annotations);
        }

        $elementSpec = new ArrayObject(array(
            'flags' => array(),
            'spec'  => array(
                'name' => $name
            ),
        ));
        $inputSpec   = new ArrayObject(array('name' => $name));

        $events = $this->events();
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
     * Retrieve the name from an annotation
     *
     * Loops through annotations until a Name annotation is encountered. Once 
     * encountered, the value of getName() is returned.
     * 
     * @param  AnnotationCollection $annotations 
     * @return string|false
     */
    protected function getNameFromAnnotation($annotations)
    {
        foreach ($annotations as $annotation) {
            if (!$annotation instanceof Name) {
                continue;
            }
            return $annotation->getName();
        }
        return false;
    }
}
