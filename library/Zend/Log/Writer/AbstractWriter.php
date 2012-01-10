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
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Writer;

use Zend\Log\Factory,
    Zend\Log\Writer,
    Zend\Log\Filter,
    Zend\Log\Formatter,
    Zend\Log\Exception,
    Zend\Config\Config;

/**
 * @uses       \Zend\Log\Exception\InvalidArgumentException
 * @uses       \Zend\Log\Factory
 * @uses       \Zend\Log\Filter\Priority
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractWriter implements Writer, Factory
{
    /**
     * @var array of Filter
     */
    protected $_filters = array();

    /**
     * Formats the log message before writing.
     *
     * @var Formatter
     */
    protected $_formatter;

    /**
     * Add a filter specific to this writer.
     *
     * @param  Filter|int  $filter
     * @throws Exception\InvalidArgumentException
     * @return self
     */
    public function addFilter($filter)
    {
        if (is_int($filter)) {
            $filter = new Filter\Priority($filter);
        }

        if (!$filter instanceof Filter) {
            throw new Exception\InvalidArgumentException('Invalid filter provided');
        }

        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Log a message to this writer.
     *
     * @param  array $event log data event
     * @return void
     */
    public function write($event)
    {
        foreach ($this->_filters as $filter) {
            if (! $filter->accept($event)) {
                return;
            }
        }

        // exception occurs on error
        $this->_write($event);
    }

    /**
     * Set a new formatter for this writer
     *
     * @param  Formatter $formatter
     * @return self
     */
    public function setFormatter(Formatter $formatter)
    {
        $this->_formatter = $formatter;
        return $this;
    }

    /**
     * Perform shutdown activites such as closing open resources
     *
     * @return void
     */
    public function shutdown()
    {}

    /**
     * Write a message to the log.
     *
     * @param  array  $event  log data event
     * @return void
     */
    abstract protected function _write($event);

    /**
     * Validate and optionally convert the config to array
     *
     * @param  array|Config $config Config or Array
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    static protected function _parseConfig($config)
    {
        if ($config instanceof Config) {
            $config = $config->toArray();
        }

        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException(
                'Configuration must be an array or instance of Zend\Config\Config'
            );
        }

        return $config;
    }
}
