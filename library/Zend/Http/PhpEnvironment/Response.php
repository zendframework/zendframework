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
     * The current used version
     * (The value will be detected on getVersion)
     *
     * @var null|string
     */
    protected $version;

    /**
     * Return the HTTP version for this response
     *
     * @return string
     * @see \Zend\Http\AbstractMessage::getVersion()
     */
    public function getVersion()
    {
        if (!$this->version) {
            $this->version = $this->detectVersion();
        }
        return $this->version;
    }

    /**
     * Detect the current used protocol version.
     * If detection failed it falls back to version 1.0.
     *
     * @return string
     */
    protected function detectVersion()
    {
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
            return self::VERSION_11;
        }

        return self::VERSION_10;
    }

}
