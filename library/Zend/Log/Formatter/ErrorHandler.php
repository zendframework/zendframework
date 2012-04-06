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
 * @package    Zend_Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Formatter;
use Zend\Log\Exception;

/**
 * @uses       Zend\Log\Formatter\AbstractFormatter
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ErrorHandler extends AbstractFormatter
{
    const DEFAULT_FORMAT = '%timestamp% %priorityName% (%priority%) %message% (errno %extra[errno]%) in %extra[file]% on line %extra[line]%';
    
    /**
     * Format
     * 
     * @var string 
     */
    protected $format;
    
    /**
     * Class constructor
     *
     * @param  null|string  $format  Format specifier for log messages
     * @return void
     * @throws Zend\Log\Exception\InvalidArgumentException
     */
    public function __construct($format = null)
    {
        if ($format === null) {
            $format = self::DEFAULT_FORMAT;
        }

        if (!is_string($format)) {
            throw new Exception\InvalidArgumentException('Format must be a string');
        }

        $this->format = $format;
    }

    /**
     * Factory for Zend\Log\Formatter\ErrorHandler
     *
     * @param  array|\Zend\Config\Config $options useless
     * @return ErrorHandler
     */
    public static function factory($options = array())
    {
        $format = null;
        if (null !== $options) {
            if ($options instanceof Config) {
                $options = $options->toArray();
            }

            if (array_key_exists('format', $options)) {
                $format = $options['format'];
            }
        }
        
        return new self($format);
    }

    /**
     * This method formats the event for the PHP Error Handler.
     *
     * @param  array $event
     * @return string
     */
    public function format($event)
    {
        $output = $this->format;
        foreach ($event as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $sname => $svalue) {
                    $output = str_replace("%{$name}[{$sname}]%", $svalue, $output);
                }
            } else {
                $output = str_replace("%$name%", $value, $output);
            }
        }
        return $output;
    }
}