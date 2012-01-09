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
 * @category  Zend
 * @package   Zend_Currency
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Currency;

use Zend\Cache\Storage\Adapter as CacheAdapter,
    Zend\Locale,
    Zend\Locale\Data\Cldr;

/**
 * Class for handling currency notations
 *
 * @category  Zend
 * @package   Zend_Currency
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Currency
{
    // Constants for defining what currency symbol should be displayed
    const NO_SYMBOL     = 1;
    const USE_SYMBOL    = 2;
    const USE_SHORTNAME = 3;
    const USE_NAME      = 4;

    // Constants for defining the position of the currencysign
    const STANDARD = 8;
    const RIGHT    = 16;
    const LEFT     = 32;

    /**
     * Options array
     *
     * The following options are available
     * 'position'  => Position for the currency sign
     * 'script'    => Script for the output
     * 'format'    => Locale for numeric output
     * 'display'   => Currency detail to show
     * 'precision' => Precision for the currency
     * 'name'      => Name for this currency
     * 'currency'  => 3 lettered international abbreviation
     * 'symbol'    => Currency symbol
     * 'locale'    => Locale for this currency
     * 'value'     => Money value
     * 'service'   => Exchange service to use
     *
     * @var array
     * @see Zend_Locale
     */
    protected $_options = array(
        'position'  => self::STANDARD,
        'script'    => null,
        'format'    => null,
        'display'   => self::NO_SYMBOL,
        'precision' => 2,
        'name'      => null,
        'currency'  => null,
        'symbol'    => null,
        'locale'    => null,
        'value'     => 0,
        'service'   => null,
        'tag'       => 'Zend_Locale'
    );

    /**
     * Creates a currency instance. Every supressed parameter is used from the actual or the given locale.
     *
     * @param  string|array       $options OPTIONAL Options array or currency short name
     *                                              when string is given
     * @param  string|Zend\Locale\Locale $locale  OPTIONAL locale name
     * @throws Zend\Currency\Exception   When currency is invalid
     */
    public function __construct($options = null, $locale = null)
    {
        if (is_array($options)) {
            $this->setLocale($locale);
            $this->setFormat($options);
        } else if (Locale\Locale::isLocale($options, false)) {
            $this->setLocale($options);
            $options = $locale;
        } else {
            $this->setLocale($locale);
        }

        // Get currency details
        if (!isset($this->_options['currency']) || !is_array($options)) {
            $this->_options['currency'] = self::getShortName($options, $this->_options['locale']);
        }

        if (!isset($this->_options['name']) || !is_array($options)) {
            $this->_options['name']     = self::getName($options, $this->_options['locale']);
        }

        if (!isset($this->_options['symbol']) || !is_array($options)) {
            $this->_options['symbol']   = self::getSymbol($options, $this->_options['locale']);
        }

        if (($this->_options['currency'] === null) and ($this->_options['name'] === null)) {
            throw new Exception\InvalidArgumentException("Currency '$options' not found");
        }

        // Get the format
        if (!empty($this->_options['symbol'])) {
            $this->_options['display'] = self::USE_SYMBOL;
        } else if (!empty($this->_options['currency'])) {
            $this->_options['display'] = self::USE_SHORTNAME;
        }
    }

    /**
     * Returns a localized currency string
     *
     * @param  integer|float $value   OPTIONAL Currency value
     * @param  array         $options OPTIONAL options to set temporary
     * @throws Zend\Currency\Exception When the value is not a number
     * @return string
     */
    public function toCurrency($value = null, array $options = array())
    {
        if ($value === null) {
            if (is_array($options) && isset($options['value'])) {
                $value = $options['value'];
            } else {
                $value = $this->_options['value'];
            }
        }

        if (is_array($value)) {
            $options += $value;
            if (isset($options['value'])) {
                $value = $options['value'];
            }
        }

        // Validate the passed number
        if (!(isset($value)) or (is_numeric($value) === false)) {
            throw new Exception\InvalidArgumentException("Value '$value' has to be numeric");
        }

        if (isset($options['currency'])) {
            if (!isset($options['locale'])) {
                $options['locale'] = $this->_options['locale'];
            }

            $options['currency'] = self::getShortName($options['currency'], $options['locale']);
            $options['name']     = self::getName($options['currency'], $options['locale']);
            $options['symbol']   = self::getSymbol($options['currency'], $options['locale']);
        }

        $options = $this->_checkOptions($options) + $this->_options;

        // Format the number
        $format = $options['format'];
        $locale = $options['locale'];
        if (empty($format)) {
            $format = Cldr::getContent($locale, 'currencynumber');
        } else if (Locale\Locale::isLocale($format, true)) {
            $locale = $format;
            $format = Cldr::getContent($format, 'currencynumber');
        }

        $original = $value;
        $value    = Locale\Format::toNumber($value, array('locale'        => $locale,
                                                          'number_format' => $format,
                                                          'precision'     => $options['precision']));

        if ($options['position'] !== self::STANDARD) {
            $value = str_replace('¤', '', $value);
            $space = '';
            if (iconv_strpos($value, ' ') !== false) {
                $value = str_replace(' ', '', $value);
                $space = ' ';
            }

            if ($options['position'] == self::LEFT) {
                $value = '¤' . $space . $value;
            } else {
                $value = $value . $space . '¤';
            }
        }

        // Localize the number digits
        if (empty($options['script']) === false) {
            $value = Locale\Format::convertNumerals($value, 'Latn', $options['script']);
        }

        // Get the sign to be placed next to the number
        if (is_numeric($options['display']) === false) {
            $sign = $options['display'];
        } else {
            switch($options['display']) {
                case self::USE_SYMBOL:
                    $sign = $this->_extractPattern($options['symbol'], $original);
                    break;

                case self::USE_SHORTNAME:
                    $sign = $options['currency'];
                    break;

                case self::USE_NAME:
                    $sign = $options['name'];
                    break;

                default:
                    $sign = '';
                    $value = str_replace(' ', '', $value);
                    break;
            }
        }

        $value = str_replace('¤', $sign, $value);
        return $value;
    }

    /**
     * Internal method to extract the currency pattern
     * when a choice is given based on the given value
     *
     * @param  string $pattern
     * @param  float|integer $value
     * @return string
     */
    private function _extractPattern($pattern, $value)
    {
        if (strpos($pattern, '|') === false) {
            return $pattern;
        }

        $patterns = explode('|', $pattern);
        $token    = $pattern;
        $value    = trim(str_replace('¤', '', $value));
        krsort($patterns);
        foreach($patterns as $content) {
            if (strpos($content, '<') !== false) {
                $check = iconv_substr($content, 0, iconv_strpos($content, '<'));
                $token = iconv_substr($content, iconv_strpos($content, '<') + 1);
                if ($check < $value) {
                    return $token;
                }
            } else {
                $check = iconv_substr($content, 0, iconv_strpos($content, '≤'));
                $token = iconv_substr($content, iconv_strpos($content, '≤') + 1);
                if ($check <= $value) {
                    return $token;
                }
            }

        }

        return $token;
    }

    /**
     * Sets the formating options of the localized currency string
     * If no parameter is passed, the standard setting of the
     * actual set locale will be used
     *
     * @param  array $options (Optional) Options to set
     * @return Zend\Currency\Currency
     */
    public function setFormat(array $options = array())
    {
        $this->_options = $this->_checkOptions($options) + $this->_options;
        return $this;
    }

    /**
     * Internal function for checking static given locale parameter
     *
     * @param  string                    $currency (Optional) Currency name
     * @param  string|Zend\Locale\Locale $locale   (Optional) Locale to display informations
     * @throws Zend\Currency\Exception   When locale contains no region
     * @return string The extracted locale representation as string
     */
    private function _checkParams($currency = null, $locale = null)
    {
        // Manage the params
        if ((empty($locale)) and (!empty($currency)) and
            (Locale\Locale::isLocale($currency, true))) {
            $locale   = $currency;
            $currency = null;
        }

        // Validate the locale and get the country short name
        $country = null;
        if ((Locale\Locale::isLocale($locale, true)) and (strlen($locale) > 4)) {
            $country = substr($locale, (strpos($locale, '_') + 1));
        } else {
            throw new Exception\InvalidArgumentException("No region found within the locale '" . (string) $locale . "'");
        }

        // Get the available currencies for this country
        $data = Cldr::getContent($locale, 'currencytoregion', $country);
        if ((empty($currency) === false) and (empty($data) === false)) {
            $abbreviation = $currency;
        } else {
            $abbreviation = $data;
        }

        return array('locale' => $locale, 'currency' => $currency, 'name' => $abbreviation, 'country' => $country);
    }

    /**
     * Returns the actual or details of other currency symbols,
     * when no symbol is available it returns the currency shortname (f.e. FIM for Finnian Mark)
     *
     * @param  string                    $currency (Optional) Currency name
     * @param  string|Zend\Locale\Locale $locale   (Optional) Locale to display informations
     * @return string
     */
    public function getSymbol($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['symbol'];
        }

        $params = self::_checkParams($currency, $locale);

        // Get the symbol
        $symbol = Cldr::getContent($params['locale'], 'currencysymbol', $params['currency']);
        if (empty($symbol) === true) {
            $symbol = Cldr::getContent($params['locale'], 'currencysymbol', $params['name']);
        }

        if (empty($symbol) === true) {
            return null;
        }

        return $symbol;
    }

    /**
     * Returns the actual or details of other currency shortnames
     *
     * @param  string                    $currency OPTIONAL Currency's name
     * @param  string|Zend\Locale\Locale $locale   OPTIONAL The locale
     * @return string
     */
    public function getShortName($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['currency'];
        }

        $params = self::_checkParams($currency, $locale);

        // Get the shortname
        if (empty($params['currency']) === true) {
            return $params['name'];
        }

        $list = Cldr::getContent($params['locale'], 'currencytoname', $params['currency']);
        if (empty($list) === true) {
            $list = Cldr::getContent($params['locale'], 'nametocurrency', $params['currency']);
            if (empty($list) === false) {
                $list = $params['currency'];
            }
        }

        if (empty($list) === true) {
            return null;
        }

        return $list;
    }

    /**
     * Returns the actual or details of other currency names
     *
     * @param  string                    $currency (Optional) Currency's short name
     * @param  string|Zend\Locale\Locale $locale   (Optional) The locale
     * @return string
     */
    public function getName($currency = null, $locale = null)
    {
        if (($currency === null) and ($locale === null)) {
            return $this->_options['name'];
        }

        $params = self::_checkParams($currency, $locale);

        // Get the name
        $name = Cldr::getContent($params['locale'], 'nametocurrency', $params['currency']);
        if (empty($name) === true) {
            $name = Cldr::getContent($params['locale'], 'nametocurrency', $params['name']);
        }

        if (empty($name) === true) {
            return null;
        }

        return $name;
    }

    /**
     * Returns a list of regions where this currency is or was known
     *
     * @param  string $currency OPTIONAL Currency's short name
     * @throws Zend\Currency\Exception When no currency was defined
     * @return array List of regions
     */
    public function getRegionList($currency = null)
    {
        if ($currency === null) {
            $currency = $this->_options['currency'];
        }

        if (empty($currency) === true) {
            throw new Exception\InvalidArgumentException('No currency defined');
        }

        $data = Cldr::getContent($this->_options['locale'], 'regiontocurrency', $currency);

        $result = explode(' ', $data);
        return $result;
    }

    /**
     * Returns a list of currencies which are used in this region
     * a region name should be 2 charachters only (f.e. EG, DE, US)
     * If no region is given, the actual region is used
     *
     * @param  string $region OPTIONAL Region to return the currencies for
     * @return array List of currencies
     */
    public function getCurrencyList($region = null)
    {
        if (empty($region) === true) {
            if (strlen($this->_options['locale']) > 4) {
                $region = substr($this->_options['locale'], (strpos($this->_options['locale'], '_') + 1));
            }
        }

        $data = Cldr::getContent($this->_options['locale'], 'currencytoregion', $region);

        $result = explode(' ', $data);
        return $result;
    }

    /**
     * Returns the actual currency name
     *
     * @return string
     */
    public function toString()
    {
        return $this->toCurrency();
    }

    /**
     * Returns the currency name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Returns the set cache
     *
     * @return CacheAdapter The set cache
     */
    public static function getCache()
    {
        return Cldr::getCache();
    }

    /**
     * Sets a cache for Zend_Currency
     *
     * @param  CacheAdapter $cache Cache to set
     * @return void
     */
    public static function setCache(CacheAdapter $cache)
    {
        Cldr::setCache($cache);
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        return Cldr::hasCache();
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        Cldr::removeCache();
    }

    /**
     * Clears all set cache data
     *
     * @return void
     */
    public static function clearCache($tag = null)
    {
        Cldr::clearCache($tag);
    }

    /**
     * Sets a new locale for data retreivement
     * Example: 'de_XX' will be set to 'de' because 'de_XX' does not exist
     * 'xx_YY' will be set to 'root' because 'xx' does not exist
     *
     * @param  string|Zend\Locale\Locale $locale (Optional) Locale for parsing input
     * @throws Zend\Currency\Exception When the given locale does not exist
     * @return Zend\Currency Provides fluent interface
     */
    public function setLocale($locale = null)
    {
        try {
            $locale = Locale\Locale::findLocale($locale);
            if (strlen($locale) > 4) {
                $this->_options['locale'] = $locale;
            } else {
                throw new Exception\InvalidArgumentException("No region found within the locale '" . (string) $locale . "'");
            }
        } catch (Locale\Exception $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        }

        // Get currency details
        $this->_options['currency'] = $this->getShortName(null, $this->_options['locale']);
        $this->_options['name']     = $this->getName(null, $this->_options['locale']);
        $this->_options['symbol']   = $this->getSymbol(null, $this->_options['locale']);

        return $this;
    }

    /**
     * Returns the actual set locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_options['locale'];
    }

    /**
     * Returns the value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->_options['value'];
    }

    /**
     * Adds a currency
     *
     * @param float|integer|Zend\Currency\Currency $value    Add this value to currency
     * @param string|Zend\Currency\Currency        $currency The currency to add
     * @return Zend\Currency\Currency
     */
    public function setValue($value, $currency = null)
    {
        $this->_options['value'] = $this->_exchangeCurrency($value, $currency);
        return $this;
    }

    /**
     * Adds a currency
     *
     * @param float|integer|Zend\Currency\Currency $value    Add this value to currency
     * @param string|Zend\Currency\Currency        $currency The currency to add
     * @return Zend\Currency\Currency
     */
    public function add($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        $this->_options['value'] += (float) $value;
        return $this;
    }

    /**
     * Substracts a currency
     *
     * @param float|integer|Zend\Currency\Currency $value    Substracts this value from currency
     * @param string|Zend\Currency\Currency        $currency The currency to substract
     * @return Zend\Currency\Currency
     */
    public function sub($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        $this->_options['value'] -= (float) $value;
        return $this;
    }

    /**
     * Divides a currency
     *
     * @param float|integer|Zend\Currency\Currency $value    Divides this value from currency
     * @param string|Zend\Currency\Currency        $currency The currency to divide
     * @return Zend\Currency\Currency
     */
    public function div($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        $this->_options['value'] /= (float) $value;
        return $this;
    }

    /**
     * Multiplies a currency
     *
     * @param float|integer|Zend\Currency\Currency $value    Multiplies this value from currency
     * @param string|Zend\Currency\Currency        $currency The currency to multiply
     * @return Zend\Currency\Currency
     */
    public function mul($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        $this->_options['value'] *= (float) $value;
        return $this;
    }

    /**
     * Calculates the modulo from a currency
     *
     * @param float|integer|Zend\Currency\Currency $value    Calculate modulo from this value
     * @param string|Zend\Currency\Currency        $currency The currency to calculate the modulo
     * @return Zend\Currency\Currency
     */
    public function mod($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        $this->_options['value'] %= (float) $value;
        return $this;
    }

    /**
     * Compares two currencies
     *
     * @param float|integer|Zend\Currency\Currency $value    Compares the currency with this value
     * @param string|Zend\Currency\Currency        $currency The currency to compare this value from
     * @return Zend\Currency\Currency
     */
    public function compare($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        $value = $this->_options['value'] - $value;
        if ($value < 0) {
            return -1;
        } else if ($value > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * Returns true when the two currencies are equal
     *
     * @param float|integer|Zend\Currency\Currency $value    Compares the currency with this value
     * @param string|Zend\Currency\Currency        $currency The currency to compare this value from
     * @return boolean
     */
    public function equals($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        if ($this->_options['value'] == $value) {
            return true;
        }

        return false;
    }

    /**
     * Returns true when the currency is more than the given value
     *
     * @param float|integer|Zend\Currency\Currency $value    Compares the currency with this value
     * @param string|Zend\Currency\Currency        $currency The currency to compare this value from
     * @return boolean
     */
    public function isMore($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        if ($this->_options['value'] > $value) {
            return true;
        }

        return false;
    }

    /**
     * Returns true when the currency is less than the given value
     *
     * @param float|integer|Zend\Currency\Currency $value    Compares the currency with this value
     * @param string|Zend\Currency\Currency        $currency The currency to compare this value from
     * @return boolean
     */
    public function isLess($value, $currency = null)
    {
        $value = $this->_exchangeCurrency($value, $currency);
        if ($this->_options['value'] < $value) {
            return true;
        }

        return false;

    }

    /**
     * Internal method which calculates the exchanges currency
     *
     * @param float|integer|Zend\Currency\Currency $value    Compares the currency with this value
     * @param string|Zend\Currency\Currency        $currency The currency to compare this value from
     * @return float
     */
    protected function _exchangeCurrency($value, $currency)
    {
        if ($value instanceof Currency) {
            $currency = $value->getShortName();
            $value    = $value->getValue();
        } else {
            $currency = $this->getShortName($currency, $this->getLocale());
        }

        $rate = 1;
        if ($currency !== $this->getShortName()) {
            $service = $this->getService();
            if (!($service instanceof CurrencyService)) {
                throw new Exception\RuntimeException('No exchange service applied');
            }

            $rate = $service->getRate($currency, $this->getShortName());
        }

        $value *= $rate;
        return $value;
    }

    /**
     * Returns the set service class
     *
     * @return Zend\Currency\CurrencyService
     */
    public function getService()
    {
        return $this->_options['service'];
    }

    /**
     * Sets a new exchange service
     *
     * @param string|Zend\Currency\CurrencyService $service Service class
     * @return Zend\Currency\Currency
     */
    public function setService($service)
    {
        if (is_string($service)) {
            if (!class_exists($service)) {
                throw new Exception\InvalidArgumentException('A currency service by class name "' . $service . '" does not exist');
            }
            $service = new $service;
        }

        if (!($service instanceof CurrencyService)) {
            throw new Exception\InvalidArgumentException('A currency service must implement Zend\Currency\CurrencyService');
        }

        $this->_options['service'] = $service;
        return $this;
    }

    /**
     * Internal method for checking the options array
     *
     * @param  array $options Options to check
     * @throws Zend\Currency\Exception On unknown position
     * @throws Zend\Currency\Exception On unknown locale
     * @throws Zend\Currency\Exception On unknown display
     * @throws Zend\Currency\Exception On precision not between -1 and 30
     * @throws Zend\Currency\Exception On problem with script conversion
     * @throws Zend\Currency\Exception On unknown options
     * @return array
     */
    protected function _checkOptions(array $options = array())
    {
        if (count($options) === 0) {
            return $this->_options;
        }

        foreach ($options as $name => $value) {
            $name = strtolower($name);
            if ($name !== 'format') {
                if (gettype($value) === 'string') {
                    $value = strtolower($value);
                }
            }

            switch($name) {
                case 'position':
                    if (($value !== self::STANDARD) and ($value !== self::RIGHT) and ($value !== self::LEFT)) {
                        throw new Exception\InvalidArgumentException("Unknown position '" . $value . "'");
                    }

                    break;

                case 'format':
                    if ((empty($value) === false) and (Locale\Locale::isLocale($value) === false)) {
                        if (!is_string($value) || (strpos($value, '0') === false)) {
                            throw new Exception\InvalidArgumentException('\''
                                              . ((gettype($value) === 'object') ? get_class($value) : $value)
                                              . '\' is no format token');
                        }
                    }
                    break;

                case 'display':
                    if (is_numeric($value) and ($value !== self::NO_SYMBOL) and ($value !== self::USE_SYMBOL) and
                        ($value !== self::USE_SHORTNAME) and ($value !== self::USE_NAME)) {
                        throw new Exception\InvalidArgumentException("Unknown display '$value'");
                    }
                    break;

                case 'precision':
                    if ($value === null) {
                        $value = -1;
                    }

                    if (($value < -1) or ($value > 30)) {
                        throw new Exception\InvalidArgumentException("'$value' precision has to be between -1 and 30.");
                    }
                    break;

                case 'script':
                    try {
                        Locale\Format::convertNumerals(0, $options['script']);
                    } catch (Locale\Exception $e) {
                        throw new Exception\InvalidArgumentException($e->getMessage());
                    }
                    break;

                default:
                    break;
            }
        }

        return $options;
    }
}
