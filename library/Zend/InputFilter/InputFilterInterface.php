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

use Countable;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface InputFilterInterface extends Countable
{
    const VALIDATE_ALL = 'INPUT_FILTER_ALL';

    /**
     * Add an input to the input filter
     * 
     * @param  InputInterface|InputFilterInterface $input 
     * @param  null|string $name Name used to retrieve this input
     * @return InputFilterInterface
     */
    public function add($input, $name = null);

    /**
     * Retrieve a named input
     * 
     * @param  string $name 
     * @return InputInterface|InputFilterInterface
     */
    public function get($name);

    /**
     * Test if an input or input filter by the given name is attached
     * 
     * @param  string $name 
     * @return bool
     */
    public function has($name);

    /**
     * Set data to use when validating and filtering
     * 
     * @param  array|Traversable $data 
     * @return InputFilterInterface
     */
    public function setData($data);

    /**
     * Is the data set valid?
     * 
     * @return bool
     */
    public function isValid();

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
    public function setValidationGroup($name);

    /**
     * Return a list of inputs that were invalid.
     *
     * Implementations should return an associative array of name/input pairs
     * that failed validation.
     * 
     * @return InputInterface[]
     */
    public function getInvalidInput();

    /**
     * Return a list of inputs that were valid.
     *
     * Implementations should return an associative array of name/input pairs
     * that passed validation.
     * 
     * @return InputInterface[]
     */
    public function getValidInput();

    /**
     * Retrieve a value from a named input
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getValue($name);

    /**
     * Return a list of filtered values
     *
     * List should be an associative array, with the values filtered. If
     * validation failed, this should raise an exception.
     * 
     * @return array
     */
    public function getValues();

    /**
     * Retrieve a raw (unfiltered) value from a named input
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getRawValue($name);

    /**
     * Return a list of unfiltered values
     *
     * List should be an associative array of named input/value pairs,
     * with the values unfiltered.
     * 
     * @return array
     */
    public function getRawValues();

    /**
     * Return a list of validation failure messages
     *
     * Should return an associative array of named input/message list pairs.
     * Pairs should only be returned for inputs that failed validation.
     * 
     * @return array
     */
    public function getMessages();
}
