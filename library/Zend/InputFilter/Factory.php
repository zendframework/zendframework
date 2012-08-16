<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace Zend\InputFilter;

use Traversable;
use Zend\Filter\FilterChain;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\ValidatorChain;
use Zend\Validator\ValidatorInterface;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 */
class Factory
{
    protected $defaultFilterChain;
    protected $defaultValidatorChain;

    /**
     * Set default filter chain to use
     *
     * @param  FilterChain $filterChain
     * @return Factory
     */
    public function setDefaultFilterChain(FilterChain $filterChain)
    {
        $this->defaultFilterChain = $filterChain;
        return $this;
    }

    /**
     * Get default filter chain, if any
     *
     * @return null|FilterChain
     */
    public function getDefaultFilterChain()
    {
        return $this->defaultFilterChain;
    }

    /**
     * Clear the default filter chain (i.e., don't inject one into new inputs)
     *
     * @return void
     */
    public function clearDefaultFilterChain()
    {
        $this->defaultFilterChain = null;
    }

    /**
     * Set default validator chain to use
     *
     * @param  ValidatorChain $validatorChain
     * @return Factory
     */
    public function setDefaultValidatorChain(ValidatorChain $validatorChain)
    {
        $this->defaultValidatorChain = $validatorChain;
        return $this;
    }

    /**
     * Get default validator chain, if any
     *
     * @return null|ValidatorChain
     */
    public function getDefaultValidatorChain()
    {
        return $this->defaultValidatorChain;
    }

    /**
     * Clear the default validator chain (i.e., don't inject one into new inputs)
     *
     * @return void
     */
    public function clearDefaultValidatorChain()
    {
        $this->defaultValidatorChain = null;
    }

