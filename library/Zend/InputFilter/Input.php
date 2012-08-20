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

use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;
use Zend\Validator\NotEmpty;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 */
class Input implements InputInterface
{
    /**
     * @var boolean
     */
    protected $allowEmpty = false;

    /**
     * @var boolean
     */
    protected $breakOnFailure = false;

    /**
     * @var string|null
     */
    protected $errorMessage;

    /**
     * @var FilterChain
     */
    protected $filterChain;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $notEmptyValidator = false;

    /**
     * @var boolean
     */
    protected $required = true;

    /**
     * @var ValidatorChain
     */
    protected $validatorChain;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * @param  boolean $allowEmpty
     * @return Input
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = (bool) $allowEmpty;
        return $this;
    }

    /**
     * @param  boolean $breakOnFailure
     * @return Input
     */
    public function setBreakOnFailure($breakOnFailure)
    {
        $this->breakOnFailure = (bool) $breakOnFailure;
        return $this;
    }

    /**
     * @param  string|null $errorMessage
     * @return Input
     */
    public function setErrorMessage($errorMessage)
    {
        $errorMessage = (null === $errorMessage) ? null : (string) $errorMessage;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @param  FilterChain $filterChain
     * @return Input
     */
    public function setFilterChain(FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;
        return $this;
    }

    /**
     * @param  string $name
     * @return Input
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * @param  boolean $required
     * @return Input
     */
    public function setRequired($required)
    {
        $this->required = (bool) $required;
        return $this;
    }

    /**
     * @param  ValidatorChain $validatorChain
     * @return Input
     */
    public function setValidatorChain(ValidatorChain $validatorChain)
    {
        $this->validatorChain = $validatorChain;
        return $this;
    }

    /**
     * @param  mixed $value
     * @return Input
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function allowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * @return boolean
     */
    public function breakOnFailure()
    {
        return $this->breakOnFailure;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (!$this->filterChain) {
            $this->setFilterChain(new FilterChain());
        }
        return $this->filterChain;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getRawValue()
    {
        return $this->value;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return ValidatorChain
     */
    public function getValidatorChain()
    {
        if (!$this->validatorChain) {
            $this->setValidatorChain(new ValidatorChain());
        }
        return $this->validatorChain;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $filter = $this->getFilterChain();
        return $filter->filter($this->value);
    }

    /**
     * @param  InputInterface $input
     * @return Input
     */
    public function merge(InputInterface $input)
    {
        $this->setAllowEmpty($input->allowEmpty());
        $this->setBreakOnFailure($input->breakOnFailure());
        $this->setErrorMessage($input->getErrorMessage());
        $this->setName($input->getName());
        $this->setRequired($input->isRequired());
        $this->setValue($input->getValue());

        $filterChain = $input->getFilterChain();
        $this->getFilterChain()->merge($filterChain);

        $validatorChain = $input->getValidatorChain();
        $this->getValidatorChain()->merge($validatorChain);
        return $this;
    }

    /**
     * @param  mixed $context Extra "context" to provide the validator
     * @return boolean
     */
    public function isValid($context = null)
    {
        $this->injectNotEmptyValidator();
        $validator = $this->getValidatorChain();
        $value     = $this->getValue();
        return $validator->isValid($value, $context);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        if (null !== $this->errorMessage) {
            return (array) $this->errorMessage;
        }

        $validator = $this->getValidatorChain();
        return $validator->getMessages();
    }

    protected function injectNotEmptyValidator()
    {
        if ((!$this->isRequired() && $this->allowEmpty()) || $this->notEmptyValidator) {
            return;
        }
        $chain = $this->getValidatorChain();

        // Check if NotEmpty validator is already first in chain
        $validators = $chain->getValidators();
        if (isset($validators[0]['instance'])
            && $validators[0]['instance'] instanceof NotEmpty
        ) {
            $this->notEmptyValidator = true;
            return;
        }

        $chain->prependByName('NotEmpty', array(), true);
        $this->notEmptyValidator = true;
    }
}
