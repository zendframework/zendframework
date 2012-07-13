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

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Formatter
 */
class FirePhp implements FormatterInterface
{
    /**
     * Formats the given evevnt data into a single line to be written by the writer.
     *
     * @param array $event The event data which should be formatted.
     * @return string
     */
    public function format($event)
    {
        return $event['message'];
    }
}
