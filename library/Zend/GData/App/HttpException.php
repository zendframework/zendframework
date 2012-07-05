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
 * @package    Zend_Gdata
 * @subpackage App
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\GData\App;

use Zend\Http;
use Zend\Http\Client\Exception\ExceptionInterface as ClientExceptionInterface;

/**
 * Gdata exceptions
 *
 * Class to represent exceptions that occur during Gdata operations.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HttpException extends Exception
{
    /** @var null|ExceptionInterface */
    protected $_httpClientException = null;
    /** @var null|Http\Response */
    protected $_response = null;

    /**
     * Create a new \Zend\GData\App\HttpException
     *
     * @param string $message Optionally set a message
     * @param ClientExceptionInterface $e Optionally pass in a Zend\Http\Client\Exception\ExceptionInterface
     * @param \Zend\Http\Response Optionally pass in a \Zend\Http\Response
     */
    public function __construct($message = null, ClientExceptionInterface $e = null, $response = null)
    {
        $this->_httpClientException = $e;
        $this->_response = $response;
        parent::__construct($message);
    }

    /**
     * Get the Zend_Http_Client_Exception.
     *
     * @return ClientExceptionInterface
     */
    public function getHttpClientException()
    {
        return $this->_httpClientException;
    }

    /**
     * Set the Http Client Exception.
     *
     * @param  ClientExceptionInterface $value
     * @return self
     */
    public function setHttpClientException(ClientExceptionInterface $value)
    {
        $this->_httpClientException = $value;
        return $this;
    }

    /**
     * Set the Http Response.
     *
     * @param \Zend\Http\Response $response
     * @return self
     */
    public function setResponse(Http\Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Get the Http Response.
     *
     * @return Http\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Get the body of the Http Response
     *
     * @return null|string
     */
    public function getRawResponseBody()
    {
        if ($this->getResponse()) {
            $response = $this->getResponse();
            return $response->getRawBody();
        }
        return null;
    }

}
