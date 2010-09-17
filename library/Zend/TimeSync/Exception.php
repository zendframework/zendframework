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
 * @category  Zend
 * @package   Zend_TimeSync
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\TimeSync;

/**
 * Exception class for Zend_TimeSync
 *
 * @uses      \Zend\Exception
 * @category  Zend
 * @package   Zend_TimeSync
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Exception extends \Zend\Exception
{
    /**
     * Contains array of exceptions thrown in queried server
     *
     * @var array
     */
    protected $_exceptions;

    /**
     * Adds an exception to the exception list
     *
     * @param  \Zend\TimeSync\Exception $exception New exteption to throw
     * @return void
     */
    public function addException(Exception $exception)
    {
        $this->_exceptions[] = $exception;
    }

    /**
     * Returns an array of exceptions that were thrown
     *
     * @return array
     */
    public function get()
    {
        return $this->_exceptions;
    }
}
