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

use Zend\Log\Formatter\Simple as SimpleFormatter,
    Zend\Log\Exception;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Stream extends AbstractWriter
{
    /**
     * Holds the PHP stream to log to.
     *
     * @var null|stream
     */
    protected $stream = null;

    /**
     * Constructor
     *
     * @param array|string|resource $streamOrUrl Stream or URL to open as a stream
     * @param string|null $mode Mode, only applicable if a URL is given
     * @return Stream
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function __construct($streamOrUrl, $mode = null)
    {
        // Setting the default mode
        if (null === $mode) {
            $mode = 'a';
        }

        if (is_resource($streamOrUrl)) {
            if ('stream' != get_resource_type($streamOrUrl)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Resource is not a stream; received "%s',
                    get_resource_type($streamOrUrl)
                ));
            }

            if ('a' != $mode) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Mode must be "a" on existing streams; received "%s"',
                    $mode
                ));
            }

            $this->stream = $streamOrUrl;
        } else {
            if (is_array($streamOrUrl) && isset($streamOrUrl['stream'])) {
                $streamOrUrl = $streamOrUrl['stream'];
            }

            if (! $this->stream = @fopen($streamOrUrl, $mode, false)) {
                throw new Exception\RuntimeException(sprintf(
                    '"%s" cannot be opened with mode "%s"',
                    $streamOrUrl,
                    $mode
                ));
            }
        }

        $this->formatter = new SimpleFormatter();
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function doWrite(array $event)
    {
        $line = $this->formatter->format($event);

        if (false === @fwrite($this->stream, $line)) {
            throw new Exception\RuntimeException("Unable to write to stream");
        }
    }

    /**
     * Close the stream resource.
     *
     * @return void
     */
    public function shutdown()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }
}
