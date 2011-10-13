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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log;

/**
 * @category   Zend
 * @package    Zend_Log
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Loggable
{
    /**
     * @param string $message
     * @param array|null $extra
     * @return Loggabble
     */
    public function emerg($message, array $extra = null);

    /**
     * @param string $message
     * @param array|null $extra
     * @return Loggabble
     */
    public function alert($message, array $extra = null);

    /**
     * @param string $message
     * @param array|null $extra
     * @return Loggabble
     */
    public function crit($message, array $extra = null);

    /**
     * @param string $message
     * @param array|null $extra
     * @return Loggabble
     */
    public function err($message, array $extra = null);

    /**
     * @param string $message
     * @param array|null $extra
     * @return Loggabble
     */
    public function warn($message, array $extra = null);

    /**
     * @param string $message
     * @param array|null $extra
     * @return Loggabble
     */
    public function notice($message, array $extra = null);

    /**
     * @param string $message
     * @param array|null $extra
     * @return Loggabble
     */
    public function info($message, array $extra = null);

    /**
     * @param string $message
     * @param array|null $extra
     * @return Loggabble
     */
    public function debug($message, array $extra = null);
}