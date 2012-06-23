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

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MimeVersion implements HeaderInterface
{
    /**
     * @var string Version string
     */
    protected $version = '1.0';

    /**
     * Unserialize from string
     *
     * @param  string $headerLine
     * @throws Exception\InvalidArgumentException
     * @return MimeVersion
     */
    public static function fromString($headerLine)
    {
        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'mime-version') {
            throw new Exception\InvalidArgumentException('Invalid header line for Mime-Version string');
        }

        // Check for version, and set if found
        $header = new static();
        if (preg_match('/^(?P<version>\d+\.\d+)$/', $value, $matches)) {
            $header->version = $matches['version'];
        }

        return $header;
    }

    /**
     * Get the field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Mime-Version';
    }

    /**
     * Get the field value (version string)
     *
     * @return string
     */
    public function getFieldValue()
    {
        return $this->version;
    }

    /**
     * Set character encoding
     *
     * @param  string $encoding
     */
    public function setEncoding($encoding)
    {
        // irrelevant to this implementation
    }

    /**
     * Get character encoding
     *
     */
    public function getEncoding()
    {
        // irrelevant to this implementation
    }

    /**
     * Serialize to string
     *
     * @return string
     */
    public function toString()
    {
        return 'Mime-Version: ' . $this->getFieldValue();
    }

    /**
     * Set the version string used in this header
     *
     * @param  string $version
     * @return MimeVersion
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Retrieve the version string for this header
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
