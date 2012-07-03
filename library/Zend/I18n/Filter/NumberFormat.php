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
 * @package    Zend_I18n
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\Filter;

use Zend\Filter\AbstractLocale;
use Zend\I18n\Exception;
use NumberFormatter;
use Traversable;

class NumberFormat extends AbstractLocale
{
    protected $options = array(
        'locale' => null,
        'style'  => NumberFormatter::DEFAULT_STYLE,
        'type'   => NumberFormatter::TYPE_DOUBLE
    );

    /**
     * @var NumberFormatter
     */
    protected $formatter = null;

    public function __construct($options = null)
    {
        if ($options !== null) {
            if ($options instanceof Traversable) {
                $options = iterator_to_array($options);
            }

            if (!is_array($options)) {
                $args = func_get_args();
                if (isset($args[0])) {
                    $this->setLocale($args[0]);
                }
                if (isset($args[1])) {
                    $this->setStyle($args[1]);
                }
                if (isset($args[2])) {
                    $this->setType($args[2]);
                }
            } else {
                $this->setOptions($options);
            }
        }
    }

    /**
     * @param  null $locale
     * @return NumberFormat
     */
    public function setLocale($locale = null)
    {
        $this->options['locale'] = $locale;
        $this->formatter = null;
        return $this;
    }

    /**
     * @param  int $style
     * @return NumberFormat
     */
    public function setStyle($style)
    {
        $this->options['style'] = (int) $style;
        $this->formatter = null;
        return $this;
    }

    /**
     * @return int
     */
    public function getStyle()
    {
        return $this->options['style'];
    }

    /**
     * @param  int $type
     * @return NumberFormat
     */
    public function setType($type)
    {
        $this->options['type'] = (int) $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->options['type'];
    }

    /**
     * @param  NumberFormatter $formatter
     * @return NumberFormat
     */
    public function setFormatter(NumberFormatter $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @return NumberFormatter
     * @throws Exception\RuntimeException
     */
    public function getFormatter()
    {
        if ($this->formatter === null) {
            $formatter = NumberFormatter::create($this->getLocale(), $this->getStyle());
            if (!$formatter) {
                throw new Exception\RuntimeException(
                    'Can not create NumberFormatter instance; ' . intl_get_error_message()
                );
            }

            $this->formatter = $formatter;
        }

        return $this->formatter;
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * @see    Zend\Filter\FilterInterface::filter()
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        $formatter = $this->getFormatter();
        $type      = $this->getType();

        if (is_int($value) || is_float($value)) {
            $result = @numfmt_format($formatter, $value, $type);
        } else {
            $value = str_replace(array("\xC2\xA0", ' '), '', $value);
            $result = @numfmt_parse($formatter, $value, $type);
        }

        if ($result === false) {
            return $value;
        }

        return str_replace("\xC2\xA0", ' ', $result);
    }
}
