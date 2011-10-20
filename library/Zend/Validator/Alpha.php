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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator;

/**
 * @uses       \Zend\Filter\Alpha
 * @uses       \Zend\Validator\AbstractValidator
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Alpha extends AbstractValidator
{
    const INVALID      = 'alphaInvalid';
    const NOT_ALPHA    = 'notAlpha';
    const STRING_EMPTY = 'alphaStringEmpty';

    /**
     * Whether to allow white space characters; off by default
     *
     * @var boolean
     */
    protected $allowWhiteSpace;

    /**
     * Alphabetic filter used for validation
     *
     * @var \Zend\Filter\Alpha
     */
    protected static $_filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID      => "Invalid type given. String expected",
        self::NOT_ALPHA    => "'%value%' contains non alphabetic characters",
        self::STRING_EMPTY => "'%value%' is an empty string"
    );

    /**
     * Sets default option values for this instance
     *
     * @param  boolean|\Zend\Config\Config $allowWhiteSpace
     * @return void
     */
    public function __construct($allowWhiteSpace = false)
    {
        if ($allowWhiteSpace instanceof \Zend\Config\Config) {
            $allowWhiteSpace = $allowWhiteSpace->toArray();
        }

        if (is_array($allowWhiteSpace)) {
            if (array_key_exists('allowWhiteSpace', $allowWhiteSpace)) {
                $allowWhiteSpace = $allowWhiteSpace['allowWhiteSpace'];
            } else {
                $allowWhiteSpace = false;
            }
        }

        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
    }

    /**
     * Returns the allowWhiteSpace option
     *
     * @return boolean
     */
    public function getAllowWhiteSpace()
    {
        return $this->allowWhiteSpace;
    }

    /**
     * Sets the allowWhiteSpace option
     *
     * @param boolean $allowWhiteSpace
     * @return \Zend\Filter\Alpha Provides a fluent interface
     */
    public function setAllowWhiteSpace($allowWhiteSpace)
    {
        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
        return $this;
    }

    /**
     * Returns true if and only if $value contains only alphabetic characters
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);

        if ('' === $value) {
            $this->_error(self::STRING_EMPTY);
            return false;
        }

        if (null === self::$_filter) {
            self::$_filter = new \Zend\Filter\Alpha();
        }

        self::$_filter->setAllowWhiteSpace($this->allowWhiteSpace);

        if ($value !== self::$_filter->filter($value)) {
            $this->_error(self::NOT_ALPHA);
            return false;
        }

        return true;
    }

}
