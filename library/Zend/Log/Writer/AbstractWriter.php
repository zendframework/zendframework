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

namespace Zend\Log\Writer;

use Zend\Log\Filter,
    Zend\Log\Formatter\FormatterInterface as Formatter,
    Zend\Log\Exception;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractWriter implements WriterInterface
{
    /**
     * Filter chain
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Formats the log message before writing
     *
     * @var Formatter
     */
    protected $formatter;

    /**
     * Add a filter specific to this writer.
     *
     * @param  Filter\FilterInterface|int $filter
     * @return AbstractWriter
     * @throws Exception\InvalidArgumentException
     */
    public function addFilter($filter)
    {
        if (is_int($filter)) {
            $filter = new Filter\Priority($filter);
        }

        if (!$filter instanceof Filter\FilterInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Filter must implement Zend\Log\Filter; received %s',
                is_object($filter) ? get_class($filter) : gettype($filter)
            ));
        }

        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Log a message to this writer.
     *
     * @param array $event log data event
     * @return void
     */
    public function write(array $event)
    {
        foreach ($this->filters as $filter) {
            if (!$filter->filter($event)) {
                return;
            }
        }

        // exception occurs on error
        $this->doWrite($event);
    }

    /**
     * Set a new formatter for this writer
     *
     * @param  Formatter $formatter
     * @return self
     */
    public function setFormatter(Formatter $formatter)
    {
        $this->formatter = $formatter;
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
     * Write a message to the log
     *
     * @param array $event log data event
     * @return void
     */
    abstract protected function doWrite(array $event);
}
