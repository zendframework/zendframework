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
use Zend;
use Zend\Locale;

/**
 * @see        Zend_Locale
 * @see        Zend_Locale_Format
 * @see        Zend_Registry
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PostCode extends AbstractValidator
{
    const INVALID        = 'postcodeInvalid';
    const NO_MATCH       = 'postcodeNoMatch';
    const SERVICE        = 'postcodeService';
    const SERVICEFAILURE = 'postcodeServiceFailure';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID        => "Invalid type given. String or integer expected",
        self::NO_MATCH       => "'%value%' does not appear to be a postal code",
        self::SERVICE        => "'%value%' does not appear to be a postal code",
        self::SERVICEFAILURE => "An exception has been raised while validating '%value%'",
    );

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'service'=> null, // Service callback for additional validation
        'format' => null, // Manual postal code format
        'locale' => null, // Locale to use
    );

    /**
     * Constructor for the integer validator
     *
     * Accepts either a string locale, a Zend_Locale object, or an array or
     * Zend_Config object containing the keys "locale" and/or "format".
     *
     * @param string|\Zend\Locale\Locale|array|\Traversable $options
     * @throws \Zend\Validator\Exception On empty format
     */
    public function __construct($options = null)
    {
        if (empty($options)) {
            if (\Zend\Registry::isRegistered('Zend_Locale')) {
                $this->setLocale(\Zend\Registry::get('Zend_Locale'));
            }
        } elseif ($options instanceof Locale\Locale || is_string($options)) {
            // Received Locale object or string locale
            $this->setLocale($options);
        }

        parent::__construct($options);
        $format = $this->getFormat();
        if (empty($format)) {
            throw new Exception\InvalidArgumentException("A postcode-format string has to be given for validation");
        }
    }

    /**
     * Returns the set locale
     *
     * @return string|\Zend\Locale\Locale The set locale
     */
    public function getLocale()
    {
        return $this->options['locale'];
    }

    /**
     * Sets the locale to use
     *
     * @param string|\Zend\Locale\Locale $locale
     * @throws \Zend\Validator\Exception On unrecognised region
     * @throws \Zend\Validator\Exception On not detected format
     * @return \Zend\Validator\PostCode  Provides fluid interface
     */
    public function setLocale($locale = null)
    {
        $this->options['locale'] = Locale\Locale::findLocale($locale);
        $locale                  = new Locale\Locale($this->getLocale());
        $region                  = $locale->getRegion();
        if (empty($region)) {
            throw new Exception\InvalidArgumentException("Unable to detect a region for the locale '$locale'");
        }

        $format = Locale\Locale::getTranslation(
            $locale->getRegion(),
            'postaltoterritory',
            $this->getLocale()
        );

        if (empty($format)) {
            throw new Exception\InvalidArgumentException("Unable to detect a postcode format for the region '{$locale->getRegion()}'");
        }

        $this->setFormat($format);
        return $this;
    }

    /**
     * Returns the set postal code format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->options['format'];
    }

    /**
     * Sets a self defined postal format as regex
     *
     * @param string $format
     * @throws \Zend\Validator\Exception On empty format
     * @return \Zend\Validator\PostCode  Provides fluid interface
     */
    public function setFormat($format)
    {
        if (empty($format) || !is_string($format)) {
            throw new Exception\InvalidArgumentException("A postcode-format string has to be given for validation");
        }

        if ($format[0] !== '/') {
            $format = '/^' . $format;
        }

        if ($format[strlen($format) - 1] !== '/') {
            $format .= '$/';
        }

        $this->options['format'] = $format;
        return $this;
    }

    /**
     * Returns the actual set service
     *
     * @return callback
     */
    public function getService()
    {
        return $this->options['service'];
    }

    /**
     * Sets a new callback for service validation
     *
     * @param string|array $service
     */
    public function setService($service)
    {
        if (!is_callable($service)) {
            throw new Exception\InvalidArgumentException('Invalid callback given');
        }

        $this->options['service'] = $service;
        return $this;
    }

    /**
     * Returns true if and only if $value is a valid postalcode
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->setValue($value);
        if (!is_string($value) && !is_int($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $service = $this->getService();
        if (!empty($service)) {
            try {
                $callback = new Callback($service);
                $callback->setOptions(array(
                    'format' => $this->options['format'],
                    'locale' => $this->options['locale'],
                ));
                if (!$callback->isValid($value)) {
                    $this->error(self::SERVICE, $value);
                    return false;
                }
            } catch (\Exception $e) {
                $this->error(self::SERVICEFAILURE, $value);
                return false;
            }
        }

        $format = $this->getFormat();
        if (!preg_match($format, $value)) {
            $this->error(self::NO_MATCH);
            return false;
        }

        return true;
    }
}