    /**
     * Factory for input objects
     *
     * @param  array|Traversable $inputSpecification
     * @return InputInterface|InputFilterInterface
     */
    public function createInput($inputSpecification)
    {
        if (!is_array($inputSpecification) && !$inputSpecification instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable; received "%s"',
                __METHOD__,
                (is_object($inputSpecification) ? get_class($inputSpecification) : gettype($inputSpecification))
            ));
        }
        if ($inputSpecification instanceof Traversable) {
            $inputSpecification = ArrayUtils::iteratorToArray($inputSpecification);
        }

        $class = 'Zend\InputFilter\Input';
        if (isset($inputSpecification['type'])) {
            $class = $inputSpecification['type'];
            if (!class_exists($class)) {
                throw new Exception\RuntimeException(sprintf(
                    'Input factory expects the "type" to be a valid class; received "%s"',
                    $class
                ));
            }
        }
        $input = new $class();

        if ($input instanceof InputFilterInterface) {
            return $this->createInputFilter($inputSpecification);
        }

        if (!$input instanceof InputInterface) {
            throw new Exception\RuntimeException(sprintf(
                'Input factory expects the "type" to be a class implementing %s; received "%s"',
                'Zend\InputFilter\InputInterface',
                $class
            ));
        }

        if ($this->defaultFilterChain) {
            $input->setFilterChain(clone $this->defaultFilterChain);
        }
        if ($this->defaultValidatorChain) {
            $input->setValidatorChain(clone $this->defaultValidatorChain);
        }

        foreach ($inputSpecification as $key => $value) {
            switch ($key) {
                case 'name':
                    $input->setName($value);
                    break;
                case 'required':
                    $input->setRequired($value);
                    if (!isset($inputSpecification['allow_empty'])) {
                        $input->setAllowEmpty(!$value);
                    }
                    break;
                case 'allow_empty':
                    $input->setAllowEmpty($value);
                    if (!isset($inputSpecification['required'])) {
                        $input->setRequired(!$value);
                    }
                    break;
                case 'filters':
                    if ($value instanceof FilterChain) {
                        $input->setFilterChain($value);
                        break;
                    }
                    if (!is_array($value) && !$value instanceof Traversable) {
                        throw new Exception\RuntimeException(sprintf(
                            '%s expects the value associated with "filters" to be an array/Traversable of filters or filter specifications, or a FilterChain; received "%s"',
                            __METHOD__,
                            (is_object($value) ? get_class($value) : gettype($value))
                        ));
                    }
                    $this->populateFilters($input->getFilterChain(), $value);
                    break;
                case 'validators':
                    if ($value instanceof ValidatorChain) {
                        $input->setValidatorChain($value);
                        break;
                    }
                    if (!is_array($value) && !$value instanceof Traversable) {
                        throw new Exception\RuntimeException(sprintf(
                            '%s expects the value associated with "validators" to be an array/Traversable of validators or validator specifications, or a ValidatorChain; received "%s"',
                            __METHOD__,
                            (is_object($value) ? get_class($value) : gettype($value))
                        ));
                    }
                    $this->populateValidators($input->getValidatorChain(), $value);
                    break;
                default:
                    // ignore unknown keys
                    break;
            }
        }

        return $input;
    }

    /**
     * Factory for input filters
     *
     * @param  array|Traversable $inputFilterSpecification
     * @return InputFilterInterface
     */
    public function createInputFilter($inputFilterSpecification)
    {
        if (!is_array($inputFilterSpecification) && !$inputFilterSpecification instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable; received "%s"',
                __METHOD__,
                (is_object($inputFilterSpecification) ? get_class($inputFilterSpecification) : gettype($inputFilterSpecification))
            ));
        }
        if ($inputFilterSpecification instanceof Traversable) {
            $inputFilterSpecification = ArrayUtils::iteratorToArray($inputFilterSpecification);
        }

        $class = 'Zend\InputFilter\InputFilter';
        if (isset($inputFilterSpecification['type']) && is_string($inputFilterSpecification['type'])) {
            $class = $inputFilterSpecification['type'];
            if (!class_exists($class)) {
                throw new Exception\RuntimeException(sprintf(
                    'Input factory expects the "type" to be a valid class; received "%s"',
                    $class
                ));
            }
            unset($inputFilterSpecification['type']);
        }
        $inputFilter = new $class();

        if (!$inputFilter instanceof InputFilterInterface) {
            throw new Exception\RuntimeException(sprintf(
                'InputFilter factory expects the "type" to be a class implementing %s; received "%s"',
                'Zend\InputFilter\InputFilterInterface',
                $class
            ));
        }

        foreach ($inputFilterSpecification as $key => $value) {
            $input = $this->createInput($value);
            $inputFilter->add($input, $key);
        }

        return $inputFilter;
    }

    protected function populateFilters(FilterChain $chain, $filters)
    {
        foreach ($filters as $filter) {
            if (is_object($filter) || is_callable($filter)) {
                $chain->attach($filter);
                continue;
            }

            if (is_array($filter)) {
                if (!isset($filter['name'])) {
                    throw new Exception\RuntimeException(
                        'Invalid filter specification provided; does not include "name" key'
                    );
                }
                $name = $filter['name'];
                $options = array();
                if (isset($filter['options'])) {
                    $options = $filter['options'];
                }
                $chain->attachByName($name, $options);
                continue;
            }

            throw new Exception\RuntimeException(
                'Invalid filter specification provided; was neither a filter instance nor an array specification'
            );
        }
    }

    protected function populateValidators(ValidatorChain $chain, $validators)
    {
        foreach ($validators as $validator) {
            if ($validator instanceof ValidatorInterface) {
                $chain->addValidator($validator);
                continue;
            }

            if (is_array($validator)) {
                if (!isset($validator['name'])) {
                    throw new Exception\RuntimeException(
                        'Invalid validator specification provided; does not include "name" key'
                    );
                }
                $name    = $validator['name'];
                $options = array();
                if (isset($validator['options'])) {
                    $options = $validator['options'];
                }
                $breakChainOnFailure = false;
                if (isset($validator['break_chain_on_failure'])) {
                    $breakChainOnFailure = $validator['break_chain_on_failure'];
                }
                $chain->addByName($name, $options, $breakChainOnFailure);
                continue;
            }

            throw new Exception\RuntimeException(
                'Invalid validator specification provided; was neither a validator instance nor an array specification'
            );
        }
    }
}
