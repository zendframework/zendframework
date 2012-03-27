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
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mail\Header;

use Zend\Mail\AddressList,
    Zend\Mail\Header;

/**
 * Base class for headers composing address lists (to, from, cc, bcc, reply-to)
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAddressList implements Header
{
    /**
     * @var AddressList
     */
    protected $addressList;

    /**
     * @var string Normalized field name
     */
    protected $fieldName;

    /**
     * Header encoding
     * 
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * @var string lowercased field name
     */
    protected static $type;

    /**
     * Parse string to create header object
     * 
     * @param  string $headerLine 
     * @return AbstractAddressList
     */
    public static function fromString($headerLine)
    {
        $headerLine = iconv_mime_decode($headerLine, ICONV_MIME_DECODE_CONTINUE_ON_ERROR);

        // split into name/value
        list($fieldName, $fieldValue) = explode(': ', $headerLine, 2);

        if (strtolower($fieldName) !== static::$type) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid header line for "%s" string',
                __CLASS__
            ));
        }
        $header = new static();

        // split value on ","
        $fieldValue = str_replace("\r\n ", " ", $fieldValue);
        $values     = explode(',', $fieldValue);
        array_walk($values, 'trim');

        $addressList = $header->getAddressList();
        foreach ($values as $address) {
            // split values into name/email
            if (!preg_match('/^((?<name>.*?)<(?<namedEmail>[^>]+)>|(?<email>.+))$/', $address, $matches)) {
                // Should we raise an exception here?
                continue;
            }
            $name = null;
            if (isset($matches['name'])) {
                $name  = trim($matches['name']);
            }
            if (empty($name)) {
                $name = null;
            } else {
                $name = iconv_mime_decode($name, ICONV_MIME_DECODE_CONTINUE_ON_ERROR);
            }

            if (isset($matches['namedEmail'])) {
                $email = $matches['namedEmail'];
            }
            if (isset($matches['email'])) {
                $email = $matches['email'];
            }
            $email = trim($email); // we may have leading whitespace

            // populate address list
            $addressList->add($email, $name);
        }

        return $header;
    }

    /**
     * Get field name of this header
     * 
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Get field value of this header
     * 
     * @return string
     */
    public function getFieldValue()
    {
        $emails   = array();
        $encoding = $this->getEncoding();
        foreach ($this->getAddressList() as $address) {
            $email = $address->getEmail();
            $name  = $address->getName();
            if (empty($name)) {
                $emails[] = $email;
            } else {
                if (false !== strstr($name, ',')) {
                    $name = sprintf('"%s"', $name);
                }

                if ('ASCII' !== $encoding) {
                    $name = HeaderWrap::mimeEncodeValue($name, $encoding, false);
                }
                $emails[] = sprintf('%s <%s>', $name, $email);
            }
        }
        $string = implode(",\r\n ", $emails);
        return $string;
    }

    /**
     * Set header encoding
     * 
     * @param  string $encoding 
     * @return AbstractAddressList
     */
    public function setEncoding($encoding) 
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get header encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set address list for this header
     * 
     * @param  AddressList $addressList 
     * @return void
     */
    public function setAddressList(AddressList $addressList)
    {
        $this->addressList = $addressList;
    }

    /**
     * Get address list managed by this header
     * 
     * @return AddressList
     */
    public function getAddressList()
    {
        if (null === $this->addressList) {
            $this->setAddressList(new AddressList());
        }
        return $this->addressList;
    }

    /**
     * Serialize to string
     * 
     * @return string
     */
    public function toString()
    {
        $name  = $this->getFieldName();
        $value = $this->getFieldValue();
        return sprintf("%s: %s\r\n", $name, $value);
    }
} 
