<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Writer\ChromePhp;

interface ChromePhpInterface
{
    /**
     * Log a message
     *
     * @param string $line
     */
    public function log($line);

    /**
     * Log a warning message
     *
     * @param string $line
     */
    public function warn($line);

    /**
     * Log an error message
     *
     * @param string $line
     */
    public function error($line);

    /**
     * Log an info message
     *
     * @param string $line
     */
    public function info($line);

    /**
     * Sends a group log
     *
     * @param string $line
     */
    public function group($line);

    /**
     * Sends a collapsed group log
     *
     * @param string $line
     */
    public function groupCollapsed($line);

    /**
     * Ends a group log
     *
     * @param string $line
     */
    public function groupEnd($line);
}
