<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\I18n\View\Helper;

use DateTime;
use IntlDateFormatter;
use Locale;
use Zend\I18n\Exception;
use Zend\View\Helper\AbstractHelper;

/**
 * View helper for formatting dates.
 */
class DateFormat extends AbstractHelper
{
    /**
     * Locale to use instead of the default.
     *
     * @var string
     */
    protected $locale;

    /**
     * Timezone to use.
     *
     * @var string
     */
    protected $timezone;

    /**
     * Formatter instances.
     *
     * @var array
     */
    protected $formatters = array();

    /**
     * Set timezone to use instead of the default.
     *
     * @param string $timezone
     * @return DateFormat
     */
    public function setTimezone($timezone)
    {
        $this->timezone = (string) $timezone;

        foreach ($this->formatters as $formatter) {
            $formatter->setTimeZoneId($this->timezone);
        }
        return $this;
    }

    /**
     * Get the timezone to use.
     *
     * @return string|null
     */
    public function getTimezone()
    {
        if (!$this->timezone) {
            return date_default_timezone_get();
        }

        return $this->timezone;
    }

    /**
     * Set locale to use instead of the default.
     *
     * @param  string $locale
     * @return DateFormat
     */
    public function setlocale($locale)
    {
        $this->locale = (string) $locale;
        return $this;
    }

    /**
     * Get the locale to use.
     *
     * @return string|null
     */
    public function getlocale()
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Format a date.
     *
     * @param  DateTime|integer|array  $date
     * @param  int                     $dateType
     * @param  int                     $timeType
     * @param  string                  $locale
     * @param  string|null             $pattern
     * @return string
     */
    public function __invoke(
        $date,
        $dateType = IntlDateFormatter::NONE,
        $timeType = IntlDateFormatter::NONE,
        $locale   = null,
        $pattern  = null
    ) {
        if ($locale === null) {
            $locale = $this->getlocale();
        }

        $timezone    = $this->getTimezone();
        $formatterId = md5($dateType . "\0" . $timeType . "\0" . $locale ."\0" . $pattern);

        if (!isset($this->formatters[$formatterId])) {
            $this->formatters[$formatterId] = new IntlDateFormatter(
                $locale,
                $dateType,
                $timeType,
                $timezone,
                IntlDateFormatter::GREGORIAN,
                $pattern
            );
        }

        // DateTime support for IntlDateFormatter::format() was only added in 5.3.4
        if ($date instanceof DateTime && version_compare(PHP_VERSION, '5.3.4', '<')) {
            $date = $date->getTimestamp();
        }

        return $this->formatters[$formatterId]->format($date);
    }
}
