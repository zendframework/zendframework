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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Writer;
use Zend\Log;

/**
 * @uses       \Zend\Log\Exception\InvalidArgumentException
 * @uses       \Zend\Log\Formatter\Simple
 * @uses       \Zend\Log\Writer\AbstractWriter
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Stream extends AbstractWriter
{
    /**
     * Holds the PHP stream to log to.
     *
     * @var null|stream
     */
    protected $_stream = null;

    /**
     * Class Constructor
     *
     * @param array|string|resource $streamOrUrl Stream or URL to open as a stream
     * @param string|null $mode Mode, only applicable if a URL is given
     * @return void
     * @throws \Zend\Log\Exception\InvalidArgumentException
     * @throws \Zend\Log\Exception\RuntimeException
     */
    public function __construct($streamOrUrl, $mode = null)
    {
        // Setting the default
        if (null === $mode) {
            $mode = 'a';
        }

        if (is_resource($streamOrUrl)) {
            if (get_resource_type($streamOrUrl) != 'stream') {
                throw new Log\Exception\InvalidArgumentException('Resource is not a stream');
            }

            if ($mode != 'a') {
                throw new Log\Exception\InvalidArgumentException('Mode cannot be changed on existing streams');
            }

            $this->_stream = $streamOrUrl;
        } else {
            if (is_array($streamOrUrl) && isset($streamOrUrl['stream'])) {
                $streamOrUrl = $streamOrUrl['stream'];
            }

            if (! $this->_stream = @fopen($streamOrUrl, $mode, false)) {
                $msg = "\"$streamOrUrl\" cannot be opened with mode \"$mode\"";
                throw new Log\Exception\RuntimeException($msg);
            }
        }

        $this->_formatter = new Log\Formatter\Simple();
    }

    /**
     * Create a new instance of Zend_Log_Writer_Stream
     *
     * @param  array|\Zend\Config\Config $config
     * @return \Zend\Log\Writer\Stream
     */
    public static function factory($config = array())
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array(
            'stream' => null,
            'mode'   => null,
        ), $config);

        $streamOrUrl = isset($config['url']) ? $config['url'] : $config['stream'];

        return new self(
            $streamOrUrl,
            $config['mode']
        );
    }

    /**
     * Close the stream resource.
     *
     * @return void
     */
    public function shutdown()
    {
        if (is_resource($this->_stream)) {
            fclose($this->_stream);
        }
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @throws \Zend\Log\Exception\RuntimeException
     * @return void
     */
    protected function _write($event)
    {
        $line = $this->_formatter->format($event);

        if (false === @fwrite($this->_stream, $line)) {
            throw new Log\Exception\RuntimeException("Unable to write to stream");
        }
    }
}
