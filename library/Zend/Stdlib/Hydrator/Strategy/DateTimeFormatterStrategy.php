<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Strategy;

use DateTime;
use DateTimeZone;

class DateTimeFormatterStrategy implements StrategyInterface
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var DateTimeZone|null
     */
    protected $timezone;

    /**
     * Constructor
     *
     * @param string            $format
     * @param DateTimeZone|null $timezone
     */
    public function __construct($format = DateTime::RFC3339, DateTimeZone $timezone = null)
    {
        $this->format   = $format;
        $this->timezone = $timezone;
    }

    /**
     * Sets timezone
     *
     * @param  DateTimeZone $timezone
     * @return void
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Converts to date time string
     *
     * @param DateTime|string|null
     * @return string|null
     */
    public function extract($value)
    {
        if ($value instanceof DateTime) {
            return $value->format($this->format);
        }

        return $value;
    }

    /**
     * Converts date time string to DateTime instance for injecting to object
     *
     * @param  string|null $value
     * @return DateTime|null
     */
    public function hydrate($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        if ($this->timezone instanceof DateTimeZone) {
            return DateTime::createFromFormat($this->format, $value, $this->timezone);
        }

        return DateTime::createFromFormat($this->format, $value);
    }
}
