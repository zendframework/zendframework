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
use Zend\Stdlib\StringUtils;
use Zend\Stdlib\StringWrapper\StringWrapperInterface;
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
     * UTF-8 compatable wrapper for string functions
     *
     * @var StringWrapperInterface
     */
    protected $wrapper;

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

        $this->wrapper = StringUtils::getWrapper();

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
     * Returns true if and only if $value is a floating-point value. Uses the formal definition of a float as described
     * in the PHP manual: {@link http://www.php.net/float}
     *
     * @param  string $value
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function isValid($value)
    {
        if (!is_scalar($value) || is_bool($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        if (is_float($value) || is_int($value)) {
            return true;
        }

        $formatter = new NumberFormatter($this->getLocale(), NumberFormatter::DECIMAL);
        if (intl_is_failure($formatter->getErrorCode())) {
            throw new Exception\InvalidArgumentException($formatter->getErrorMessage());
        }

        $groupSeparator = $formatter->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
        $decSeparator   = $formatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

        /**
         * @desc There are seperator "look-alikes" for decimal and group seperators that are more commonly used than the
         *       official unicode chracter. We need to replace those with the real thing - or remove it.
         */
        //NO-BREAK SPACE and ARABIC THOUSANDS SEPARATOR
        if ($groupSeparator == "\xC2\xA0") {
            $value = str_replace(' ', $groupSeparator, $value);
        } elseif ($groupSeparator == "\xD9\xAC") {  //NumberFormatter doesn't have grouping at all for Arabic-Indic
            $value = str_replace(array('\'', $groupSeparator), '', $value);
        }

        //ARABIC DECIMAL SEPARATOR
        if ($decSeparator == "\xD9\xAB") {
            $value = str_replace(',', $decSeparator, $value);
        }

        //We have seperators, and they are flipped. i.e. 2.000,000 for en-US
        $groupSeparatorPosition = $this->wrapper->strpos($value, $groupSeparator);
        $decSeparatorPosition = $this->wrapper->strpos($value, $decSeparator);
        if ($groupSeparatorPosition && $decSeparatorPosition && $groupSeparatorPosition > $decSeparatorPosition) {
            return false;
        }

        //If we have Unicode support, we can use the real graphemes, otherwise, just the ASCII characters
        $decimal     = '[\\' . $decSeparator . ']';
        $posNeg      = '[+-]';
        $exp         = '[Ee]';
        $numberRange = '0-9';
        $useUnicode  = '';

        if (StringUtils::hasPcreUnicodeSupport()) {
            $posNeg = '[' . $formatter->getSymbol(NumberFormatter::PLUS_SIGN_SYMBOL) .
                $formatter->getSymbol(NumberFormatter::MINUS_SIGN_SYMBOL) . ']';
            $exp = '[Ee' . $formatter->getSymbol(NumberFormatter::EXPONENTIAL_SYMBOL) . ']+';
            $numberRange = '\p{N}';
            $useUnicode = 'u';
        }

        /**
         * @desc Match against the formal definition of a float. The exponential number check is modified for RTL
         *       non-Latin number systems (Arabic-Indic numbering). I'm also switching out the period for the decimal
         *       separator. Also, the formal definition leaves out +- from the integer and decimal notations. This also
         *       checks that a grouping sperator is not in the last GROUPING_SIZE graphemes of the string - i.e. 10,6 is
         *       not valid for en-US.
         * @see http://www.php.net/float
         */

        //No strrpos() in the wrappers yet.
        $lastStringGroup = $this->wrapper->substr($value, -($formatter->getAttribute(NumberFormatter::GROUPING_SIZE)));

        $lnum    = '[' . $numberRange . ']+';
        $dnum    = '(([' . $numberRange . ']*' . $decimal . $lnum . ')|(' . $lnum . $decimal . '[' . $numberRange . ']*))';
        $expDnum = '((' . $posNeg . '?((' . $lnum . '|' . $dnum . ')' . $exp . $posNeg . '?' . $lnum . '))|' .
                   '(' . $posNeg . '?(' . $lnum . $posNeg . '?' . $exp . '(' . $dnum . '|' . $lnum . '))' . $posNeg . '?))';

        //If the locale has suffixed indicators, add that to the pattern
        $suffix         = ($formatter->getTextAttribute(NumberFormatter::NEGATIVE_SUFFIX)) ? $posNeg . '?' : '';
        $unGroupedValue = str_replace($groupSeparator, '', $value);

        if ((preg_match('/^' .$posNeg . '?' . $lnum . $suffix . '$/'.$useUnicode, $unGroupedValue) ||
            preg_match('/^' .$posNeg . '?' . $dnum . $suffix . '$/'.$useUnicode, $unGroupedValue) ||
            preg_match('/^' . $expDnum . '$/'.$useUnicode, $unGroupedValue)) &&
            false === $this->wrapper->strpos($lastStringGroup, $groupSeparator)) {

            return true;
        } else {
            return false;
        }
    }
}
