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

/**
 * @category   Zend
 * @package    Zend_InputFilter
 */
class Input implements InputInterface
{
    protected $allowEmpty = false;
    protected $breakOnFailure = false;
    protected $errorMessage;
    protected $filterChain;
    protected $name;
    protected $notEmptyValidator = false;
    protected $required = true;
    protected $validatorChain;
    protected $value;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = (bool) $allowEmpty;
        return $this;
    }

    public function setBreakOnFailure($breakOnFailure)
    {
        $this->breakOnFailure = (bool) $breakOnFailure;
        return $this;
    }

    public function setErrorMessage($errorMessage)
    {
        $errorMessage = (null === $errorMessage) ? null : (string) $errorMessage;
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function setFilterChain(FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;
        return $this;
    }

    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    public function setRequired($required)
    {
        $this->required = (bool) $required;
        return $this;
    }

    public function setValidatorChain(ValidatorChain $validatorChain)
    {
        $this->validatorChain = $validatorChain;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function allowEmpty()
    {
        return $this->allowEmpty;
    }

    public function breakOnFailure()
    {
        return $this->breakOnFailure;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getFilterChain()
    {
        if (!$this->filterChain) {
            $this->setFilterChain(new FilterChain());
        }
        return $this->filterChain;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRawValue()
    {
        return $this->value;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function getValidatorChain()
    {
        if (!$this->validatorChain) {
            $this->setValidatorChain(new ValidatorChain());
        }
        return $this->validatorChain;
    }

    public function getValue()
    {
        $filter = $this->getFilterChain();
        return $filter->filter($this->value);
    }

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
    }

    public function isValid($context = null)
    {
        $this->injectNotEmptyValidator();
        $validator = $this->getValidatorChain();
        $value     = $this->getValue();
        return $validator->isValid($value, $context);
    }

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
        if (!$this->isRequired() && $this->allowEmpty() && !$this->notEmptyValidator) {
            return;
        }
        $chain = $this->getValidatorChain();
        $chain->prependByName('NotEmpty', array(), true);
        $this->notEmptyValidator = true;
    }
}
