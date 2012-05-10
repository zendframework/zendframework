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

use IteratorAggregate;
use Traversable;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Hydrator;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BaseForm extends Fieldset implements FormInterface
{
    /**
     * Seed attributes
     * 
     * @var array
     */
    protected $attributes = array(
        'method' => 'POST',
    );

    /**
     * How to bind values to the attached object
     * 
     * @var int
     */
    protected $bindAs = FormInterface::VALUES_NORMALIZED;

    /**
     * Data being validated
     * 
     * @var null|array|\Traversable
     */
    protected $data;
 
    /**
     * @var null|InputFilterInterface
     */
    protected $filter;

    /**
     * Whether or not validation has occurred
     * 
     * @var bool
     */
    protected $hasValidated = false;

    /**
     * Hydrator to use with bound object
     * 
     * @var Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * The object bound to this form, if any
     * 
     * @var null|object
     */
    protected $object;

    /**
     * Validation group, if any
     * 
     * @var null|array
     */
    protected $validationGroup;

    /**
     * Set data to validate and/or populate elements
     *
     * Typically, also passes data on to the composed input filter.
     * 
     * @param  array|\ArrayAccess $data 
     * @return Form
     */
    public function setData($data)
    {
        if ($data instanceof Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }
        if (!is_array($data)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        $this->hasValidated = false;
        $this->data         = $data;
        $this->populateValues($data);

        return $this;
    }

    /**
     * Bind an object to the form
     *
     * Ensures the object is populated with validated values.
     * 
     * @param  object $object 
     * @return void
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if (!is_object($object)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an object argument; received "%s"',
                __METHOD__,
                $object
            ));
        }

        if (!in_array($flags, array(FormInterface::VALUES_NORMALIZED, FormInterface::VALUES_RAW))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects the $flags argument to be one of "%s" or "%s"; received "%s"',
                __METHOD__,
                'Zend\Form\FormInterface::VALUES_NORMALIZED',
                'Zend\Form\FormInterface::VALUES_RAW',
                $flags
            ));
        }

        $this->bindAs = $flags;
        $this->object = $object;
        $this->extract();
    }

    /**
     * Set the hydrator to use when binding an object to the form
     * 
     * @param  Hydrator\HydratorInterface $hydrator 
     * @return Form
     */
    public function setHydrator(Hydrator\HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * Get the hydrator used when binding an object to the form
     *
     * Will lazy-load Hydrator\ArraySerializable if none is present.
     * 
     * @return null|Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator instanceof Hydrator\HydratorInterface) {
            $this->setHydrator(new Hydrator\ArraySerializable());
        }
        return $this->hydrator;
    }

    /**
     * Validate the form
     *
     * Typically, will proxy to the composed input filter.
     * 
     * @return bool
     */
    public function isValid()
    {
        if (!is_array($this->data)) {
            throw new Exception\DomainException(sprintf(
                '%s is unable to validate as there is no data currently set',
                __METHOD__
            ));
        }

        $filter = $this->getInputFilter();
        if (!$filter instanceof InputFilterInterface) {
            throw new Exception\DomainException(sprintf(
                '%s is unable to validate as there is no input filter present',
                __METHOD__
            ));
        }

        $filter->setData($this->data);
        $filter->setValidationGroup(InputFilterInterface::VALIDATE_ALL);

        if ($this->validationGroup !== null) {
            $filter->setValidationGroup($this->validationGroup);
        }

        $result = $filter->isValid();
        if ($result) {
            $this->hydrate();
        }

        if (!$result) {
            $this->setMessages($filter->getMessages());
        }

        $this->hasValidated = true;
        return $result;
    }

    /**
     * Retrieve the validated data
     *
     * By default, retrieves normalized values; pass one of the 
     * FormInterface::VALUES_* constants to shape the behavior.
     * 
     * @param  int $flag 
     * @return array|object
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        if (!$this->hasValidated) {
            throw new Exception\DomainException(sprintf(
                '%s cannot return data as validation has not yet occurred',
                __METHOD__
            ));
        }

        if (($flag !== FormInterface::VALUES_AS_ARRAY) && is_object($this->object)) {
            return $this->object;
        }

        $filter = $this->getInputFilter();

        if ($flag === FormInterface::VALUES_RAW) {
            return $filter->getRawValues();
        }

        return $filter->getValues();
    }

    /**
     * Set the validation group (set of values to validate)
     *
     * Typically, proxies to the composed input filter
     *
     * @return FormInterface
     */
    public function setValidationGroup()
    {
        $argc = func_num_args();
        if (0 === $argc) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects at least one argument; none provided',
                __METHOD__
            ));
        }

        $argv = func_get_args();
        $this->hasValidated = false;

        if (1 < $argc) {
            $this->validationGroup = $argv;
            return $this;
        }

        $arg = array_shift($argv);
        if ($arg === FormInterface::VALIDATE_ALL) {
            $this->validationGroup = null;
            return $this;
        }

        if (!is_array($arg)) {
            $arg = (array) $arg;
        }
        $this->validationGroup = $arg;
        return $this;
    }

    /**
     * Set the input filter used by this form
     * 
     * @param  InputFilterInterface $inputFilter 
     * @return Form
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->hasValidated = false;
        $this->filter       = $inputFilter;
        return $this;
    }

    /**
     * Retrieve input filter used by this form
     * 
     * @return null|InputFilterInterface
     */
    public function getInputFilter()
    {
        if ($this->object instanceof InputFilterAwareInterface) {
            return $this->object->getInputFilter();
        }
        return $this->filter;
    }

    /**
     * Hydrate the attached object
     * 
     * @return void
     */
    protected function hydrate()
    {
        if (!is_object($this->object)) {
            return;
        }
        $hydrator = $this->getHydrator();
        $filter   = $this->getInputFilter();

        switch ($this->bindAs) {
            case FormInterface::VALUES_RAW:
                $data = $filter->getRawValues();
                break;
            case FormInterface::VALUES_NORMALIZED:
            default:
                $data = $filter->getValues();
                break;
        }
        $hydrator->hydrate($data, $this->object);
    }

    /**
     * Extract values from the bound object and populate
     * the form elements
     * 
     * @return void
     */
    protected function extract()
    {
        if (!is_object($this->object)) {
            return;
        }
        $hydrator = $this->getHydrator();
        if (!$hydrator instanceof Hydrator\HydratorInterface) {
            return;
        }

        $values = $hydrator->extract($this->object);
        if (!is_array($values)) {
            // Do nothing if the hydrator returned a non-array
            return;
        }

        $this->populateValues($values);
    }
}
