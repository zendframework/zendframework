<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */
namespace Zend\Log\Writer;

use Zend\Log\Filter\Priority as PriorityFilter;
use Zend\Log\Filter\FilterInterface;
use Zend\Log\Formatter\FormatterInterface;
use Zend\Log\Exception;
use Zend\Log\Logger;
use Zend\Log\Writer\WriterInterface;
use Zend\Log\Writer\AbstractWriter;

/**
 * Buffers all events until the strategy determines to flush them.
 *
 * @see        http://packages.python.org/Logbook/api/handlers.html#logbook.FingersCrossedHandler
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 */
class FingersCrossed extends AbstractWriter
{

    /**
     * The wrapped writer
     *
     * @var WriterInterface
     */
    protected $writer;

    /**
     * Flag if buffering is enabled
     *
     * @var boolean
     */
    protected $buffering = true;

    /**
     * Oldest entries are removed from the buffer if bufferSize is reached.
     * 0 is infinte buffer size.
     *
     * @var int
     */
    protected $bufferSize;

    /**
     * array of log events
     *
     * @var array
     */
    protected $buffer = array();

    /**
     * Constructor
     *
     * @param WriterInterface $writer Wrapped writer
     * @param FilterInterface|int $filterOrPriority Filter or log priority which determines buffering of events
     * @param int $bufferSize Maximum buffer size
     */
    public function __construct(WriterInterface $writer, $filterOrPriority = null, $bufferSize = 0)
    {
        $this->writer = $writer;

        if (null === $filterOrPriority) {
            $filterOrPriority = new PriorityFilter(Logger::WARN);
        } elseif (!$filterOrPriority instanceof FilterInterface) {
            $filterOrPriority = new PriorityFilter($filterOrPriority);
        }

        $this->addFilter($filterOrPriority);
        $this->bufferSize = $bufferSize;
    }

    /**
     * Log a message to this writer.
     *
     * @param array $event log data event
     * @return void
     */
    public function write(array $event)
    {
        $this->doWrite($event);
    }

    /**
     * Check if buffered data should be flushed
     *
     * @param array $event event data
     * @return boolean true if buffered data should be flushed
     */
    protected function isActivated(array $event)
    {
        foreach ($this->filters as $filter) {
            if (!$filter->filter($event)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Write message to buffer or delegate event data to the wrapped writer
     *
     * @param array $event event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        if (!$this->buffering) {
            $this->writer->write($event);
            return;
        }

        $this->buffer[] = $event;

        if ($this->bufferSize > 0 && count($this->buffer) > $this->bufferSize) {
            array_shift($this->buffer);
        }

        if (!$this->isActivated($event)) {
            return;
        }

        $this->buffering = false;

        foreach ($this->buffer as $bufferedEvent) {
            $this->writer->write($bufferedEvent);
        }
    }

    /**
     * Resets the state of the handler.
     * Stops forwarding records to the wrapped writer
     */
    public function reset()
    {
        $this->buffering = true;
    }

    /**
     * Stub in accordance to parent method signature.
     * Fomatters must be set on the wrapped writer.
     *
     * @param Formatter $formatter
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        return $this->writer;
    }

    /**
     * Record shutdown
     *
     * @return void
     */
    public function shutdown()
    {
        $this->writer->shutdown();
        $this->buffer = null;
    }
}
