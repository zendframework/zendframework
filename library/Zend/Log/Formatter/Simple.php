<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log\Formatter;

use DateTime;
use Zend\Log\Exception;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Formatter
 */
class Simple implements FormatterInterface
{
    const DEFAULT_FORMAT = '%timestamp% %priorityName% (%priority%): %message% %info%';

    /**
     * Format specifier for log messages
     *
     * @var string
     */
    protected $format;

    /**
     * Format specifier for DateTime objects in event data (default: ISO 8601)
     *
     * @see http://php.net/manual/en/function.date.php
     * @var string
     */
    protected $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT;

    /**
     * Class constructor
     *
     * @see http://php.net/manual/en/function.date.php
     * @param null|string $format Format specifier for log messages
     * @param null|string $dateTimeFormat Format specifier for DateTime objects in event data
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($format = null, $dateTimeFormat = null)
    {
        if (isset($format) && !is_string($format)) {
            throw new Exception\InvalidArgumentException('Format must be a string');
        }

        $this->format = isset($format) ? $format : static::DEFAULT_FORMAT;

        if (isset($dateTimeFormat)) {
            $this->dateTimeFormat = $dateTimeFormat;
        }
    }

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param array $event event data
     * @return string formatted line to write to the log
     */
    public function format($event)
    {
        $output = $this->format;

        if (!isset($event['info'])) {
            $event['info'] = '';
        }

        if (isset($event['timestamp']) && $event['timestamp'] instanceof DateTime) {
            $event['timestamp'] = $event['timestamp']->format($this->getDateTimeFormat());
        }

        foreach ($event as $name => $value) {
            if ((is_object($value) && !method_exists($value,'__toString'))
                || is_array($value)
            ) {
                $value = gettype($value);
            }

            $output = str_replace("%$name%", $value, $output);
        }

        return $output;
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * {@inheritDoc}
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = (string) $dateTimeFormat;
        return $this;
    }
}
