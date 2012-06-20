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
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Feed\PubSubHubbub;

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HttpResponse
{
    /**
     * The body of any response to the current callback request
     *
     * @var string
     */
    protected $content = '';

    /**
     * Array of headers. Each header is an array with keys 'name' and 'value'
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * HTTP response code to use in headers
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Send the response, including all headers
     *
     * @return void
     */
    public function send()
    {
        $this->sendHeaders();
        echo $this->getContent();
    }

    /**
     * Send all headers
     *
     * Sends any headers specified. If an {@link setHttpResponseCode() HTTP response code}
     * has been specified, it is sent with the first header.
     *
     * @return void
     */
    public function sendHeaders()
    {
        if (count($this->_headers) || (200 != $this->statusCode)) {
            $this->canSendHeaders(true);
        } elseif (200 == $this->statusCode) {
            return;
        }
        $httpCodeSent = false;
        foreach ($this->_headers as $header) {
            if (!$httpCodeSent && $this->statusCode) {
                header($header['name'] . ': ' . $header['value'], $header['replace'], $this->statusCode);
                $httpCodeSent = true;
            } else {
                header($header['name'] . ': ' . $header['value'], $header['replace']);
            }
        }
        if (!$httpCodeSent) {
            header('HTTP/1.1 ' . $this->statusCode);
            $httpCodeSent = true;
        }
    }

    /**
     * Set a header
     *
     * If $replace is true, replaces any headers already defined with that
     * $name.
     *
     * @param  string $name
     * @param  string $value
     * @param  boolean $replace
     * @return \Zend\Feed\PubSubHubbub\HttpResponse
     */
    public function setHeader($name, $value, $replace = false)
    {
        $name  = $this->_normalizeHeader($name);
        $value = (string) $value;
        if ($replace) {
            foreach ($this->_headers as $key => $header) {
                if ($name == $header['name']) {
                    unset($this->_headers[$key]);
                }
            }
        }
        $this->_headers[] = array(
            'name'    => $name,
            'value'   => $value,
            'replace' => $replace,
        );

        return $this;
    }

    /**
     * Check if a specific Header is set and return its value
     *
     * @param  string $name
     * @return string|null
     */
    public function getHeader($name)
    {
        $name = $this->_normalizeHeader($name);
        foreach ($this->_headers as $header) {
            if ($header['name'] == $name) {
                return $header['value'];
            }
        }
    }

    /**
     * Return array of headers; see {@link $_headers} for format
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Can we send headers?
     *
     * @param  boolean $throw Whether or not to throw an exception if headers have been sent; defaults to false
     * @return HttpResponse
     * @throws Exception\RuntimeException
     */
    public function canSendHeaders($throw = false)
    {
        $ok = headers_sent($file, $line);
        if ($ok && $throw) {
            throw new Exception\RuntimeException('Cannot send headers; headers already sent in ' . $file . ', line ' . $line);
        }
        return !$ok;
    }

    /**
     * Set HTTP response code to use with headers
     *
     * @param  int $code
     * @return HttpResponse
     * @throws Exception\InvalidArgumentException
     */
    public function setStatusCode($code)
    {
        if (!is_int($code) || (100 > $code) || (599 < $code)) {
            throw new Exception\InvalidArgumentException('Invalid HTTP response'
            . ' code:' . $code);
        }
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Retrieve HTTP response code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set body content
     *
     * @param  string $content
     * @return \Zend\Feed\PubSubHubbub\HttpResponse
     */
    public function setContent($content)
    {
        $this->content = (string) $content;
        $this->setHeader('content-length', strlen($content));
        return $this;
    }

    /**
     * Return the body content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Normalizes a header name to X-Capitalized-Names
     *
     * @param  string $name
     * @return string
     */
    protected function _normalizeHeader($name)
    {
        $filtered = str_replace(array('-', '_'), ' ', (string) $name);
        $filtered = ucwords(strtolower($filtered));
        $filtered = str_replace(' ', '-', $filtered);
        return $filtered;
    }
}
