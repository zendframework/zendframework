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

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ExceptionHandler implements FormatterInterface
{
    /**
     * This method formats the event for the PHP Exception
     *
     * @param  array $event
     * @return string
     */
    public function format($event)
    {
        $output = $event['timestamp'] . ' ' . $event['priorityName'] . ' (' .
                  $event['priority'] . ') ' . $event['message'] .' in ' .
                  $event['extra']['file'] . ' on line ' . $event['extra']['line'];
        if (!empty($event['extra']['trace'])) {
            foreach ($event['extra']['trace'] as $trace) {
                $outputTrace = "File  : {$trace['file']}\n" .
                               "Line  : {$trace['line']}\n" .
                               "Func  : {$trace['function']}\n" .
                               "Class : {$trace['class']}\n" .
                               "Type  : " . $this->getType($trace['type']) . "\n" .
                               "Args  : " . print_r($trace['args'], true) . "\n";           
            }
            $output.= "\n[Trace]\n" . $outputTrace;
        }
        return $output;
    }
    /**
     * Get the type of a function
     * 
     * @param  string $type
     * @return string 
     */
    protected function getType($type) {
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
