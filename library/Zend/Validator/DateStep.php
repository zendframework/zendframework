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
use Zend\Date as ZendDate;
use Zend\Locale\Format;
use Zend\Locale\Locale;
use Zend\Registry;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Exception;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DateStep extends AbstractValidator
{
    const INVALID      = 'dateStepInvalid';
    const INVALID_DATE = 'dateStepInvalidDate';
    const NOT_STEP     = 'dateStepNotStep';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID      => "Invalid type given. String, integer, array or Zend_Date expected",
        self::INVALID_DATE => "'%value%' does not appear to be a valid date",
        self::NOT_STEP     => "'%value%' is not a valid step."
    );

    /**
     * Optional base date value
     *
     * @var string|integer|array|\Zend\Date\Date
     */
    protected $baseValue = 0;

    /**
     * Optional date step value (defaults to 1)
     *
     * @var string|integer|array|\Zend\Date\Date
     */
    protected $stepValue = 1;

    /**
     * Optional date step value (defaults to \Zend\Date\Date::DAY)
     *
     * @var string
     */
    protected $stepDatePart = ZendDate\Date::DAY;

    /**
     * Optional format to be used when the baseValue
     * and validation value are strings to be converted
     * to \Zend\Date\Date objects.
     *
     * @var string|null
     */
    protected $format;

    /**
     * Optional locale to be used when the baseValue
     * and validation value are strings to be converted
     * to \Zend\Date\Date objects.
     *
     * @var string|\Zend\Locale\Locale|null
     */
    protected $locale;

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

            $options = $temp;
        }

        if (isset($options['baseValue'])) {
            $this->setBaseValue($options['baseValue']);
        }
        if (isset($options['stepValue'])) {
            $this->setStepValue($options['stepValue']);
        }
        if (isset($options['stepDatePart'])) {
            $this->setStepDatePart($options['stepDatePart']);
        }
        if (array_key_exists('format', $options)) {
            $this->setFormat($options['format']);
        }
        if (!isset($options['locale'])) {
            if (Registry::isRegistered('Zend_Locale')) {
                $options['locale'] = Registry::get('Zend_Locale');
            }
        }
        if (isset($options['locale'])) {
            $this->setLocale($options['locale']);
        }

        parent::__construct($options);
    }

    /**
     * Sets the base value from which the step should be computed
     *
     * @param  string|integer|array|\Zend\Date\Date $baseValue
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
     * @return string|integer|array|\Zend\Date\Date
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    /**
     * Sets the step value
     *
     * @param  string|integer|array|\Zend\Date\Date $step
     * @return DateStep
     */
    public function setStepValue($stepValue)
    {
        $this->stepValue = $stepValue;
        return $this;
    }

    /**
     * Returns the step value
     *
     * @return string|integer|array|\Zend\Date\Date
     */
    public function getStepValue()
    {
        return $this->stepValue;
    }

    /**
     * Sets the step date part
     *
     * @param  string $stepDatePart
     * @return DateStep
     */
    public function setStepDatePart($stepDatePart)
    {
        $this->stepDatePart = $stepDatePart;
        return $this;
    }

    /**
     * Returns the step date part
     *
     * @return string
     */
    public function getStepDatePart()
    {
        return $this->stepValue;
    }

    /**
     * Returns the format option
     *
     * @return string|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Sets the format option
     *
     * @param  string $format
     * @return DateStep
     */
    public function setFormat($format = null)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string|Locale|null
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the locale option
     *
     * @param  string|Locale $locale
     * @return DateStep
     */
    public function setLocale($locale = null)
    {
        $this->locale = Locale::findLocale($locale);
        return $this;
    }

    /**
     * Returns true if $value is a scalar and a valid step value
     *
     * @param mixed $value
     * @return bool
     * @throws Exception\InvalidArgumentException|\Zend\Date\Exception
     */
    public function isValid($value)
    {
        if (!is_string($value)
            && !is_int($value)
            && !is_float($value)
            && !is_array($value)
            && !($value instanceof ZendDate\Date)
        ) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        $format       = $this->getFormat();
        $locale       = $this->getLocale();
        $baseValue    = $this->getBaseValue();
        $stepValue    = $this->getStepValue();
        $stepDatePart = $this->getStepDatePart();

        // Confirm that baseValue and value are dates
        if (!ZendDate\Date::isDate($baseValue, $format, $locale)) {
            throw new Exception\InvalidArgumentException('Invalid baseValue given');
        }

        if (!ZendDate\Date::isDate($value, $format, $locale)) {
            $this->error(self::INVALID_DATE);
            return false;
        }

        // Convert baseValue and value to Date objects
        $baseDate  = new ZendDate\Date($baseValue, $format, $locale);
        $valueDate = new ZendDate\Date($value, $format, $locale);

        if ($valueDate->equals($baseDate)) {
            return true;
        }

        // Keep adding steps to the base date until a match is found
        // or until the value is exceeded
        while ($baseDate->isEarlier($valueDate)) {
            $baseDate->add($stepValue, $stepDatePart, $locale);
            if ($baseDate->equals($valueDate)) {
                return true;
            }
        }

        return false;
    }
}
