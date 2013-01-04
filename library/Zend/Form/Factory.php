<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form;

use ArrayAccess;
use ReflectionClass;
use Traversable;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\InputFilterInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Hydrator;

/**
 * @category   Zend
 * @package    Zend_Form
 */
class Factory
{
    /**
     * @var InputFilterFactory
     */
    protected $inputFilterFactory;

    /**
     * Set input filter factory to use when creating forms
     *
     * @param  InputFilterFactory $inputFilterFactory
     * @return Factory
     */
    public function setInputFilterFactory(InputFilterFactory $inputFilterFactory)
    {
        $this->inputFilterFactory = $inputFilterFactory;
        return $this;
    }

    /**
     * Get current input filter factory
     *
     * If none provided, uses an unconfigured instance.
     *
     * @return InputFilterFactory
     */
    public function getInputFilterFactory()
    {
        if (null === $this->inputFilterFactory) {
            $this->setInputFilterFactory(new InputFilterFactory());
        }
        return $this->inputFilterFactory;
    }

    /**
     * Create an element, fieldset, or form
     *
     * Introspects the 'type' key of the provided $spec, and determines what
     * type is being requested; if none is provided, assumes the spec
     * represents simply an element.
     *
     * @param  array|Traversable $spec
     * @return ElementInterface
     * @throws Exception\DomainException
     */
    public function create($spec)
    {
        $spec = $this->validateSpecification($spec, __METHOD__);
        $type = isset($spec['type']) ? $spec['type'] : 'Zend\Form\Element';

        if (self::isSubclassOf($type, 'Zend\Form\FormInterface')) {
            return $this->createForm($spec);
        }

        if (self::isSubclassOf($type, 'Zend\Form\FieldsetInterface')) {
            return $this->createFieldset($spec);
        }

        if (self::isSubclassOf($type, 'Zend\Form\ElementInterface')) {
            return $this->createElement($spec);
        }

        throw new Exception\DomainException(sprintf(
            '%s expects the $spec["type"] to implement one of %s, %s, or %s; received %s',
            __METHOD__,
            'Zend\Form\ElementInterface',
            'Zend\Form\FieldsetInterface',
            'Zend\Form\FormInterface',
            $type
        ));
    }

