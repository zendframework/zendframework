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
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mail\Transport;

use Traversable,
    Zend\Mail\AddressList,
    Zend\Mail\Header,
    Zend\Mail\Headers,
    Zend\Mail\Message,
    Zend\Mail\Transport;

/**
 * Class for sending email via the PHP internal mail() function
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Sendmail implements Transport
{
    /**
     * Config options for sendmail parameters
     *
     * @var string
     */
    protected $parameters;

    /**
     * Callback to use when sending mail; typically, {@link mailHandler()}
     * 
     * @var callable
     */
    protected $callable;

    /**
     * Headers to omit due to being in the recipients list
     * 
     * @var array
     */
    protected $recipientHeaders = array(
        'bcc',
        'cc',
        'to',
    );

    /**
     * error information
     * @var string
     */
    protected $errstr;

    /**
     * Constructor.
     *
     * @param  null|string|array|Traversable $parameters OPTIONAL (Default: null)
     * @return void
     */
    public function __construct($parameters = null)
    {
        if ($parameters !== null) {
            $this->setParameters($parameters);
        }
        $this->callable = array($this, 'mailHandler');
    }

    /**
     * Set sendmail parameters
     *
     * Used to populate the additional_parameters argument to mail()
     * 
     * @param  null|string|array|Traversable $parameters 
     * @return Sendmail
     */
    public function setParameters($parameters)
    {
        if (is_null($parameters) || is_string($parameters)) {
            $this->parameters = $parameters;
            return $this;
        }

        if (!is_array($parameters) && !$parameters instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string, array, or Traversable object of paremeters; received "%s"',
                __METHOD__,
                (is_object($parameters) ? get_class($parameters) : gettype($parameters))
            ));
        }

        $string = '';
        foreach ($parameters as $param) {
            $string .= ' ' . $param;
        }
        trim($string);

        $this->parameters = $string;
        return $this;
    }

    /**
     * Set callback to use for mail
     *
     * Primarily for testing purposes, but could be used to curry arguments.
     * 
     * @param  callable $callable 
     * @return Sendmail
     */
    public function setCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a callable argument; received "%s"',
                __METHOD__,
                (is_object($callable) ? get_class($callable) : gettype($callable))
            ));
        }
        $this->callable = $callable;
        return $this;
    }

    /**
     * Send a message
     * 
     * @param  Message $message 
     * @return void
     */
    public function send(Message $message)
    {
        $to      = $this->prepareRecipients($message);
        $subject = $this->prepareSubject($message);
        $body    = $this->prepareBody($message);
        $headers = $this->prepareHeaders($message);

        call_user_func($this->callable, $to, $subject, $body, $headers, $this->parameters);
    }

    /**
     * Prepare recipients list
     * 
     * @param  Message $message 
     * @return string
     */
    protected function prepareRecipients(Message $message)
    {
        $addressList = new AddressList();
        $to = $message->to();
        if (0 < count($to)) {
            $addressList->merge($to);
        }

        $cc = $message->cc();
        if (0 < count($cc)) {
            $addressList->merge($cc);
        }

        $bcc = $message->bcc();
        if (0 < count($bcc)) {
            $addressList->merge($bcc);
        }

        $header = new Header\To();
        $header->setAddressList($addressList);
        return $header->getFieldValue();
    }

    /**
     * Prepare the subject line string
     * 
     * @param  Message $message 
     * @return string
     */
    protected function prepareSubject(Message $message)
    {
        return $message->getSubject();
    }

    /**
     * Prepare the body string
     * 
     * @param  Message $message 
     * @return string
     */
    protected function prepareBody(Message $message)
    {
        return $message->getBodyText();
    }

    /**
     * Prepare the textual representation of headers
     * 
     * @param  Message $message
     * @return string
     */
    protected function prepareHeaders(Message $message)
    {
        $headers = $message->headers();

        $headersToSend = new Headers();
        foreach ($headers as $header) {
            if (in_array($header->getFieldName(), $this->recipientHeaders)) {
                continue;
            }
            $headersToSend->addHeader($header);
        }

        return $headersToSend->toString();
    }

    /**
     * Send mail using PHP native mail()
     *
     * @param  string $to
     * @param  string $subject
     * @param  string $message
     * @param  string $headers
     * @return void
     * @throws Exception\RuntimeException on mail failure
     */
    public function mailHandler($to, $subject, $message, $headers, $parameters)
    {
        set_error_handler(array($this, 'handleMailErrors'));
        if ($parameters === null) {
            $result = mail($to, $subject, $message, $headers);
        } else {
            $result = mail($to, $subject, $message, $headers, $parameters);
        }
        restore_error_handler();

        if ($this->errstr !== null || !$result) {
            $errstr = $this->errstr;
            if (empty($errstr)) {
                $errstr = 'Unknown error';
            }
            throw new Exception\RuntimeException('Unable to send mail: ' . $errstr);
        }
    }

    /**
     * Temporary error handler for PHP native mail().
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param array  $errcontext
     * @return true
     */
    public function handleMailErrors($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
    {
        $this->errstr = $errstr;
        return true;
    }

}
