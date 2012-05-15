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

use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface InputInterface
{
    public function setAllowEmpty($allowEmpty);
    public function setBreakOnFailure($breakOnFailure);
    public function setErrorMessage($errorMessage);
    public function setFilterChain(FilterChain $filterChain);
    public function setName($name);
    public function setRequired($required);
    public function setValidatorChain(ValidatorChain $validatorChain);
    public function setValue($value);

    public function allowEmpty();
    public function breakOnFailure();
    public function getFilterChain();
    public function getName();
    public function getRawValue();
    public function isRequired();
    public function getValidatorChain();
    public function getValue();

    public function isValid();
    public function getMessages();
}
