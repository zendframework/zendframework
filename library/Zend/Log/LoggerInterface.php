<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log;

/**
 * @category   Zend
 * @package    Zend_Log
 */
interface LoggerInterface
{
    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return Loggabble
     */
    public function emerg($message, $extra = array());

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return Loggabble
     */
    public function alert($message, $extra = array());

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return Loggabble
     */
    public function crit($message, $extra = array());

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return Loggabble
     */
    public function err($message, $extra = array());

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return Loggabble
     */
    public function warn($message, $extra = array());

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return Loggabble
     */
    public function notice($message, $extra = array());

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return Loggabble
     */
    public function info($message, $extra = array());

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return Loggabble
     */
    public function debug($message, $extra = array());
}
