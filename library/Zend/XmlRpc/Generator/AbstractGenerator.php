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
 * @package    Zend_XmlRpc
 * @subpackage Generator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\XmlRpc\Generator;

use Zend\XmlRpc\Generator as GeneratorAdapter;

/**
 * Abstract XML generator adapter
 *
 * @uses Zend\XmlRpc\Generator
 */
abstract class AbstractGenerator implements GeneratorAdapter
{
    /**
     * XML encoding string
     *
     * @var string
     */
    protected $_encoding;

    /**
     * Construct new instance of the generator
     *
     * @param string $encoding XML encoding, default UTF-8
     */
    public function __construct($encoding = 'UTF-8')
    {
        $this->setEncoding($encoding);
        $this->_init();
    }

    /**
     * Initialize internal objects
     * 
     * @return void
     */
    abstract protected function _init();

    /**
     * Start XML element
     *
     * Method opens a new XML element with an element name and an optional value
     * 
     * @param string $name XML tag name
     * @param string $value Optional value of the XML tag
     * @return Zend_XmlRpc_Generator_Abstract Fluent interface
     */
    public function openElement($name, $value = null)
    {
        $this->_openElement($name);
        if ($value !== null) {
            $this->_writeTextData($value);
        }

        return $this;
    }

    /**
     * End of an XML element
     *
     * Method marks the end of an XML element
     *
     * @param string $name XML tag name
     * @return Zend_XmlRpc_Generator_Abstract Fluent interface
     */
    public function closeElement($name)
    {
        $this->_closeElement($name);

        return $this;
    }

    /**
     * Return encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Set XML encoding
     * 
     * @param  string $encoding 
     * @return Zend\XmlRpc\Generator\AbstractGenerator
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;
        return $this;
    }

    /**
     * Returns the XML as a string and flushes all internal buffers
     *
     * @return string
     */
    public function flush()
    {
        $xml = $this->saveXml();
        $this->_init();
        return $xml;
    }

    /**
     * Returns XML without document declaration
     *
     * @return string
     */
    public function __toString()
    {
        return $this->stripDeclaration($this->saveXml());
    }

    /**
     * Removes XML declaration from a string
     *
     * @param  string $xml
     * @return string
     */
    public function stripDeclaration($xml)
    {
        return preg_replace('/<\?xml version="1.0"( encoding="[^\"]*")?\?>\n/u', '', $xml);
    }

    /**
     * Start XML element
     *
     * @param string $name XML element name
     */
    abstract protected function _openElement($name);

    /**
     * Write XML text data into the currently opened XML element
     *
     * @param string $text
     */
    abstract protected function _writeTextData($text);

    /**
     * End XML element
     * 
     * @param string $name
     */
    abstract protected function _closeElement($name);
}
