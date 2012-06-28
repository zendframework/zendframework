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
use Zend\Locale;
use Zend\Registry;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Int extends AbstractValidator
{
    const INVALID = 'intInvalid';
    const NOT_INT = 'notInt';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given. String or integer expected",
        self::NOT_INT => "'%value%' does not appear to be an integer",
    );

    protected $locale;

    /**
     * Constructor for the integer validator
     *
     * @param  array|Traversable|\Zend\Locale\Locale|string $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (is_array($options)) {
            if (array_key_exists('locale', $options)) {
                $options = $options['locale'];
            } else {
                $options = null;
            }
        }

        if ($options !== null) {
            $this->setLocale($options);
        }

        parent::__construct();
    }

    /**
     * Returns the set locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the locale to use
     *
     * @param string|\Zend\Locale\Locale $locale
     * @return Int
     */
    public function setLocale($locale = null)
    {
        $this->locale = Locale\Locale::findLocale($locale);
        return $this;
    }

    /**
     * Returns true if and only if $value is a valid integer
     *
     * @param  string|integer $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        if (is_int($value)) {
            return true;
        }

        $this->setValue($value);
        if ($this->locale === null) {
            $locale        = localeconv();
            $valueFiltered = str_replace($locale['decimal_point'], '.', $value);
            $valueFiltered = str_replace($locale['thousands_sep'], '', $valueFiltered);

            if (strval(intval($valueFiltered)) != $valueFiltered) {
                $this->error(self::NOT_INT);
                return false;
            }

        } else {
            try {
                if (!Locale\Format::isInteger($value, array('locale' => $this->locale))) {
                    $this->error(self::NOT_INT);
                    return false;
                }
            } catch (Locale\Exception\ExceptionInterface $e) {
                $this->error(self::NOT_INT);
                return false;
            }
        }

        return true;
    }
}
