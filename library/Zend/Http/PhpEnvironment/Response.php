<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\PhpEnvironment;

use Zend\Http\Header\MultipleHeaderInterface;
use Zend\Http\Response as HttpResponse;

/**
 * HTTP Response for current PHP environment
 *
 * @category   Zend
 * @package    Zend_Http
 */
class Response extends HttpResponse
{
    /**
     * @var bool
     */
    protected $headersSent = false;

    /**
     * @var bool
     */
    protected $contentSent = false;

    /**
     * Construct
     * Instantiates response.
     */
    public function __construct()
    {
        // autodetect default HTTP version
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0') {
                $this->setVersion(self::VERSION_10);
            } elseif ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
                $this->setVersion(self::VERSION_11);
            }
        }
    }

    /**
     * @return bool
     */
    public function headersSent()
    {
        return $this->headersSent;
    }

    /**
     * @return bool
     */
    public function contentSent()
    {
        return $this->contentSent;
    }

    /**
     * Send HTTP headers
     *
     * @return Response
     */
    public function sendHeaders()
    {
        if ($this->headersSent()) {
            return $this;
        }

        $status  = $this->renderStatusLine();
        header($status);

        /** @var \Zend\Http\Header\HeaderInterface $header */
        foreach ($this->getHeaders() as $header) {
            if ($header instanceof MultipleHeaderInterface) {
                header($header->toString(), false);
                continue;
            }
            header($header->toString());
        }

        $this->headersSent = true;
        return $this;
    }

    /**
     * Send content
     *
     * @return Response
     */
    public function sendContent()
    {
        if ($this->contentSent()) {
            return $this;
        }

        echo $this->getContent();
        $this->contentSent = true;
        return $this;
    }

    /**
     * Send HTTP response
     *
     * @return Response
     */
    public function send()
    {
        $this->sendHeaders()
             ->sendContent();
        return $this;
    }
}