    /**
     * Create an element based on the provided specification
     *
     * Specification can contain any of the following:
     * - type: the Element class to use; defaults to \Zend\Form\Element
     * - name: what name to provide the element, if any
     * - options: an array, Traversable, or ArrayAccess object of element options
     * - attributes: an array, Traversable, or ArrayAccess object of element
     *   attributes to assign
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @return ElementInterface
     * @throws Exception\InvalidArgumentException for an invalid $spec
     * @throws Exception\DomainException for an invalid element type
     */
    public function createElement($spec)
    {
        $spec = $this->validateSpecification($spec, __METHOD__);

        $type       = isset($spec['type'])       ? $spec['type']       : 'Zend\Form\Element';
        $name       = isset($spec['name'])       ? $spec['name']       : null;
        $options    = isset($spec['options'])    ? $spec['options']    : null;
        $attributes = isset($spec['attributes']) ? $spec['attributes'] : null;

        $element = new $type();
        if (!$element instanceof ElementInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects an element type that implements Zend\Form\ElementInterface; received "%s"',
                __METHOD__,
                $type
            ));
        }

        if ($name !== null && $name !== '') {
            $element->setName($name);
        }

        if (is_array($options) || $options instanceof Traversable || $options instanceof ArrayAccess) {
            $element->setOptions($options);
        }

        if (is_array($attributes) || $attributes instanceof Traversable || $attributes instanceof ArrayAccess) {
            $element->setAttributes($attributes);
        }

        return $element;
    }

    /**
     * Create a fieldset based on the provided specification
     *
     * Specification can contain any of the following:
     * - type: the Fieldset class to use; defaults to \Zend\Form\Fieldset
     * - name: what name to provide the fieldset, if any
     * - options: an array, Traversable, or ArrayAccess object of element options
     * - attributes: an array, Traversable, or ArrayAccess object of element
     *   attributes to assign
     * - elements: an array or Traversable object where each entry is an array
     *   or ArrayAccess object containing the keys:
     *   - flags: (optional) array of flags to pass to FieldsetInterface::add()
     *   - spec: the actual element specification, per {@link createElement()}
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @return FieldsetInterface
     * @throws Exception\InvalidArgumentException for an invalid $spec
     * @throws Exception\DomainException for an invalid fieldset type
     */
    public function createFieldset($spec)
    {
        $spec = $this->validateSpecification($spec, __METHOD__);

        $type = isset($spec['type']) ? $spec['type'] : 'Zend\Form\Fieldset';
        $spec['type'] = $type;

        $fieldset = $this->createElement($spec);
        if (!$fieldset instanceof FieldsetInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects a fieldset type that implements Zend\Form\FieldsetInterface; received "%s"',
                __METHOD__,
                $type
            ));
        }

        if (isset($spec['object'])) {
            $this->prepareAndInjectObject($spec['object'], $fieldset, __METHOD__);
        }

        if (isset($spec['hydrator'])) {
            $this->prepareAndInjectHydrator($spec['hydrator'], $fieldset, __METHOD__);
        }

        if (isset($spec['elements'])) {
            $this->prepareAndInjectElements($spec['elements'], $fieldset, __METHOD__);
        }

        if (isset($spec['fieldsets'])) {
            $this->prepareAndInjectFieldsets($spec['fieldsets'], $fieldset, __METHOD__);
        }

        return $fieldset;
    }

    /**
     * Create a form based on the provided specification
     *
     * Specification follows that of {@link createFieldset()}, and adds the
     * following keys:
     *
     * - input_filter: input filter instance, named input filter class, or
     *   array specification for the input filter factory
     * - hydrator: hydrator instance or named hydrator class
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @return FormInterface
     * @throws Exception\InvalidArgumentException for an invalid $spec
     * @throws Exception\DomainException for an invalid form type
     */
    public function createForm($spec)
    {
        $spec = $this->validateSpecification($spec, __METHOD__);

        $type = isset($spec['type']) ? $spec['type'] : 'Zend\Form\Form';
        $spec['type'] = $type;

        $form = $this->createFieldset($spec);
        if (!$form instanceof FormInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects a form type that implements Zend\Form\FormInterface; received "%s"',
                __METHOD__,
                $type
            ));
        }

        if (isset($spec['input_filter'])) {
            $this->prepareAndInjectInputFilter($spec['input_filter'], $form, __METHOD__);
        }

        if (isset($spec['validation_group'])) {
            $this->prepareAndInjectValidationGroup($spec['validation_group'], $form, __METHOD__);
        }

        return $form;
    }

    /**
     * Validate a provided specification
     *
     * Ensures we have an array, Traversable, or ArrayAccess object, and returns it.
     *
     * @param  array|Traversable|ArrayAccess $spec
     * @param  string $method Method invoking the validator
     * @return array|ArrayAccess
     * @throws Exception\InvalidArgumentException for invalid $spec
     */
    protected function validateSpecification($spec, $method)
    {
        if (is_array($spec)) {
            return $spec;
        }

        if ($spec instanceof Traversable) {
            $spec = ArrayUtils::iteratorToArray($spec);
            return $spec;
        }

        if (!$spec instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array, or object implementing Traversable or ArrayAccess; received "%s"',
                $method,
                (is_object($spec) ? get_class($spec) : gettype($spec))
            ));
        }

        return $spec;
    }

    /**
     * Takes a list of element specifications, creates the elements, and injects them into the provided fieldset
     *
     * @param  array|Traversable|ArrayAccess $elements
     * @param  FieldsetInterface $fieldset
     * @param  string $method Method invoking this one (for exception messages)
     * @return void
     */
    protected function prepareAndInjectElements($elements, FieldsetInterface $fieldset, $method)
    {
        $elements = $this->validateSpecification($elements, $method);

        foreach ($elements as $elementSpecification) {
            $flags = isset($elementSpecification['flags']) ? $elementSpecification['flags'] : array();
            $spec  = isset($elementSpecification['spec'])  ? $elementSpecification['spec']  : array();

            $element = $this->createElement($spec);
            $fieldset->add($element, $flags);
        }
    }

    /**
     * Takes a list of fieldset specifications, creates the fieldsets, and injects them into the master fieldset
     *
     * @param  array|Traversable|ArrayAccess $fieldsets
     * @param  FieldsetInterface $masterFieldset
     * @param  string $method Method invoking this one (for exception messages)
     * @return void
     */
    public function prepareAndInjectFieldsets($fieldsets, FieldsetInterface $masterFieldset, $method)
    {
        $fieldsets = $this->validateSpecification($fieldsets, $method);

        foreach ($fieldsets as $fieldsetSpecification) {
            $flags = isset($fieldsetSpecification['flags']) ? $fieldsetSpecification['flags'] : array();
            $spec  = isset($fieldsetSpecification['spec'])  ? $fieldsetSpecification['spec']  : array();

            $fieldset = $this->createFieldset($spec);
            $masterFieldset->add($fieldset, $flags);
        }
    }

    /**
     * Prepare and inject an object
     *
     * Takes a string indicating a class name, instantiates the class
     * by that name, and injects the class instance as the bound object.
     *
     * @param  string           $objectName
     * @param  FieldsetInterface $fieldset
     * @param  string           $method
     * @throws Exception\DomainException
     * @return void
     */
    protected function prepareAndInjectObject($objectName, FieldsetInterface $fieldset, $method)
    {
        if (!is_string($objectName)) {
            throw new Exception\DomainException(sprintf(
                '%s expects string class name; received "%s"',
                $method,
                (is_object($objectName) ? get_class($objectName) : gettype($objectName))
            ));
        }

        if (!class_exists($objectName)) {
            throw new Exception\DomainException(sprintf(
                '%s expects string class name to be a valid class name; received "%s"',
                $method,
                $objectName
            ));
        }

        $fieldset->setObject(new $objectName);
    }

    /**
     * Prepare and inject a named hydrator
     *
     * Takes a string indicating a hydrator class name (or a concrete instance), instantiates the class
     * by that name, and injects the hydrator instance into the form.
     *
     * @param  string $hydratorOrName
     * @param  FieldsetInterface $fieldset
     * @param  string $method
     * @return void
     * @throws Exception\DomainException If $hydratorOrName is not a string, does not resolve to a known class, or
     *                                   the class does not implement Hydrator\HydratorInterface
     */
    protected function prepareAndInjectHydrator($hydratorOrName, FieldsetInterface $fieldset, $method)
    {
        if (is_object($hydratorOrName) && $hydratorOrName instanceof Hydrator\HydratorInterface) {
            $fieldset->setHydrator($hydratorOrName);
            return;
        }

        if (!is_string($hydratorOrName)) {
            throw new Exception\DomainException(sprintf(
                '%s expects string hydrator class name; received "%s"',
                $method,
                (is_object($hydratorOrName) ? get_class($hydratorOrName) : gettype($hydratorOrName))
            ));
        }

        if (!class_exists($hydratorOrName)) {
            throw new Exception\DomainException(sprintf(
                '%s expects string hydrator name to be a valid class name; received "%s"',
                $method,
                $hydratorOrName
            ));
        }

        $hydrator = new $hydratorOrName;
        if (!$hydrator instanceof Hydrator\HydratorInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects a valid implementation of Zend\Form\Hydrator\HydratorInterface; received "%s"',
                $method,
                $hydratorOrName
            ));
        }

        $fieldset->setHydrator($hydrator);
    }

    /**
     * Prepare an input filter instance and inject in the provided form
     *
     * If the input filter specified is a string, assumes it is a class name,
     * and attempts to instantiate it. If the class does not exist, or does
     * not extend InputFilterInterface, an exception is raised.
     *
     * Otherwise, $spec is passed on to the attached InputFilter Factory
     * instance in order to create the input filter.
     *
     * @param  string|array|Traversable $spec
     * @param  FormInterface $form
     * @param  string $method
     * @return void
     * @throws Exception\DomainException for unknown InputFilter class or invalid InputFilter instance
     */
    protected function prepareAndInjectInputFilter($spec, FormInterface $form, $method)
    {
        if (is_string($spec)) {
            if (!class_exists($spec)) {
                throw new Exception\DomainException(sprintf(
                    '%s expects string input filter names to be valid class names; received "%s"',
                    $method,
                    $spec
                ));
            }
            $filter = new $spec;
            if (!$filter instanceof InputFilterInterface) {
                throw new Exception\DomainException(sprintf(
                    '%s expects a valid implementation of Zend\InputFilter\InputFilterInterface; received "%s"',
                    $method,
                    $spec
                ));
            }
            $form->setInputFilter($filter);
            return;
        }

        $factory = $this->getInputFilterFactory();
        $filter  = $factory->createInputFilter($spec);
        $form->setInputFilter($filter);
    }

    /**
     * Prepare a validation group and inject in the provided form
     *
     * Takes an array of elements names
     *
     * @param  string|array|Traversable $spec
     * @param  FormInterface $form
     * @param  string $method
     * @return void
     * @throws Exception\DomainException if validation group given is not an array
     */
    protected function prepareAndInjectValidationGroup($spec, FormInterface $form, $method)
    {
        if (!is_array($spec)) {
            if (!class_exists($spec)) {
                throw new Exception\DomainException(sprintf(
                    '%s expects an array for validation group; received "%s"',
                    $method,
                    $spec
                ));
            }
        }

        $form->setValidationGroup($spec);
    }

    /**
     * Checks if the object has this class as one of its parents
     *
     * @see https://bugs.php.net/bug.php?id=53727
     * @see https://github.com/zendframework/zf2/pull/1807
     *
     * @param string $className
     * @param string $type
     * @return bool
     */
    protected static function isSubclassOf($className, $type)
    {
        if (is_subclass_of($className, $type)) {
            return true;
        }
        if (version_compare(PHP_VERSION, '5.3.7', '>=')) {
            return false;
        }
        if (!interface_exists($type)) {
            return false;
        }
        $r = new ReflectionClass($className);
        return $r->implementsInterface($type);
    }
}
