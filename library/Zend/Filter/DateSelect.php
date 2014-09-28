<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

class DateSelect extends AbstractFilter
{
    protected $nullOnEmpty = false;
    protected $nullOnAllEmpty = false;

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
            if ($this->nullOnEmpty() && (empty($value['year']) || empty($value['month']) || empty($value['day']))) {
                    return null;
                }

            if ($this->nullOnAllEmpty() && (empty($value['year']) && empty($value['month']) && empty($value['day']))) {
                return null;
            }

            $value = $value['year'] . '-' . $value['month'] . '-' . $value['day'];
        }

        return $value;
    }
}
