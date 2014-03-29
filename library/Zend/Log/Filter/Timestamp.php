<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Filter;

use Zend\Log\Exception;
use DateTime;

/**
 * Filters log events based on the time when they were triggered.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class Timestamp implements FilterInterface
{
    /**
     * DateTime instance or desired value based on $dateFormatChar.
     *
     * @var int|DateTime
     */
    protected $value;

    /**
     * PHP idate()-compliant format character.
     *
     * @var string|null
     */
    protected $dateFormatChar = null;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @param int|DateTime|array|Traversable $value DateTime instance or desired value based on $dateFormatChar
     * @param string $dateFormatChar PHP idate()-compliant format character
     * @param string $operator Comparison operator
     * @return Timestamp
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($value, $dateFormatChar = null, $operator = null)
    {
        if ($value instanceof Traversable) {
            $value = iterator_to_array($value);
        }
        if (is_array($value)) {
            extract($value, EXTR_IF_EXISTS);
        }

        if ($value instanceof DateTime) {
            $this->value = $value;
        } else {
            if (!is_int($value)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Value must be either DateTime instance or integer; received "%s"',
                    gettype($value)
                ));
            } elseif (!is_string($dateFormatChar)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Date format character must be supplied as string; received "%s"',
                    gettype($dateFormatChar)
                ));
            }
            $this->value = $value;
            $this->dateFormatChar = $dateFormatChar;
        }

        $this->operator = ($operator) ?: '<=';
    }

    /**
     * Returns TRUE if timestamp is accepted, otherwise FALSE is returned.
     *
     * @param array $event event data
     * @return bool
     */
    public function filter(array $event)
    {
        $datetime = $event['timestamp'];
        $timestamp = $datetime->getTimestamp();

        if ($this->value instanceof DateTime) {
            return version_compare((string) $timestamp, (string) $this->value->getTimestamp(), $this->operator);
        }

        return version_compare(
            idate($this->dateFormatChar, $timestamp),
            $this->value,
            $this->operator
        );
    }
}
