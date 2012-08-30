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

use Zend\Log\Formatter\FormatterInterface;
use Zend\Log\Exception;
use Zend\Log\Logger;
use Zend\Log\Writer\FingersCrossed\ErrorLevelActivationStrategy;
use Zend\Log\Writer\FingersCrossed\ActivationStrategyInterface;
use Zend\Log\Writer\WriterInterface;
use Zend\Log\Writer\AbstractWriter;

/**
 * Buffers all events until the strategy determines to flush them.
 *
 * @category Zend
 * @package Zend_Log
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
     * Strategy which determines if events are buffered
     *
     * @var ActivationStrategyInterface
     */
    protected $activationStrategy;

    /**
     * Constructor
     *
     * @param WriterInterface $writer Wrapped writer
     * @param ActivationStrategyInterface|int $activationStrategyOrPriority Strategy or log priority which determines buffering of events
     * @param int $bufferSize Maximum buffer size
     */
    public function __construct(WriterInterface $writer, $activationStrategyOrPriority = null, $bufferSize = 0)
    {
        $this->writer = $writer;

        if ($activationStrategyOrPriority === null) {
            $this->activationStrategy = new ErrorLevelActivationStrategy(Logger::WARN);
        } elseif (! $activationStrategyOrPriority instanceof ActivationStrategyInterface) {
            $this->activationStrategy = new ErrorLevelActivationStrategy($activationStrategyOrPriority);
        } else {
            $this->activationStrategy = $activationStrategyOrPriority;
        }

        $this->bufferSize = $bufferSize;
    }

    /**
     * Write message to buffer or delegate event data to the wrapped writer
     *
     * @param array $event event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        if ($this->buffering) {
            $this->buffer[] = $event;

            if ($this->bufferSize > 0 && count($this->buffer) > $this->bufferSize) {
                array_shift($this->buffer);
            }

            if ($this->activationStrategy->isWriterActivated($event)) {
                $this->buffering = false;

                foreach ($this->buffer as $bufferedEvent) {
                    $this->writer->write($bufferedEvent);
                }
            }
        } else {
            $this->writer->write($event);
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
     * Prevent setting a formatter for this writer
     *
     * @param Formatter $formatter
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        throw new Exception\InvalidArgumentException('Formatter must be set on the wrapped writer');
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