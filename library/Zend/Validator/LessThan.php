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

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class LessThan extends AbstractValidator
{
    const NOT_LESS           = 'notLessThan';
    const NOT_LESS_INCLUSIVE = 'notLessThanInclusive';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_LESS           => "'%value%' is not less than '%max%'",
        self::NOT_LESS_INCLUSIVE => "'%value%' is not less or equal than '%max%'"
    );

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = array(
        'max' => '_max'
    );

    /**
     * Maximum value
     *
     * @var mixed
     */
    protected $_max;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to max
     *
     * If false, then strict comparisons are done, and the value may equal
     * the max option
     *
     * @var boolean
     */
    protected $_inclusive;

    /**
     * Sets validator options
     *
     * @param  array|Traversable $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['max'] = array_shift($options);

            if (!empty($options)) {
                $temp['inclusive'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('max', $options)) {
            throw new Exception\InvalidArgumentException("Missing option 'max'");
        }

        if (!array_key_exists('inclusive', $options)) {
            $options['inclusive'] = false;
        }

        $this->setMax($options['max'])
             ->setInclusive($options['inclusive']);
             
        parent::__construct();
    }

    /**
     * Returns the max option
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  mixed $max
     * @return \Zend\Validator\LessThan Provides a fluent interface
     */
    public function setMax($max)
    {
        $this->_max = $max;
        return $this;
    }

    /**
     * Returns the inclusive option
     *
     * @return boolean
     */
    public function getInclusive()
    {
        return $this->_inclusive;
    }

    /**
     * Sets the inclusive option
     *
     * @param  boolean $inclusive
     * @return \Zend\Validator\LessThan Provides a fluent interface
     */
    public function setInclusive($inclusive)
    {
        $this->_inclusive = $inclusive;
        return $this;
    }

    /**
     * Returns true if and only if $value is less than max option, inclusively
     * when the inclusive option is true
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->setValue($value);

        if ($this->_inclusive) {
            if ($value > $this->_max) {
                $this->error(self::NOT_LESS_INCLUSIVE);
                return false;
            }
        } else {
            if ($value >= $this->_max) {
                $this->error(self::NOT_LESS);
                return false;
            }
        }

        return true;
    }
}
