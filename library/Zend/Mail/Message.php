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

use Traversable;

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
            $this->headers->addHeaderLine('Orig-Date', date('r'));
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
        $this->updateAddressList(__METHOD__, $addressList, $emailOrAddressOrList, $name);
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
        $this->updateAddressList(__METHOD__, $addressList, $emailOrAddressOrList, $name);
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
        $this->updateAddressList(__METHOD__, $addressList, $emailOrAddressOrList, $name);
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
        $this->updateAddressList(__METHOD__, $addressList, $emailOrAddressOrList, $name);
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
        $this->updateAddressList(__METHOD__, $addressList, $emailOrAddressOrList, $name);
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
        $headers = $this->headers();
        if ($headers->has($headerName)) {
            $header = $headers->get($headerName);
        } else {
            $header = new $headerClass();
            $headers->addHeader($header);
        }
        return $header->getAddressList();
    }

    /**
     * Update an address list
     *
     * Proxied to this from addFrom, addTo, addCc, addBcc, and addReplyTo.
     * 
     * @param  string $callingMethod 
     * @param  AddressList $addressList 
     * @param  string|AddressDescription|array|AddressList|Traversable $emailOrAddressOrList 
     * @param  null|string $name 
     * @return void
     */
    protected function updateAddressList($callingMethod, AddressList $addressList, $emailOrAddressOrList, $name)
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
}
