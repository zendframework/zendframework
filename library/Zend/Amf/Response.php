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
 * @package    Zend_Amf
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Amf;

/**
 * Handles converting the PHP object ready for response back into AMF
 *
 * @uses       \Zend\Amf\Constants
 * @uses       \Zend\Amf\Parser\Amf0\Serializer
 * @uses       \Zend\Amf\Parser\OutputStream
 * @package    Zend_Amf
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Response
{
    /**
     * Instantiate new output stream and start serialization
     *
     * @return \Zend\Amf\Response
     */
    public function finalize();

    /**
     * Serialize the PHP data types back into Actionscript and
     * create and AMF stream.
     *
     * @param  Zend\Amf\Parser\OutputStream $stream
     * @return Zend\Amf\Response
     */
    public function writeMessage(Parser\OutputStream $stream);

    /**
     * Return the output stream content
     *
     * @return string The contents of the output stream
     */
    public function getResponse();

    /**
     * Return the output stream content
     *
     * @return string
     */
    public function __toString();

    /**
     * Add an AMF body to be sent to the Flash Player
     *
     * @param  Zend\Amf\Value\MessageBody $body
     * @return Zend\Amf\Response
     */
    public function addAmfBody(Value\MessageBody $body);

    /**
     * Return an array of AMF bodies to be serialized
     *
     * @return array
     */
    public function getAmfBodies();

    /**
     * Add an AMF Header to be sent back to the flash player
     *
     * @param  Zend\Amf\Value\MessageHeader $header
     * @return Zend\Amf\Response
     */
    public function addAmfHeader(Value\MessageHeader $header);

    /**
     * Retrieve attached AMF message headers
     *
     * @return array Array of \Zend\Amf\Value\MessageHeader objects
     */
    public function getAmfHeaders();

    /**
     * Set the AMF encoding that will be used for serialization
     *
     * @param  int $encoding
     * @return Zend\Amf\Response
     */
    public function setObjectEncoding($encoding);
}
