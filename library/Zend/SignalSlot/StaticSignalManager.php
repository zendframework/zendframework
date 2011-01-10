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
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\SignalSlot;

/**
 * Interface for global (static) message delivery
 *
 * @category   Zend
 * @package    Zend_SignalSlot
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface StaticSignalManager
{
    public static function emit($signal, $args = null);
    public static function emitUntil($callback, $signal, $args = null);
    public static function connect($signalOrAggregate, $callback = null, $priority = 1);
    public static function detach($handle);
    public static function getSignals();
    public static function getHandlers($signal);
    public static function clearHandlers($signal);
}
