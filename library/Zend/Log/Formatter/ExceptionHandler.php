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
class ExceptionHandler implements FormatterInterface
{
    /**
     * This method formats the event for the PHP Exception
     *
     * @param array $event
     * @return string
     */
    public function format($event)
    {
        $output = $event['timestamp'] . ' ' . $event['priorityName'] . ' ('
                . $event['priority'] . ') ' . $event['message'] .' in '
                . $event['extra']['file'] . ' on line ' . $event['extra']['line'];

        if (!empty($event['extra']['trace'])) {
            $outputTrace = '';
            foreach ($event['extra']['trace'] as $trace) {
                $outputTrace .= "File  : {$trace['file']}\n"
                              . "Line  : {$trace['line']}\n"
                              . "Func  : {$trace['function']}\n"
                              . "Class : {$trace['class']}\n"
                              . "Type  : " . $this->getType($trace['type']) . "\n"
                              . "Args  : " . print_r($trace['args'], true) . "\n";
            }
            $output .= "\n[Trace]\n" . $outputTrace;
        }

        return $output;
    }

    /**
     * Get the type of a function
     *
     * @param string $type
     * @return string
     */
    protected function getType($type)
    {
        switch ($type) {
            case "::" :
                return "static";
            case "->" :
                return "method";
            default :
                return $type;
        }
    }
}
