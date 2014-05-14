<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use DateInterval;
use DateTime;
use DateTimeZone;
use Traversable;
use Zend\Stdlib\ArrayUtils;

class DateStep extends Date
{
    const NOT_STEP       = 'dateStepNotStep';

    const FORMAT_DEFAULT = DateTime::ISO8601;

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID      => "Invalid type given. String, integer, array or DateTime expected",
        self::INVALID_DATE => "The input does not appear to be a valid date",
        self::FALSEFORMAT  => "The input does not fit the date format '%format%'",
        self::NOT_STEP     => "The input is not a valid step",
    );

    /**
     * Optional base date value
     *
     * @var string|int|\DateTime
     */
    protected $baseValue = '1970-01-01T00:00:00Z';

    /**
     * Date step interval (defaults to 1 day).
     * Uses the DateInterval specification.
     *
     * @var DateInterval
     */
    protected $step;

    /**
     * Optional timezone to be used when the baseValue
     * and validation values do not contain timezone info
     *
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * Set default options for this instance
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp['baseValue'] = array_shift($options);
            if (!empty($options)) {
                $temp['step'] = array_shift($options);
            }
            if (!empty($options)) {
                $temp['format'] = array_shift($options);
            }
            if (!empty($options)) {
                $temp['timezone'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!isset($options['step'])) {
            $options['step'] = new DateInterval('P1D');
        }
        if (!isset($options['timezone'])) {
            $options['timezone'] = new DateTimeZone(date_default_timezone_get());
        }

        parent::__construct($options);
    }

    /**
     * Sets the base value from which the step should be computed
     *
     * @param  string|int|\DateTime $baseValue
     * @return DateStep
     */
    public function setBaseValue($baseValue)
    {
        $this->baseValue = $baseValue;
        return $this;
    }

    /**
     * Returns the base value from which the step should be computed
     *
     * @return string|int|\DateTime
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    /**
     * Sets the step date interval
     *
     * @param  DateInterval $step
     * @return DateStep
     */
    public function setStep(DateInterval $step)
    {
        $this->step = $step;
        return $this;
    }

    /**
     * Returns the step date interval
     *
     * @return DateInterval
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Returns the timezone option
     *
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Sets the timezone option
     *
     * @param  DateTimeZone $timezone
     * @return DateStep
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Supports formats with ISO week (W) definitions
     *
     * @see Date::convertString()
     */
    protected function convertString($value, $addErrors = true)
    {
        // Custom week format support
        if (strpos($this->format, 'Y-\WW') === 0
            && preg_match('/^([0-9]{4})\-W([0-9]{2})/', $value, $matches)
        ) {
            $date = new DateTime;
            $date->setISODate($matches[1], $matches[2]);
        } else {
            $date = DateTime::createFromFormat($this->format, $value, $this->timezone);
        }

        // Invalid dates can show up as warnings (ie. "2007-02-99")
        // and still return a DateTime object.
        $errors = DateTime::getLastErrors();
        if ($errors['warning_count'] > 0) {
            if ($addErrors) {
                $this->error(self::FALSEFORMAT);
            }
            return false;
        }

        return $date;
    }

    /**
     * Returns true if a date is within a valid step
     *
     * @param  string|int|\DateTime $value
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function isValid($value)
    {
        if (!parent::isValid($value)) {
            return false;
        }

        $valueDate = $this->convertToDateTime($value, false); // avoid duplicate errors
        $baseDate  = $this->convertToDateTime($this->baseValue, false);
        $step      = $this->getStep();

        // Same date?
        if ($valueDate == $baseDate) {
            return true;
        }

        // Optimization for simple intervals.
        // Handle intervals of just one date or time unit.
        $intervalParts = explode('|', $step->format('%y|%m|%d|%h|%i|%s'));
        $partCounts    = array_count_values($intervalParts);
        if (5 === $partCounts["0"]) {
            // Find the unit with the non-zero interval
            $unitKeys = array('years', 'months', 'days', 'hours', 'minutes', 'seconds');
            $intervalParts = array_combine($unitKeys, $intervalParts);

            $intervalUnit = null;
            $stepValue    = null;
            foreach ($intervalParts as $key => $value) {
                if (0 != $value) {
                    $intervalUnit = $key;
                    $stepValue    = (int) $value;
                    break;
                }
            }

            // Get absolute time difference
            $timeDiff  = $valueDate->diff($baseDate, true);
            $diffParts = explode('|', $timeDiff->format('%y|%m|%d|%h|%i|%s'));
            $diffParts = array_combine($unitKeys, $diffParts);

            // Check date units
            if (in_array($intervalUnit, array('years', 'months', 'days'))) {
                switch ($intervalUnit) {
                    case 'years':
                        if (   0 == $diffParts['months']  && 0 == $diffParts['days']
                            && 0 == $diffParts['hours']   && 0 == $diffParts['minutes']
                            && 0 == $diffParts['seconds']
                        ) {
                            if (($diffParts['years'] % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                    case 'months':
                        if (   0 == $diffParts['days']    && 0 == $diffParts['hours']
                            && 0 == $diffParts['minutes'] && 0 == $diffParts['seconds']
                        ) {
                            $months = ($diffParts['years'] * 12) + $diffParts['months'];
                            if (($months % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                    case 'days':
                        if (   0 == $diffParts['hours'] && 0 == $diffParts['minutes']
                            && 0 == $diffParts['seconds']
                        ) {
                            $days = $timeDiff->format('%a'); // Total days
                            if (($days % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                }
                $this->error(self::NOT_STEP);
                return false;
            }

            // Check time units
            if (in_array($intervalUnit, array('hours', 'minutes', 'seconds'))) {

                // Simple test if $stepValue is 1.
                if (1 == $stepValue) {
                    if ('hours' === $intervalUnit
                        && 0 == $diffParts['minutes'] && 0 == $diffParts['seconds']
                    ) {
                        return true;
                    } elseif ('minutes' === $intervalUnit && 0 == $diffParts['seconds']) {
                        return true;
                    } elseif ('seconds' === $intervalUnit) {
                        return true;
                    }
                }

                // Simple test for same day, when using default baseDate
                if ($baseDate->format('Y-m-d') == $valueDate->format('Y-m-d')
                    && $baseDate->format('Y-m-d') == '1970-01-01'
                ) {
                    switch ($intervalUnit) {
                        case 'hours':
                            if (0 == $diffParts['minutes'] && 0 == $diffParts['seconds']) {
                                if (($diffParts['hours'] % $stepValue) === 0) {
                                    return true;
                                }
                            }
                            break;
                        case 'minutes':
                            if (0 == $diffParts['seconds']) {
                                $minutes = ($diffParts['hours'] * 60) + $diffParts['minutes'];
                                if (($minutes % $stepValue) === 0) {
                                    return true;
                                }
                            }
                            break;
                        case 'seconds':
                            $seconds = ($diffParts['hours'] * 60)
                                       + ($diffParts['minutes'] * 60)
                                       + $diffParts['seconds'];
                            if (($seconds % $stepValue) === 0) {
                                return true;
                            }
                            break;
                    }
                    $this->error(self::NOT_STEP);
                    return false;
                }
            }
        }

        // Fall back to slower (but accurate) method for complex intervals.
        // Keep adding steps to the base date until a match is found
        // or until the value is exceeded.
        if ($baseDate < $valueDate) {
            while ($baseDate < $valueDate) {
                $baseDate->add($step);
                if ($baseDate == $valueDate) {
                    return true;
                }
            }
        } else {
            while ($baseDate > $valueDate) {
                $baseDate->sub($step);
                if ($baseDate == $valueDate) {
                    return true;
                }
            }
        }

        $this->error(self::NOT_STEP);
        return false;
    }
}
