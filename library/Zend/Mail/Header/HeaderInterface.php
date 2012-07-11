<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace Zend\Mail\Header;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 */
interface HeaderInterface
{
    /**
     * Format value in Mime-Encoding if not US-ASCII encoding is used
     *
     * @var boolean
     */
    const FORMAT_ENCODED = true;

    /**
     * Return value with the interval ZF2 value (UTF-8 non-encoded)
     *
     * @var boolean
     */
    const FORMAT_RAW     = false;


    /**
     * Factory to generate a header object from a string
     *
     * @param string $headerLine
     * @return self
     */
    public static function fromString($headerLine);

    /**
     * Retrieve header name
     *
     * @return string
     */
    public function getFieldName();

    /**
     * Retrieve header value
     *
     * @param  boolean $format Return the value in Mime::Encoded or in Raw format
     * @return string
     */
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW);

    /**
     * Set header encoding
     *
     * @param  string $encoding
     * @return self
     */
    public function setEncoding($encoding);

    /**
     * Get header encoding
     *
     * @return string
     */
    public function getEncoding();

    /**
     * Cast to string
     *
     * Returns in form of "NAME: VALUE"
     *
     * @return string
     */
    public function toString();
}
