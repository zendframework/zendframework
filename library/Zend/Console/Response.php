<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace Zend\Console;

use Zend\Stdlib\Message;
use Zend\Stdlib\ResponseInterface;

/**
 * @category   Zend
 * @package    Zend_Console
 */
class Response extends Message implements ResponseInterface
{
    protected $contentSent = false;

    public function contentSent()
    {
        return $this->contentSent;
    }

    /**
     * Set the error level that will be returned to shell.
     *
     * @param integer   $errorLevel
     * @return Response
     */
    public function setErrorLevel($errorLevel)
    {
        $this->setMetadata('errorLevel', $errorLevel);
        return $this;
    }

    /**
     * Get response error level that will be returned to shell.
     *
     * @return integer|0
     */
    public function getErrorLevel()
    {
        return $this->getMetadata('errorLevel', 0);
    }

    public function sendContent()
    {
        if ($this->contentSent()) {
            return $this;
        }
        echo $this->getContent();
        $this->contentSent = true;
        return $this;
    }

    public function send()
    {
        $this->sendContent();
        $errorLevel = (int)$this->getMetadata('errorLevel',0);
        exit($errorLevel);
    }
}
