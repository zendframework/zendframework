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
 * @package    Zend_Authentication
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Authentication;

/**
 * @category   Zend
 * @package    Zend_Authentication
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Storage
{
    /**
     * Returns true if and only if storage is empty
     *
     * @throws Zend\Authentication\Storage\Exception If it is impossible to determine whether storage is empty
     * @return boolean
     */
    public function isEmpty();

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws Zend\Authentication\Storage\Exception If reading contents from storage is impossible
     * @return mixed
     */
    public function read();

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws Zend\Authentication\Storage\Exception If writing $contents to storage is impossible
     * @return void
     */
    public function write($contents);

    /**
     * Clears contents from storage
     *
     * @throws Zend\Authentication\Storage\Exception If clearing contents from storage is impossible
     * @return void
     */
    public function clear();
}
