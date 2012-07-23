<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Request;

use Zend\Amf\Parser;
use Zend\Amf\Value;

/**
 * Handle the incoming AMF request by deserializing the data to php object
 * types and storing the data for Zend_Amf_Server to handle for processing.
 *
 * @todo       Currently not checking if the object needs to be Type Mapped to a server object.
 * @package    Zend_Amf
 */
interface RequestInterface
{
    /**
     * Prepare the AMF InputStream for parsing.
     *
     * @param  string $request
     * @return RequestInterface
     */
    public function initialize($request);

    /**
     * Takes the raw AMF input stream and converts it into valid PHP objects
     *
     * @param  Parser\InputStream
     * @return RequestInterface
     */
    public function readMessage(Parser\InputStream $stream);

    /**
     * Deserialize a message header from the input stream.
     *
     * A message header is structured as:
     * - NAME String
     * - MUST UNDERSTAND Boolean
     * - LENGTH Int
     * - DATA Object
     *
     * @return Value\MessageHeader
     */
    public function readHeader();

    /**
     * Deserialize a message body from the input stream
     *
     * @return Value\MessageBody
     */
    public function readBody();

    /**
     * Return an array of the body objects that were found in the amf request.
     *
     * @return array {target, response, length, content}
     */
    public function getAmfBodies();

    /**
     * Accessor to private array of message bodies.
     *
     * @param  Value\MessageBody $message
     * @return RequestInterface
     */
    public function addAmfBody(Value\MessageBody $message);

    /**
     * Return an array of headers that were found in the amf request.
     *
     * @return array {operation, mustUnderstand, length, param}
     */
    public function getAmfHeaders();

    /**
     * Return the either 0 or 3 for respect AMF version
     *
     * @return int
     */
    public function getObjectEncoding();

    /**
     * Set the object response encoding
     *
     * @param  mixed $int
     * @return RequestInterface
     */
    public function setObjectEncoding($int);
}
