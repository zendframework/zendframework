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

use Zend\Mail\Header;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Subject implements Header, UnstructuredHeader
{
    /**
     * @var string
     */
    protected $subject = '';

    /**
     * Header encoding
     * 
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * Factory from header line
     * 
     * @param  string $headerLine 
     * @return Subject
     */
    public static function fromString($headerLine)
    {
        $headerLine = iconv_mime_decode($headerLine, ICONV_MIME_DECODE_CONTINUE_ON_ERROR);
        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'subject') {
            throw new Exception\InvalidArgumentException('Invalid header line for Subject string');
        }

        $header = new static();
        $header->setSubject($value);
        
        return $header;
    }

    /**
     * Get the header name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Subject';
    }

    /**
     * Get the header value
     * 
     * @return string
     */
    public function getFieldValue()
    {
        $encoding = $this->getEncoding();
        if ($encoding == 'ASCII') {
            return HeaderWrap::wrap($this->subject, $this);
        }
        return HeaderWrap::mimeEncodeValue($this->subject, $encoding, true);
    }

    /**
     * Set header encoding
     * 
     * @param  string $encoding 
     * @return Subject
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
     * Set the value of the header
     * 
     * @param  string $subject 
     * @return Subject
     */
    public function setSubject($subject)
    {
        $this->subject = (string) $subject;
        return $this;
    }

    /**
     * String representation of header
     * 
     * @return string
     */
    public function toString()
    {
        return 'Subject: ' . $this->getFieldValue();
    }
}
