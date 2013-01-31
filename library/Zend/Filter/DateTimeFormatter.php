<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use DateTime;
use Exception;
use Zend\Filter\AbstractFilter;

class DateTimeFormatter extends AbstractFilter
{
    /**
     * A valid format string accepted by date()
     *
     * @var string
     */
    protected $format = DateTime::ISO8601;

    /**
     * Sets filter options
     *
     * @param  string|array|\Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set the format string accepted by date() to use when normalizing a string
     *
     * @param  string $format
     * @return \Zend\Filter\DateTimeNormalize
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Filter a datetime string by normalizing it to the filters specified format
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        try {
            $result = $this->normalizeDateTime($value);
        } catch (Exception $ex) {
            // DateTime threw an exception, an invalid date string was provided
            return $value;
        }

        return $result;
    }

    /**
     * Attempt to convert a string into a valid DateTime object
     *
     * @param string $value
     * @returns DateTime
     * @throws Exception
     */
    protected function normalizeDateTime($value)
    {
        if (is_int($value)) {
            $dateTime = new DateTime('@' . $value);
        } else {
            $dateTime = new DateTime($value);
        }

        return $dateTime->format($this->format);
    }
}
