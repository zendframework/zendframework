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

/**
 * @namespace
 */
namespace Zend\Validator;
use Zend;
use Zend\Locale;

/**
 * @uses       \Zend\Locale\Locale
 * @uses       \Zend\Locale\Format
 * @uses       \Zend\Registry
 * @uses       \Zend\Validator\AbstractValidator
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Float extends AbstractValidator
{
    const INVALID   = 'floatInvalid';
    const NOT_FLOAT = 'notFloat';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Invalid type given. String, integer or float expected",
        self::NOT_FLOAT => "'%value%' does not appear to be a float",
    );

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'locale' => null,
    );

    /**
     * Constructor
     *
     * @param array $options
     * @return \Zend\Validator\Float
     */
    public function __construct($options = null)
    {
        if (!is_array($options)) {
            $options = array('locale' => $options);
        }

        parent::__construct($options);
    }

    /**
     * Returns the set locale
     *
     * @return \Zend\Locale
     */
    public function getLocale()
    {
        return $this->options['locale'];
    }

    /**
     * Sets the locale to use
     *
     * @param string|\Zend\Locale\Locale $locale
     */
    public function setLocale($locale = null)
    {
        $this->options['locale'] = Locale\Locale::findLocale($locale);
        return $this;
    }

    /**
     * Returns true if and only if $value is a floating-point value
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        if (is_float($value)) {
            return true;
        }

        $this->setValue($value);
        try {
            if (!Locale\Format::isFloat($value, array('locale' => $this->options['locale']))) {
                $this->error(self::NOT_FLOAT);
                return false;
            }
        } catch (Locale\Exception $e) {
            $this->error(self::NOT_FLOAT);
            return false;
        }

        return true;
    }
}
