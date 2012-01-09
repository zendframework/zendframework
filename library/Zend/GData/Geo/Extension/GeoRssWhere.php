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
 * @package    Zend_Gdata
 * @subpackage Geo
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\Geo\Extension;

/**
 * Represents the georss:where element used by the Gdata Geo extensions.
 *
 * @uses       \Zend\GData\Extension
 * @uses       \Zend\GData\Geo
 * @uses       \Zend\GData\Geo\Extension\GmlPoint
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Geo
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
