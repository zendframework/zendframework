<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\I18n\Validator;

use Locale;
use NumberFormatter;
use Traversable;
use Zend\I18n\Exception as I18nException;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

class Float extends AbstractValidator
{
    const INVALID   = 'floatInvalid';
    const NOT_FLOAT = 'notFloat';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Invalid type given. String, integer or float expected",
        self::NOT_FLOAT => "The input does not appear to be a float",
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
     * @throws Exception\ExtensionNotLoadedException if ext/intl is not present
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('intl')) {
            throw new I18nException\ExtensionNotLoadedException(
                sprintf('%s component requires the intl PHP extension', __NAMESPACE__)
            );
        }

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
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        if (is_float($value)) {
            return true;
        }

        $locale = $this->getLocale();
        $numberFormatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);

        if (intl_is_failure($numberFormatter->getErrorCode())) {
            throw new Exception\InvalidArgumentException($numberFormatter->getErrorMessage());
        }

        $parsedFloat = $numberFormatter->parse($value);
var_dump(__LINE__,$parsedFloat, $value);
        //Check if the parser returned a number or not
        if (intl_is_failure($numberFormatter->getErrorCode()) || false === $parsedFloat) {
            $this->error(self::NOT_FLOAT);
            return false;
        }

        if (intl_is_failure($numberFormatter->getErrorCode())) {
            throw new Exception\InvalidArgumentException($numberFormatter->getErrorMessage());
        }
var_dump(__LINE__,$numberFormatter->format($parsedFloat), $value);
        // This is a valid float if we can do a parse/format roundtrip on the data
        if ($numberFormatter->format($parsedFloat) == $value || strval($parsedFloat) == strval($value)) {
            return true;
        }

        $this->error(self::NOT_FLOAT);
        return false;
    }
}
