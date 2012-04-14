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

use Traversable,
    Zend\Date as ZendDate,
    Zend\Locale\Format,
    Zend\Locale\Locale,
    Zend\Registry,
    Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Date extends AbstractValidator
{
    const INVALID        = 'dateInvalid';
    const INVALID_DATE   = 'dateInvalidDate';
    const FALSEFORMAT    = 'dateFalseFormat';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID        => "Invalid type given. String, integer, array or Zend_Date expected",
        self::INVALID_DATE   => "'%value%' does not appear to be a valid date",
        self::FALSEFORMAT    => "'%value%' does not fit the date format '%format%'",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'format'  => 'format'
    );

    /**
     * Optional format
     *
     * @var string|null
     */
    protected $format;

    /**
     * Optional locale
     *
     * @var string|\Zend\Locale\Locale|null
     */
    protected $locale;

    /**
     * Sets validator options
     *
     * @param  string|array|Traversable $options OPTIONAL
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp['format'] = array_shift($options);
            if (!empty($options)) {
                $temp['locale'] = array_shift($options);
            }

            $options = $temp;
        }

        if (array_key_exists('format', $options)) {
            $this->setFormat($options['format']);
        }

        if (!array_key_exists('locale', $options)) {
            if (Registry::isRegistered('Zend_Locale')) {
                $options['locale'] = Registry::get('Zend_Locale');
            }
        }

        if (array_key_exists('locale', $options)) {
            $this->setLocale($options['locale']);
        }
        
        parent::__construct($options);
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
     * @return Date provides a fluent interface
     */
    public function setLocale($locale = null)
    {
        $this->locale = Locale::findLocale($locale);
        return $this;
    }

    /**
     * Returns the locale option
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
     * @return \Zend\Validator\Date provides a fluent interface
     */
    public function setFormat($format = null)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Returns true if $value is a valid date of the format YYYY-MM-DD
     * If optional $format or $locale is set the date format is checked
     * according to Zend_Date, see Zend_Date::isDate()
     *
     * @param  string|array|ZendDate $value
     * @return boolean
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

        if (($this->format !== null) 
            || ($this->locale !== null)
            || is_array($value) 
            || $value instanceof ZendDate\Date
        ) {
            if (!ZendDate\Date::isDate($value, $this->format, $this->locale)) {
                if ($this->checkFormat($value) === false) {
                    $this->error(self::FALSEFORMAT);
                } else {
                    $this->error(self::INVALID_DATE);
                }
                return false;
            }
        } else {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                $this->format = 'yyyy-MM-dd';
                $this->error(self::FALSEFORMAT);
                $this->format = null;
                return false;
            }

            list($year, $month, $day) = sscanf($value, '%d-%d-%d');

            if (!checkdate($month, $day, $year)) {
                $this->error(self::INVALID_DATE);
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the given date fits the given format
     *
     * @param  string $value  Date to check
     * @return boolean False when date does not fit the format
     */
    private function checkFormat($value)
    {
        try {
            $parsed = Format::getDate((string) $value, array(
                'date_format' => $this->format,
                'format_type' => 'iso',
                'fix_date'    => false,
            ));
            if (isset($parsed['year']) 
                && ((strpos(strtoupper($this->format), 'YY') !== false) 
                    && (strpos(strtoupper($this->format), 'YYYY') === false))
            ) {
                $parsed['year'] = ZendDate\Date::getFullYear($parsed['year']);
            }
        } catch (\Exception $e) {
            // Date can not be parsed
            return false;
        }

        if (((strpos($this->format, 'Y') !== false) || (strpos($this->format, 'y') !== false)) 
            && (!isset($parsed['year']))
        ) {
            // Year expected but not found
            return false;
        }

        if ((strpos($this->format, 'M') !== false) && (!isset($parsed['month']))) {
            // Month expected but not found
            return false;
        }

        if ((strpos($this->format, 'd') !== false) && (!isset($parsed['day']))) {
            // Day expected but not found
            return false;
        }

        if (((strpos($this->format, 'H') !== false) || (strpos($this->format, 'h') !== false)) 
            && (!isset($parsed['hour']))
        ) {
            // Hour expected but not found
            return false;
        }

        if ((strpos($this->format, 'm') !== false) && (!isset($parsed['minute']))) {
            // Minute expected but not found
            return false;
        }

        if ((strpos($this->format, 's') !== false) && (!isset($parsed['second']))) {
            // Second expected  but not found
            return false;
        }

        // Date fits the format
        return true;
    }
}
