<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\YouTube\Extension;

/**
 * Represents the media:content element of Media RSS.
 * Represents media objects.  Multiple media objects representing
 * the same content can be represented using a
 * media:group (Zend_Gdata_Media_Extension_MediaGroup) element.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */
class MediaContent extends \Zend\GData\Media\Extension\MediaContent
{
    protected $_rootElement = 'content';
    protected $_rootNamespace = 'media';

    /*
     * Format of the video
     * Optional.
     *
     * @var int
     */
    protected $_format = null;


    public function __construct()
    {
        $this->registerAllNamespaces(\Zend\GData\YouTube::$namespaces);
        parent::__construct();
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
        if ($this->_format!= null) {
            $element->setAttributeNS($this->lookupNamespace('yt'), 'yt:format', $this->_format);
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
        $absoluteAttrName = $attribute->namespaceURI . ':' . $attribute->localName;
        if ($absoluteAttrName == $this->lookupNamespace('yt') . ':' . 'format') {
            $this->_format = $attribute->nodeValue;
        } else {
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Returns the format of the media
     * Optional.
     *
     * @return int  The format of the media
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Sets the format of the media
     *
     * @param int $value    Format of the media
     * @return \Zend\GData\YouTube\Extension\MediaContent  Provides a fluent interface
     *
     */
    public function setFormat($value)
    {
        $this->_format = $value;
        return $this;
    }

}
