<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Media\Extension;

/**
 * Represents the media:copyright element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Media
 */
class MediaCopyright extends \Zend\GData\Extension
{

    protected $_rootElement = 'copyright';
    protected $_rootNamespace = 'media';

    /**
     * @var string
     */
    protected $_url = null;

    /**
     * @param string $text
     * @param string $url
     */
    public function __construct($text = null, $url = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Media::$namespaces);
        parent::__construct();
        $this->_text = $text;
        $this->_url = $url;
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_url !== null) {
            $element->setAttribute('url', $this->_url);
        }
        return $element;
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'url':
            $this->_url = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $value
     * @return \Zend\GData\Media\Extension\MediaCopyright Provides a fluent interface
     */
    public function setUrl($value)
    {
        $this->_url = $value;
        return $this;
    }

}
