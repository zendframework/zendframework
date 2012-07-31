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
class ErrorHandler extends Simple
{
    const DEFAULT_FORMAT = '%timestamp% %priorityName% (%priority%) %message% (errno %extra[errno]%) in %extra[file]% on line %extra[line]%';

    /**
     * This method formats the event for the PHP Error Handler.
     *
     * @param  array $event
     * @return string
     */
    public function format($event)
    {
        $output = $this->format;

        if (isset($event['timestamp']) && $event['timestamp'] instanceof DateTime) {
            $event['timestamp'] = $event['timestamp']->format($this->getDateTimeFormat());
        }

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
