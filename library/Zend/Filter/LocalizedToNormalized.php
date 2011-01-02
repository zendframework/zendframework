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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Filter;
use Zend\Locale\Format;

/**
 * Normalizes given localized input
 *
 * @uses       Zend\Filter\AbstractFilter
 * @uses       Zend\Locale\Format
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class LocalizedToNormalized extends AbstractFilter
{
    /**
     * Set options
     * @var array
     */
    protected $_options = array(
        'locale'      => null,
        'date_format' => null,
        'precision'   => null
    );

    /**
     * Class constructor
     *
     * @param string|\Zend\Locale\Locale $locale (Optional) Locale to set
     */
    public function __construct($options = null)
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Returns the set options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets options to use
     *
     * @param  array $options (Optional) Options to use
     * @return \Zend\Filter\LocalizedToNormalized
     */
    public function setOptions(array $options = null)
    {
        $this->_options = $options + $this->_options;
        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Normalizes the given input
     *
     * @param  string $value Value to normalized
     * @return string|array The normalized value
     */
    public function filter($value)
    {
        if (Format::isNumber($value, $this->_options)) {
            return Format::getNumber($value, $this->_options);
        } else if (($this->_options['date_format'] === null) && (strpos($value, ':') !== false)) {
            // Special case, no date format specified, detect time input
            return Format::getTime($value, $this->_options);
        } else if (Format::checkDateFormat($value, $this->_options)) {
            // Detect date or time input
            return Format::getDate($value, $this->_options);
        }

        return $value;
    }
}
