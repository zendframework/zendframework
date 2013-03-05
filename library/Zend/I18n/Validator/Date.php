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

class Date extends AbstractValidator
{
    const INVALID        = 'dateInvalid';
    const INVALID_DATE   = 'dateInvalidDate';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID        => "Invalid type given. String expected",
        self::INVALID_DATE   => "The input does not appear to be a valid date",
    );

    /**
     * Optional locale
     *
     * @var string|null
     */
    protected $locale;

    /**
     * Constructor for the integer validator
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (array_key_exists('locale', $options)) {
            $this->setLocale($options['locale']);
        }

        parent::__construct($options);
    }

    /**
     * Returns the set locale
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
     * @return Float
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
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

        if (is_float($value)) {
            return true;
        }

        $format = new IntlDateFormatter( $this->getLocale(), IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
        if (intl_is_failure($format->getErrorCode())) {
            throw new Exception\InvalidArgumentException("Invalid locale string given");
        }

        $position = 0;
        $parsedDate = $format->parse($value, $position);
        if (intl_is_failure($format->getErrorCode())) {
            $this->error(self::INVALID_DATE);
            return false;
        }

        if ($position != strlen($value)) {
            $this->error(self::INVALID_DATE);
            return false;
        }

        return true;
    }
}
