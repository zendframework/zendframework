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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mail;

use Traversable,
    Zend\Mime\Message as MimeMessage;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Message
{
    /**
     * Content of the message
     * 
     * @var null|string|object
     */
    protected $body;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * Message encoding
     *
     * Used to determine whether or not to encode headers; defaults to ASCII.
     * 
     * @var string
     */
    protected $encoding = 'ASCII';

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
     * Set the message encoding
     * 
     * @param  string $encoding 
     * @return Message
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        $this->headers()->setEncoding($encoding);
        return $this;
    }

    /**
     * Get the message encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
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
        $headers->setEncoding($this->getEncoding());
        return $this;
    }

    /**
     * Access headers collection
     *
     * Lazy-loads if not already attached.
     * 
     * @return Headers
     */
    public function headers()
    {
        if (null === $this->headers) {
            $this->setHeaders(new Headers());
            $this->headers->addHeaderLine('Date', date('r'));
        }
        return $this->headers;
    }

    /**
     * Set (overwrite) From addresses
     * 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressList 
     * @param  string|null $name 
     * @return Message
     */
    public function setFrom($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('from');
        return $this->addFrom($emailOrAddressList, $name);
    }

    /**
     * Add a "From" address
     * 
     * @param  string|Address|array|AddressList|Traversable $emailOrAddressOrList 
     * @param  string|null $name 
     * @return Message
     */
    public function addFrom($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->from();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    /**
     * Retrieve list of From senders
     * 
     * @return AddressList
     */
    public function from()
    {
        return $this->getAddressListFromHeader('from', __NAMESPACE__ . '\Header\From');
    }

    /**
     * Overwrite the address list in the To recipients
     * 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressList 
     * @param  null|string $name 
     * @return Message
     */
    public function setTo($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('to');
        return $this->addTo($emailOrAddressList, $name);
    }

    /**
     * Add one or more addresses to the To recipients
     *
     * Appends to the list.
     * 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressOrList 
     * @param  null|string $name 
     * @return Message
     */
    public function addTo($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->to();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    /**
     * Access the address list of the To header
     * 
     * @return AddressList
     */
    public function to()
    {
        return $this->getAddressListFromHeader('to', __NAMESPACE__ . '\Header\To');
    }

    /**
     * Set (overwrite) CC addresses
     * 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressList 
     * @param  string|null $name 
     * @return Message
     */
    public function setCc($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('cc');
        return $this->addCc($emailOrAddressList, $name);
    }

    /**
     * Add a "Cc" address
     * 
     * @param  string|Address|array|AddressList|Traversable $emailOrAddressOrList 
     * @param  string|null $name 
     * @return Message
     */
    public function addCc($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->cc();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    /**
     * Retrieve list of CC recipients
     * 
     * @return AddressList
     */
    public function cc()
    {
        return $this->getAddressListFromHeader('cc', __NAMESPACE__ . '\Header\Cc');
    }

    /**
     * Set (overwrite) BCC addresses
     * 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressList 
     * @param  string|null $name 
     * @return Message
     */
    public function setBcc($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('bcc');
        return $this->addBcc($emailOrAddressList, $name);
    }

    /**
     * Add a "Bcc" address
     * 
     * @param  string|Address|array|AddressList|Traversable $emailOrAddressOrList 
     * @param  string|null $name 
     * @return Message
     */
    public function addBcc($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->bcc();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    /**
     * Retrieve list of BCC recipients
     * 
     * @return AddressList
     */
    public function bcc()
    {
        return $this->getAddressListFromHeader('bcc', __NAMESPACE__ . '\Header\Bcc');
    }

    /**
     * Overwrite the address list in the Reply-To recipients
     * 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressList 
     * @param  null|string $name 
     * @return Message
     */
    public function setReplyTo($emailOrAddressList, $name = null)
    {
        $this->clearHeaderByName('reply-to');
        return $this->addReplyTo($emailOrAddressList, $name);
    }

    /**
     * Add one or more addresses to the Reply-To recipients
     *
     * Appends to the list.
     * 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressOrList 
     * @param  null|string $name 
     * @return Message
     */
    public function addReplyTo($emailOrAddressOrList, $name = null)
    {
        $addressList = $this->replyTo();
        $this->updateAddressList($addressList, $emailOrAddressOrList, $name, __METHOD__);
        return $this;
    }

    /**
     * Access the address list of the Reply-To header
     * 
     * @return AddressList
     */
    public function replyTo()
    {
        return $this->getAddressListFromHeader('reply-to', __NAMESPACE__ . '\Header\ReplyTo');
    }

    /**
     * setSender 
     * 
     * @param mixed $emailOrAddress 
     * @param mixed $name 
     * @return void
     */
    public function setSender($emailOrAddress, $name = null)
    {
        $header = $this->getHeader('sender', __NAMESPACE__ . '\Header\Sender');
        $header->setAddress($emailOrAddress, $name);
        return $this;
    }

    /**
     * Retrieve the sender address, if any
     * 
     * @return null|AddressDescription
     */
    public function getSender()
    {
        $header = $this->getHeader('sender', __NAMESPACE__ . '\Header\Sender');
        return $header->getAddress();
    }

    /**
     * Set the message subject header value
     * 
     * @param  string $subject 
     * @return Message
     */
    public function setSubject($subject)
    {
        $headers = $this->headers();
        if (!$headers->has('subject')) {
            $header = new Header\Subject();
            $headers->addHeader($header);
        } else {
            $header = $headers->get('subject');
        }
        $header->setSubject($subject);
        return $this;
    }

    /**
     * Get the message subject header value
     * 
     * @return null|string
     */
    public function getSubject()
    {
        $headers = $this->headers();
        if (!$headers->has('subject')) {
            return null;
        }
        $header = $headers->get('subject');
        return $header->getFieldValue();
    }

    /**
     * Set the message body
     * 
     * @param  null|string|MimeMessage|object $body 
     * @return Message
     */
    public function setBody($body)
    {
        if (!is_string($body) && $body !== null) {
            if (!is_object($body)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    '%s expects a string or object argument; received "%s"',
                    __METHOD__,
                    gettype($body)
                ));
            }
            if (!$body instanceof MimeMessage) {
                if (!method_exists($body, '__toString')) {
                    throw new Exception\InvalidArgumentException(sprintf(
                        '%s expects object arguments of type Zend\Mime\Message or implementing __toString(); object of type "%s" received',
                        __METHOD__,
                        get_class($body)
                    ));
                }
            }
        }
        $this->body = $body;

        if (!$this->body instanceof MimeMessage) {
            return $this;
        }

        // Get headers, and set Mime-Version header
        $headers = $this->headers();
        $this->getHeader('mime-version', __NAMESPACE__ . '\Header\MimeVersion');

        // Multipart content headers
        if ($this->body->isMultiPart()) {
            $mime   = $this->body->getMime();
            $header = $this->getHeader('content-type', __NAMESPACE__ . '\Header\ContentType');
            $header->setType('multipart/mixed');
            $header->addParameter('boundary', $mime->boundary());
            return $this;
        }

        // MIME single part headers
        $parts = $this->body->getParts();
        if (!empty($parts)) {
            $part = array_shift($parts);
            $headers->addHeaders($part->getHeadersArray());
        }
        return $this;
    }

    /**
     * Return the currently set message body
     * 
     * @return null|object
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the string-serialized message body text
     * 
     * @return string
     */
    public function getBodyText()
    {
        if ($this->body instanceof MimeMessage) {
            return $this->body->generateMessage();
        }

        return (string) $this->body;
    }

    /**
     * Retrieve a header by name
     *
     * If not found, instantiates one based on $headerClass.
     * 
     * @param  string $headerName 
     * @param  string $headerClass 
     * @return Header
     */
    protected function getHeader($headerName, $headerClass)
    {
        $headers = $this->headers();
        if ($headers->has($headerName)) {
            $header = $headers->get($headerName);
        } else {
            $header = new $headerClass();
            $headers->addHeader($header);
        }
        return $header;
    }

    /**
     * Clear a header by name
     * 
     * @param  string $headerName 
     * @return void
     */
    protected function clearHeaderByName($headerName)
    {
        $headers = $this->headers();
        if ($headers->has($headerName)) {
            $header = $headers->get($headerName);
            $headers->removeHeader($header);
        }
    }

    /**
     * Retrieve the AddressList from a named header
     *
     * Used with To, From, Cc, Bcc, and ReplyTo headers. If the header does not
     * exist, instantiates it.
     * 
     * @param  string $headerName 
     * @param  string $headerClass 
     * @return AddressList
     */
    protected function getAddressListFromHeader($headerName, $headerClass)
    {
        $header = $this->getHeader($headerName, $headerClass);
        if (!$header instanceof Header\AbstractAddressList) {
            throw new Exception\DomainException(sprintf(
                'Cannot grab address list from header of type "%s"; not an AbstractAddressList implementation',
                get_class($header)
            ));
        }
        return $header->getAddressList();
    }

    /**
     * Update an address list
     *
     * Proxied to this from addFrom, addTo, addCc, addBcc, and addReplyTo.
     * 
     * @param  AddressList $addressList 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressOrList 
     * @param  null|string $name 
     * @param  string $callingMethod 
     * @return void
     */
    protected function updateAddressList(AddressList $addressList, $emailOrAddressOrList, $name, $callingMethod)
    {
        if ($emailOrAddressOrList instanceof Traversable) {
            foreach ($emailOrAddressOrList as $address) {
                $addressList->add($address);
            }
            return;
        }
        if (is_array($emailOrAddressOrList)) {
            $addressList->addMany($emailOrAddressOrList);
            return;
        }
        if (!is_string($emailOrAddressOrList) && !$emailOrAddressOrList instanceof AddressDescription) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string, AddressDescription, array, AddressList, or Traversable as its first argument; received "%s"',
                $callingMethod,
                (is_object($emailOrAddressOrList) ? get_class($emailOrAddressOrList) : gettype($emailOrAddressOrList))
            ));
        }
        $addressList->add($emailOrAddressOrList, $name);
    }

    /**
     * Serialize to string
     * 
     * @return string
     */
    public function toString()
    {
        $headers = $this->headers();
        return $headers->toString() . "\r\n" . $this->getBodyText();
    }
}
