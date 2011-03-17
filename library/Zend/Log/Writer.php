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
interface Writer
{
    /**
     * Add a log filter to the writer
     *
     * @param  int|\Zend\Log\Filter $filter
     * @return Writer
     */
    public function addFilter($filter);

    /**
     * Set a message formatter for the writer
     *
     * @param  \Zend\Log\Formatter|Callable $formatter
     * @return Writer
     */
    public function setFormatter(Formatter $formatter);

    /**
     * Write a log message
     *
     * @param  array|mixed $event
     * @return Writer
     */
    public function write($event);

    /**
     * Perform shutdown activities
     *
     * @return void
     */
    public function shutdown();
}
