<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;

interface InputInterface
{
    /**
     * @param bool $allowEmpty
     * @return InputInterface
     */
    public function setAllowEmpty($allowEmpty);

    /**
     * @param bool $breakOnFailure
     * @return InputInterface
     */
    public function setBreakOnFailure($breakOnFailure);

    /**
     * @param string|null $errorMessage
     * @return InputInterface
     */
    public function setErrorMessage($errorMessage);

    /**
     * @param FilterChain $filterChain
     * @return InputInterface
     */
    public function setFilterChain(FilterChain $filterChain);

    /**
     * @param string $name
     * @return InputInterface
     */
    public function setName($name);

    /**
     * @param bool $required
     * @return InputInterface
     */
    public function setRequired($required);

    /**
     * @param ValidatorChain $validatorChain
     * @return InputInterface
     */
    public function setValidatorChain(ValidatorChain $validatorChain);

    /**
     * @param mixed $value
     * @return InputInterface
     */
    public function setValue($value);

    /**
     * @param InputInterface $input
     * @return InputInterface
     */
    public function merge(InputInterface $input);

    /**
     * @return bool
     */
    public function allowEmpty();

    /**
     * @return bool
     */
    public function breakOnFailure();

    /**
     * @return string|null
     */
    public function getErrorMessage();

    /**
     * @return FilterChain
     */
    public function getFilterChain();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getRawValue();

    /**
     * @return bool
     */
    public function isRequired();

    /**
     * @return ValidatorChain
     */
    public function getValidatorChain();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return array
     */
    public function getMessages();
}
