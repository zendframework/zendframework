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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Log\Filter;

/**
 * @uses       \Zend\Log\Exception
 * @uses       \Zend\Log\Filter\AbstractFilter
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Priority extends AbstractFilter
{
    /**
     * @var integer
     */
    protected $_priority;

    /**
     * @var string
     */
    protected $_operator;

    /**
     * Filter logging by $priority.  By default, it will accept any log
     * event whose priority value is less than or equal to $priority.
     *
     * @param  integer  $priority  Priority
     * @param  string   $operator  Comparison operator
     * @throws \Zend\Log\Exception
     */
    public function __construct($priority, $operator = null)
    {
        if (! is_integer($priority)) {
            throw new \Zend\Log\Exception('Priority must be an integer');
        }

        $this->_priority = $priority;
        $this->_operator = $operator === null ? '<=' : $operator;
    }

    /**
     * Create a new instance of Zend_Log_Filter_Priority
     * 
     * @param  array|\Zend\Config\Config $config
     * @return \Zend\Log\Filter\Priority
     * @throws \Zend\Log\Exception
     */
    static public function factory($config = array()) 
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array(
            'priority' => null, 
            'operator' => null,
        ), $config);

        // Add support for constants
        if (!is_numeric($config['priority']) && isset($config['priority']) && defined($config['priority'])) {
            $config['priority'] = constant($config['priority']);
        }

        if (!is_numeric($config['priority'])) {
        	throw new \Zend\Log\Exception('Priority must be an integer.');
        }
        
        return new self(
            (int) $config['priority'], 
            $config['operator']
        );
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param  array    $event    event data
     * @return boolean            accepted?
     */
    public function accept($event)
    {
        return version_compare($event['priority'], $this->_priority, $this->_operator);
    }
}
