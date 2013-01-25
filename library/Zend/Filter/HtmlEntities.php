<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class HtmlEntities extends AbstractFilter
{
    /**
     * Corresponds to the second htmlentities() argument
     *
     * @var integer
     */
    protected $quoteStyle;

    /**
     * Corresponds to the third htmlentities() argument
     *
     * @var string
     */
    protected $encoding;

    /**
     * Corresponds to the forth htmlentities() argument
     *
     * @var bool
     */
    protected $doubleQuote;

    /**
     * Sets filter options
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['quotestyle'] = array_shift($options);
            if (!empty($options)) {
                $temp['charset'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!isset($options['quotestyle'])) {
            $options['quotestyle'] = ENT_QUOTES;
        }

        if (!isset($options['encoding'])) {
            $options['encoding'] = 'UTF-8';
        }
        if (isset($options['charset'])) {
            $options['encoding'] = $options['charset'];
        }

        if (!isset($options['doublequote'])) {
            $options['doublequote'] = true;
        }

        $this->setQuoteStyle($options['quotestyle']);
        $this->setEncoding($options['encoding']);
        $this->setDoubleQuote($options['doublequote']);
    }

    /**
     * Returns the quoteStyle option
     *
     * @return integer
     */
    public function getQuoteStyle()
    {
        return $this->quoteStyle;
    }

    /**
     * Sets the quoteStyle option
     *
     * @param  integer $quoteStyle
     * @return HtmlEntities Provides a fluent interface
     */
    public function setQuoteStyle($quoteStyle)
    {
        $this->quoteStyle = $quoteStyle;
        return $this;
    }


    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
         return $this->encoding;
    }

    /**
     * Set encoding
     *
     * @param  string $value
     * @return HtmlEntities
     */
    public function setEncoding($value)
    {
        $this->encoding = (string) $value;
        return $this;
    }

    /**
     * Returns the charSet option
     *
     * Proxies to {@link getEncoding()}
     *
     * @return string
     */
    public function getCharSet()
    {
        return $this->getEncoding();
    }

    /**
     * Sets the charSet option
     *
     * Proxies to {@link setEncoding()}
     *
     * @param  string $charSet
     * @return HtmlEntities Provides a fluent interface
     */
    public function setCharSet($charSet)
    {
        return $this->setEncoding($charSet);
    }

    /**
     * Returns the doubleQuote option
     *
     * @return bool
     */
    public function getDoubleQuote()
    {
        return $this->doubleQuote;
    }

    /**
     * Sets the doubleQuote option
     *
     * @param  bool $doubleQuote
     * @return HtmlEntities Provides a fluent interface
     */
    public function setDoubleQuote($doubleQuote)
    {
        $this->doubleQuote = (bool) $doubleQuote;
        return $this;
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value, converting characters to their corresponding HTML entity
     * equivalents where they exist
     *
     * @param  string $value
     * @throws Exception\DomainException
     * @return string
     */
    public function filter($value)
    {
        $filtered = htmlentities((string) $value, $this->getQuoteStyle(), $this->getEncoding(), $this->getDoubleQuote());
        if (strlen((string) $value) && !strlen($filtered)) {
            if (!function_exists('iconv')) {
                throw new Exception\DomainException('Encoding mismatch has resulted in htmlentities errors');
            }
            $enc      = $this->getEncoding();
            $value    = iconv('', $this->getEncoding() . '//IGNORE', (string) $value);
            $filtered = htmlentities($value, $this->getQuoteStyle(), $enc, $this->getDoubleQuote());
            if (!strlen($filtered)) {
                throw new Exception\DomainException('Encoding mismatch has resulted in htmlentities errors');
            }
        }
        return $filtered;
    }
}
