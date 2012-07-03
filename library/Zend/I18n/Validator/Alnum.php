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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\Validator;

use Zend\Validator\AbstractValidator;
use Zend\I18n\Filter\Alnum as AlnumFilter;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Alnum extends AbstractValidator
{
    const INVALID      = 'alnumInvalid';
    const NOT_ALNUM    = 'notAlnum';
    const STRING_EMPTY = 'alnumStringEmpty';

    /**
     * Alphanumeric filter used for validation
     *
     * @var AlnumFilter
     */
    protected static $filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID      => "Invalid type given. String, integer or float expected",
        self::NOT_ALNUM    => "The input contains characters which are non alphabetic and no digits",
        self::STRING_EMPTY => "The input is an empty string",
    );

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'allowWhiteSpace' => false,  // Whether to allow white space characters; off by default
    );

    /**
     * Sets default option values for this instance
     *
     * @param array|\Traversable $options
     */
    public function __construct($allowWhiteSpace = false)
    {
        $options = is_array($allowWhiteSpace) ? $allowWhiteSpace : null;
        parent::__construct($options);

        if (is_scalar($allowWhiteSpace)) {
            $this->options['allowWhiteSpace'] = (boolean) $allowWhiteSpace;
        }
    }

    /**
     * Returns the allowWhiteSpace option
     *
     * @return boolean
     */
    public function getAllowWhiteSpace()
    {
        return $this->options['allowWhiteSpace'];
    }

    /**
     * Sets the allowWhiteSpace option
     *
     * @param boolean $allowWhiteSpace
     * @return AlnumFilter Provides a fluent interface
     */
    public function setAllowWhiteSpace($allowWhiteSpace)
    {
        $this->options['allowWhiteSpace'] = (boolean) $allowWhiteSpace;
        return $this;
    }

    /**
     * Returns true if and only if $value contains only alphabetic and digit characters
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        if ('' === $value) {
            $this->error(self::STRING_EMPTY);
            return false;
        }

        if (null === self::$filter) {
            self::$filter = new AlnumFilter();
        }

        self::$filter->setAllowWhiteSpace($this->options['allowWhiteSpace']);

        if ($value != self::$filter->filter($value)) {
            $this->error(self::NOT_ALNUM);
            return false;
        }

        return true;
    }
}
