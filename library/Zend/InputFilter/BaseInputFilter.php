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
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\InputFilter;

use ArrayAccess;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @todo       How should we deal with required input when data is missing? 
 *             should a message be returned? if so, what message?
 * @category   Zend
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BaseInputFilter implements InputFilterInterface
{
    protected $data;
    protected $inputs = array();
    protected $invalidInputs;
    protected $validationGroup;
    protected $validInputs;

    /**
     * Countable: number of inputs in this input filter
     *
     * Only details the number of direct children.
     * 
     * @return int
     */
    public function count()
    {
        return count($this->inputs);
    }

    /**
     * Add an input to the input filter
     * 
     * @param  InputInterface|InputFilterInterface $input 
     * @param  null|string $name Name used to retrieve this input
     * @return InputFilterInterface
     */
    public function add($input, $name = null)
    {
        if (!$input instanceof InputInterface && !$input instanceof InputFilterInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an instance of %s or %s as its first argument; received "%s"',
                __METHOD__,
                'Zend\InputFilter\InputInterface',
                'Zend\InputFilter\InputFilterInterface',
                (is_object($input) ? get_class($input) : gettype($input))
            ));
        }

        if (empty($name)) {
            $name = $input->getName();
        }
        $this->inputs[$name] = $input;
        return $this;
    }

    /**
     * Retrieve a named input
     * 
     * @param  string $name 
     * @return InputInterface|InputFilterInterface
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->inputs)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: no input found matching "%s"',
                __METHOD__,
                $name
            ));
        }
        return $this->inputs[$name];
    }

    /**
     * Test if an input or input filter by the given name is attached
     * 
     * @param  string $name 
     * @return bool
     */
    public function has($name)
    {
        return (array_key_exists($name, $this->inputs));
    }

    /**
     * Set data to use when validating and filtering
     * 
     * @param  array|Traversable $data 
     * @return InputFilterInterface
     */
    public function setData($data)
    {
        if (!is_array($data) && !$data instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received %s',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }
        if (is_object($data) && !$data instanceof ArrayAccess) {
            $data = ArrayUtils::iteratorToArray($data);
        }
        $this->data = $data;
        $this->populate();
        return $this;
    }

    /**
     * Is the data set valid?
     * 
     * @return bool
     */
    public function isValid()
    {
        if (null === $this->data) {
            throw new Exception\RuntimeException(sprintf(
                '%s: no data present to validate!',
                __METHOD__
            ));
        }

        $this->validInputs   = array();
        $this->invalidInputs = array();
        $valid               = true;
        
        $inputs = $this->validationGroup ?: array_keys($this->inputs);
        foreach ($inputs as $name) {
            $input = $this->inputs[$name];

            if (!isset($this->data[$name])) {
                // Not sure how to handle input filters in this case
                if ($input instanceof InputFilterInterface) {
                    if (!$input->isValid()) {
                        $this->invalidInputs[$name] = $input;
                        $valid = false;
                        continue;
                    }
                    $this->validInputs[$name] = $input;
                    continue;
                }

                // no matching value in data
                // - test if input is required
                // - test if input allows empty
                if (!$input->isRequired()) {
                    $this->validInputs[$name] = $input;
                    continue;
                }

                if ($input->allowEmpty()) {
                    $this->validInputs[$name] = $input;
                    continue;
                }

                // How do we mark the input as invalid in this case?
                // (for purposes of a validation error message)

                // Mark validation as having failed
                $this->invalidInputs[$name] = $input;
                $valid = false;
                if ($input->breakOnFailure()) {
                    // We failed validation, and this input is marked to
                    // break on failure
                    return false;
                }
                continue;
            }

            $value = $this->data[$name];
            if ($input instanceof InputFilterInterface) {
                if (!$input->isValid()) {
                    $this->invalidInputs[$name] = $input;
                    $valid = false;
                    continue;
                }
                $this->validInputs[$name] = $input;
                continue;
            }
            if ($input instanceof InputInterface) {
                if (!$input->isValid($this->data)) {
                    // Validation failure
                    $this->invalidInputs[$name] = $input;
                    $valid = false;

                    if ($input->breakOnFailure()) {
                        return false;
                    }
                    continue;
                }
                $this->validInputs[$name] = $input;
                continue;
            }
        }

        return $valid;
    }

    /**
     * Provide a list of one or more elements indicating the complete set to validate
     *
     * When provided, calls to {@link isValid()} will only validate the provided set.
     *
     * If the initial value is {@link VALIDATE_ALL}, the current validation group, if
     * any, should be cleared.
     *
     * Implementations should allow passing a single array value, or multiple arguments,
     * each specifying a single input.
     * 
     * @param  mixed $name 
     * @return InputFilterInterface
     */
    public function setValidationGroup($name)
    {
        if ($name === self::VALIDATE_ALL) {
            $this->validationGroup = null;
            return $this;
        }

        if (is_array($name)) {
            $this->validateValidationGroup($name);
            $this->validationGroup = $name;
            return $this;
        }

        $inputs = func_get_args();
        $this->validateValidationGroup($inputs);
        $this->validationGroup = $inputs;
        return $this;
    }

    /**
     * Return a list of inputs that were invalid.
     *
     * Implementations should return an associative array of name/input pairs
     * that failed validation.
     * 
     * @return InputInterface[]
     */
    public function getInvalidInput()
    {
        return (is_array($this->invalidInputs) ? $this->invalidInputs : array());
    }

    /**
     * Return a list of inputs that were valid.
     *
     * Implementations should return an associative array of name/input pairs
     * that passed validation.
     * 
     * @return InputInterface[]
     */
    public function getValidInput()
    {
        return (is_array($this->validInputs) ? $this->validInputs : array());
    }

    /**
     * Retrieve a value from a named input
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getValue($name)
    {
        if (!array_key_exists($name, $this->inputs)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid input name; "%s" was not found in the filter',
                __METHOD__,
                $name
            ));
        }
        $input = $this->inputs[$name];
        return $input->getValue();
    }

    /**
     * Return a list of filtered values
     *
     * List should be an associative array, with the values filtered. If
     * validation failed, this should raise an exception.
     * 
     * @return array
     */
    public function getValues()
    {
        $inputs = $this->validationGroup ?: array_keys($this->inputs);
        $values = array();
        foreach ($inputs as $name) {
            $input = $this->inputs[$name];

            if ($input instanceof InputFilterInterface) {
                $values[$name] = $input->getValues();
                continue;
            }
            $values[$name] = $input->getValue();
        }
        return $values;
    }

    /**
     * Retrieve a raw (unfiltered) value from a named input
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getRawValue($name)
    {
        if (!array_key_exists($name, $this->inputs)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid input name; "%s" was not found in the filter',
                __METHOD__,
                $name
            ));
        }
        $input = $this->inputs[$name];
        return $input->getRawValue();
    }

    /**
     * Return a list of unfiltered values
     *
     * List should be an associative array of named input/value pairs,
     * with the values unfiltered.
     * 
     * @return array
     */
    public function getRawValues()
    {
        $values = array();
        foreach ($this->inputs as $name => $input) {
            if ($input instanceof InputFilterInterface) {
                $values[$name] = $input->getRawValues();
                continue;
            }
            $values[$name] = $input->getRawValue();
        }
        return $values;
    }

    /**
     * Return a list of validation failure messages
     *
     * Should return an associative array of named input/message list pairs.
     * Pairs should only be returned for inputs that failed validation.
     * 
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        foreach ($this->getInvalidInput() as $name => $input) {
            $messages[$name] = $input->getMessages();
        }
        return $messages;
    }

    /**
     * Ensure all names of a validation group exist as input in the filter
     * 
     * @param  array $inputs 
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    protected function validateValidationGroup(array $inputs)
    {
        foreach ($inputs as $name) {
            if (!array_key_exists($name, $this->inputs)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'setValidationGroup() expects a list of valid input names; "%s" was not found',
                    $name
                ));
            }
        }
    }

    /**
     * Populate the values of all attached inputs
     * 
     * @return void
     */
    protected function populate()
    {
        foreach (array_keys($this->inputs) as $name) {
            $input = $this->inputs[$name];

            if (!isset($this->data[$name])) {
                // No value; clear value in this input
                if ($input instanceof InputFilterInterface) {
                    $input->setData(array());
                    continue;
                }

                $input->setValue(null);
                continue;
            }

            $value = $this->data[$name];

            if ($input instanceof InputFilterInterface) {
                $input->setData($value);
                continue;
            }

            $input->setValue($value);
        }
    }
}
