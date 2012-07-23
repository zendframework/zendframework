<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Geo\Extension;

/**
 * Represents the georss:where element used by the Gdata Geo extensions.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Geo
 */
class GeoRssWhere extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'georss';
    protected $_rootElement = 'where';

    /**
     * The point location for this geo element
     *
     * @var \Zend\GData\Geo\Extension\GmlPoint
     */
    protected $_point = null;

    /**
     * Create a new instance.
     *
     * @param \Zend\GData\Geo\Extension\GmlPoint $point (optional) Point to which
     *          object should be initialized.
     */
    public function __construct($point = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Geo::$namespaces);
        parent::__construct();
        $this->setPoint($point);
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     *          child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_point !== null) {
            $element->appendChild($this->_point->getDOM($element->ownerDocument));
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
            case $this->lookupNamespace('gml') . ':' . 'Point';
                $point = new GmlPoint();
                $point->transferFromDOM($child);
                $this->_point = $point;
                break;
        }
    }

    /**
     * Get the value for this element's point attribute.
     *
     * @see setPoint
     * @return \Zend\GData\Geo\Extension\GmlPoint The requested attribute.
     */
    public function getPoint()
    {
        return $this->_point;
    }

    /**
     * Set the value for this element's point attribute.
     *
     * @param \Zend\GData\Geo\Extension\GmlPoint $value The desired value for this attribute.
     * @return \Zend\GData\Geo\Extension\GeoRssWhere Provides a fluent interface
     */
    public function setPoint($value)
    {
        $this->_point = $value;
        return $this;
    }

}
