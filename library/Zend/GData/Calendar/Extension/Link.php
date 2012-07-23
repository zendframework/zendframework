<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Calendar\Extension;

/**
 * Specialized Link class for use with Calendar. Enables use of gCal extension elements.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Calendar
 */
class Link extends \Zend\GData\App\Extension\Link
{

    protected $_webContent = null;

    /**
     * Constructs a new Link object.
     * @see Zend_Gdata_App_Extension_Link#__construct
     * @param Webcontent $webContent
     */
    public function __construct($href = null, $rel = null, $type = null,
            $hrefLang = null, $title = null, $length = null, WebContent $webContent = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Calendar::$namespaces);
        parent::__construct($href, $rel, $type, $hrefLang, $title, $length);
        $this->_webContent = $webContent;
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
        if ($this->_webContent != null) {
            $element->appendChild($this->_webContent->getDOM($element->ownerDocument));
        }
        return $element;
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them as members of this entry based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gCal') . ':' . 'webContent':
            $webContent = new WebContent();
            $webContent->transferFromDOM($child);
            $this->_webContent = $webContent;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Get the value for this element's WebContent attribute.
     *
     * @return WebContent The WebContent value
     */
    public function getWebContent()
    {
        return $this->_webContent;
    }

    /**
     * Set the value for this element's WebContent attribute.
     *
     * @param WebContent $value The desired value for this attribute.
     * @return Link The element being modified.  Provides a fluent interface.
     */
    public function setWebContent($value)
    {
        $this->_webContent = $value;
        return $this;
    }


}

