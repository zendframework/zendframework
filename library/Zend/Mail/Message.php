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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mail;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Message
{
    protected $headers;

    /**
     * Is the message valid?
     *
     * If we don't any From addresses, we're invalid, according to RFC2822.
     * 
     * @return bool
     */
    public function isValid()
    {
        $from = $this->from();
        if (!$from instanceof AddressList) {
            return false;
        }
        return (bool) count($from);
    }

    /**
     * Compose headers
     * 
     * @param  Headers $headers 
     * @return Message
     */
    public function setHeaders(Headers $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function headers()
    {
        if (null === $this->headers) {
            $this->setHeaders(new Headers());
            $this->headers->addHeaderLine('Orig-Date', date('r'));
        }
        return $this->headers;
    }

    /**
     * Set (overwrite) From addresses
     * 
     * @param  string|Address|AddressList $emailOrAddressList 
     * @param  string|null $name 
     * @return Message
     */
    public function setFrom($emailOrAddressList, $name = null)
    {
        $headers = $this->headers();
        if ($headers->has('from')) {
            $header = $headers->get('from');
            $headers->removeHeader($header);
        }

        return $this->addFrom($emailOrAddressList, $name);
    }

    /**
     * Add a "From" address
     * 
     * @param  string|Address|AddressList $emailOrAddressOrList 
     * @param  string|null $name 
     * @return Message
     */
    public function addFrom($emailOrAddressOrList, $name = null)
    {

    }

    public function from()
    {
    }

    public function setTo($emailOrAddressList, $name = null)
    {
    }

    public function addTo($emailOrAddressOrList, $name = null)
    {
    }

    public function to()
    {
    }

    public function setCc($emailOrAddressList, $name = null)
    {
    }

    public function addCc($emailOrAddressOrList, $name = null)
    {
    }

    public function cc()
    {
    }

    public function setBcc($emailOrAddressList, $name = null)
    {
    }

    public function addBcc($emailOrAddressOrList, $name = null)
    {
    }

    public function bcc()
    {
    }

    public function setReplyTo($emailOrAddressList, $name = null)
    {
    }

    public function addReplyTo($emailOrAddressOrList, $name = null)
    {
    }

    public function replyTo()
    {
    }

    public function setSender($emailOrAddress, $name = null)
    {
    }

    public function getSender()
    {
    }

    public function setSubject($subject)
    {
    }

    public function getSubject()
    {
    }

    public function setBody($body)
    {
    }

    public function getBody()
    {
    }

    public function getBodyText()
    {
    }
}
