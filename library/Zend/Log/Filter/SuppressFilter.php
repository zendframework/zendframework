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
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Log\Filter;

use Zend\Log\Exception;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SuppressFilter implements FilterInterface
{
    /**
     * @var boolean
     */
    protected $accept = true;

    /**
     * This is a simple boolean filter.
     *
     * Call suppress(true) to suppress all log events.
     * Call suppress(false) to accept all log events.
     *
     * @param boolean $suppress Should all log events be suppressed?
     * @return void
     */
    public function suppress($suppress)
    {
        $this->accept = ! (bool) $suppress;
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param array $event event data
     * @return boolean accepted?
     */
    public function filter(array $event)
    {
        return $this->accept;
    }
}
