<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\I18n\Validator;

use Locale;
use IntlDateFormatter;
use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

class DateTime extends AbstractValidator
{
    const INVALID           = 'datetimeInvalid';
    const INVALID_DATETIME  = 'datetimeInvalidDateTime';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID           => "Invalid type given. String expected",
        self::INVALID_DATETIME  => "The input does not appear to be a valid datetime",
    );

    /**
     * Optional locale
     *
     * @var string|null
     */
    protected $locale;

    /**
     * @var int
     */
    protected $dateFormat;

    /**
     * @var int
     */
    protected $timeFormat;

    /**
     * @var int
     */
    protected $timezone;

    /**
     * @var $pattern
     */
    protected $pattern;

    /**
     * @var int
     */
    protected $calender;

    /**
     * DateFormatter instance
     *
     * @var IntlDateFormatter
     */
    protected $formatter;

    /**
     * The formatter invalidated
     *
     * because properties where changed
     *
     * @var bool
     */
    protected $invalidateFormatter = true;

    /**
     * Constructor for the Date validator
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        parent::__construct($options);
    }

    /**
     * @param $calendar
     *
     * @return Date provides fluent interface
     */
    public function setCalender($calender)
    {
        $this->calender = $calender;
        return $this;
    }

    public function getCalender()
    {
        if (null === $this->calender) {
            $this->calender = IntlDateFormatter::GREGORIAN;
        }

        return (!$this->invalidateFormatter) ? $this->getIntlDateFormatter()->getCalender() : $this->calender;
    }

    /**
     * @param int $dateFormat
     *
     * @return Date provides fluent interface
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
        $this->invalidateFormatter = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getDateFormat()
    {
        if (null === $this->dateFormat) {
            $this->dateFormat = IntlDateFormatter::NONE;
        }
        return $this->dateFormat;
    }

    /**
     * @param $pattern
     *
     * @return Date provides fluent interface
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function getPattern()
    {
        return (!$this->invalidateFormatter) ? $this->getIntlDateFormatter()->getPattern() : $this->pattern;
    }

    /**
     * @param int $timeFormat
     *
     * @return Date provides fluent interface
     */
    public function setTimeFormat($timeFormat)
    {
        $this->timeFormat = $timeFormat;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeFormat()
    {
        if (null === $this->timeFormat) {
            $this->timeFormat = IntlDateFormatter::NONE;
        }
        $this->invalidateFormatter = true;

        return $this->timeFormat;
    }

    /**
     * Sets the timezone to use
     *
     * @param string|null $timezone
     * @return Date provides fluent interface
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Returns the set timezone or the system default if none given
     *
     * @return string
     */
    public function getTimezone()
    {
        if (null === $this->timezone) {
            $this->timezone = date_default_timezone_get();
        }
        return (!$this->invalidateFormatter) ? $this->getIntlDateFormatter()->getTimeZoneId() : $this->timezone;
    }

    /**
     * Returns the set locale or the system default if none given
     *
     * @return string
     */
    public function getLocale()
    {
        if (null === $this->locale) {
            $this->locale = Locale::getDefault();
        }
        return $this->locale;
    }

    /**
     * Sets the locale to use
     *
     * @param string|null $locale
     * @return Date provides fluent interface
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->invalidateFormatter = true;

        return $this;
    }


    /**
     * Returns true if and only if $value is a floating-point value
     *
     * @param  string $value
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        $formatter = $this->getIntlDateFormatter();

        if (intl_is_failure($formatter->getErrorCode())) {
            throw new Exception\InvalidArgumentException("Invalid locale string given");
        }

        $position = 0;
        $parsedDate = $formatter->parse($value, $position);

        if (intl_is_failure($formatter->getErrorCode())) {
            $this->error(self::INVALID_DATETIME);
            return false;
        }

        if ($position != strlen($value)) {
            $this->error(self::INVALID_DATETIME);
            return false;
        }

        return true;
    }

    /**
     * Returns a non lenient configured DateFormatter
     *
     * @return \IntlDateFormatter
     */
    protected function getIntlDateFormatter()
    {
        if ($this->formatter == null || $this->invalidateFormatter) {
            $this->formatter = new \IntlDateFormatter($this->getLocale(), $this->getDateFormat(), $this->getTimeFormat(),
                $this->getTimezone(), $this->getCalender(), $this->getPattern());

            // non lenient behavior
            $this->formatter->setLenient(false);

            $this->invalidateFormatter = false;
        }

        return $this->formatter;
    }
}
