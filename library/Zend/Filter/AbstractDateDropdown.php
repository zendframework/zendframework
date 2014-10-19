<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

/**
 * Class AbstractDateDropdown
 * @package Zend\Filter
 */
abstract class AbstractDateDropdown extends AbstractFilter
{
    /**
     * If true, the filter will return null if any date field is empty
     *
     * @var bool
     */
    protected $nullOnEmpty = false;

    /**
     * If true, the filter will return null if all date fields are empty
     *
     * @var bool
     */
    protected $nullOnAllEmpty = false;

    /**
     * Sprintf format string to use for formatting the date, fields will be used in alphabetical order.
     *
     * @var string
     */
    protected $format = '';

    /**
     * @param boolean $nullOnAllEmpty
     * @return $this
     */
    public function setNullOnAllEmpty($nullOnAllEmpty)
    {
        $this->nullOnAllEmpty = $nullOnAllEmpty;
        return $this;
    }

    /**
     * @return boolean
     */
    public function nullOnAllEmpty()
    {
        return $this->nullOnAllEmpty;
    }

    /**
     * @param boolean $nullOnEmpty
     * @return $this
     */
    public function setNullOnEmpty($nullOnEmpty)
    {
        $this->nullOnEmpty = $nullOnEmpty;
        return $this;
    }

    /**
     * @return boolean
     */
    public function nullOnEmpty()
    {
        return $this->nullOnEmpty;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        // Convert the date to a specific format
        if (is_array($value)) {

            if (
                $this->nullOnEmpty() &&
                array_reduce($value, function ($soFar, $value) { return $soFar | empty($value); }, false)
            ) {
                return null;
            }

            if (
                $this->nullOnAllEmpty() &&
                array_reduce($value, function ($soFar, $value) { return $soFar & empty($value); }, true)
            ) {
                return null;
            }

            ksort($value);
            $value = vsprintf($this->format, $value);
        }

        return $value;
    }
}
