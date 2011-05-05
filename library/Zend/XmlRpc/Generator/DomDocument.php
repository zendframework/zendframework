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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\XmlRpc\Generator;

/**
 * DOMDocument based implementation of a XML/RPC generator
 *
 * @uses       DOMDocument
 * @uses       Zend\XmlRpc\Generator\AbstractGenerator
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Generator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 */
class DomDocument extends AbstractGenerator
{
    /**
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * @var DOMNode
     */
    protected $_currentElement;

    /**
     * Start XML element
     *
     * @param string $name
     * @return void
     */
    protected function _openElement($name)
    {
        $newElement = $this->_dom->createElement($name);

        $this->_currentElement = $this->_currentElement->appendChild($newElement);
    }

    /**
     * Write XML text data into the currently opened XML element
     *
     * @param string $text
     */
    protected function _writeTextData($text)
    {
        $this->_currentElement->appendChild($this->_dom->createTextNode($text));
    }

    /**
     * Close an previously opened XML element
     *
     * Resets $_currentElement to the next parent node in the hierarchy
     *
     * @param string $name
     * @return void
     */
    protected function _closeElement($name)
    {
        if (isset($this->_currentElement->parentNode)) {
            $this->_currentElement = $this->_currentElement->parentNode;
        }
    }

    /**
     * Save XML as a string
     *
     * @return string
     */
    public function saveXml()
    {
        return $this->_dom->saveXml();
    }

    /**
     * Initializes internal objects
     *
     * @return void
     */
    protected function _init()
    {
        $this->_dom = new \DOMDocument('1.0', $this->_encoding);
        $this->_currentElement = $this->_dom;
    }
}
