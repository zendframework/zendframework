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
class Priority implements FilterInterface
{
    /**
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected $operator;

    /**
     * Filter logging by $priority. By default, it will accept any log
     * event whose priority value is less than or equal to $priority.
     *
     * @param int $priority Priority
     * @param string $operator Comparison operator
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($priority, $operator = null)
    {
        if (!is_int($priority)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Priority must be an integer; received "%s"',
                gettype($priority)
            ));
        }

        $this->priority = $priority;
        $this->operator = $operator === null ? '<=' : $operator;
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param array $event event data
     * @return boolean accepted?
     */
    public function filter(array $event)
    {
        return version_compare($event['priority'], $this->priority, $this->operator);
    }
}
