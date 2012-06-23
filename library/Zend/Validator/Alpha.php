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

namespace Zend\Validator;

use Zend\Filter\Alpha as AlphaFilter;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @var AlphaFilter
     */
    protected static $filter = null;

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
     * @param  boolean|\Traversable $allowWhiteSpace
     * @return void
     */
    public function __construct($allowWhiteSpace = false)
    {
        parent::__construct(is_array($allowWhiteSpace) ? $allowWhiteSpace : null);

        if (is_scalar($allowWhiteSpace)) {
            $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
        }
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
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        if ('' === $value) {
            $this->error(self::STRING_EMPTY);
            return false;
        }

        if (null === self::$filter) {
            self::$filter = new AlphaFilter();
        }

        self::$filter->setAllowWhiteSpace($this->allowWhiteSpace);

        if ($value !== self::$filter->filter($value)) {
            $this->error(self::NOT_ALPHA);
            return false;
        }

        return true;
    }

}
