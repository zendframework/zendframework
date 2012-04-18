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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form;

use ArrayAccess;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Factory
{
    /**
     * Create an element based on the provided specification
     *
     * Specification can contain any of the following:
     * - type: the Element class to use; defaults to \Zend\Form\Element
     * - name: what name to provide the element, if any
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
        $attributes = isset($spec['attributes']) ? $spec['attributes'] : null;

        $element = new $type();
        if (!$element instanceof ElementInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects an element type that implements Zend\Form\ElementInterface; received "%s"',
                __METHOD__,
                $type
            ));
        }

        if ($name) {
            $element->setName($name);
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
     * @throws Exception\DomainException for an invalid element type
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

        if (isset($spec['elements'])) {
            $this->prepareAndInjectElements($spec['elements'], $fieldset, __METHOD__);
        }

        if (isset($spec['fieldsets'])) {
            $this->prepareAndInjectFieldsets($spec['fieldsets'], $fieldset, __METHOD__);
        }

        return $fieldset;
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
}
