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
 * @package    Zend_I18n
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\View\Helper;

use DateTime;
use IntlDateFormatter;
use Locale;
use Zend\View\Helper\AbstractHelper;
use Zend\I18n\Exception;

/**
 * View helper for formatting dates.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DateFormat extends AbstractHelper
{
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
     */
    public function setTimezone($timezone)
    {
        $this->timezone = (string) $timezone;

        foreach ($this->formatters as $formatter) {
            $formatter->setTimeZoneId($this->timezone);
        }
    }

    /**
     * Get a new timezone.
     *
     * @return string|null
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Add a formatter.
     *
     * @param  string            $name
     * @param  IntlDateFormatter $formatter
     * @return DateFormat
     */
    public function addFormatter($name, IntlDateFormatter $formatter)
    {
        $this->formatters[$name] = $formatter;
        return $this;
    }

    /**
     * Format a date.
     *
     * @param  DateTime|integer|array $date
     * @param  integer                $dateType
     * @param  integer                $timeType
     * @param  string                 $locale
     * @return string
     * @throws Exception\RuntimeException
     */
    public function __invoke(
        $date,
        $dateType = IntlDateFormatter::NONE,
        $timeType = IntlDateFormatter::NONE,
        $locale   = null
    ) {
        if ($locale === null) {
            $locale = Locale::getDefault();
        }

        $timezone    = $this->getTimezone();
        $formatterId = md5($dateType . "\0" . $timeType . "\0" . $locale);

        if (!isset($this->formatters[$formatterId])) {
            $this->formatters[$formatterId] = new IntlDateFormatter(
                $locale,
                $dateType,
                $timeType,
                $timezone
            );
        }

        // DateTime support for IntlDateFormatter::format() was only added in 5.3.4
        if ($date instanceof DateTime && version_compare(PHP_VERSION, '5.3.4', '<')) {
            $date = $date->getTimestamp();
        }

        return $this->formatters[$formatterId]->format($date);
    }
}
