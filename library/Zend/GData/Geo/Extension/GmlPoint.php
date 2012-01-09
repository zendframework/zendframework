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
 * Represents the gml:point element used by the Gdata Geo extensions.
 *
 * @uses       \Zend\GData\Extension
 * @uses       \Zend\GData\Geo
 * @uses       \Zend\GData\Geo\Extension\GmlPos
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Geo
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GmlPoint extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gml';
    protected $_rootElement = 'Point';

    /**
     * The position represented by this GmlPoint
     *
     * @var \Zend\GData\Geo\Extension\GmlPos
     */
    protected $_pos = null;

    /**
     * Create a new instance.
     *
     * @param \Zend\GData\Geo\Extension\GmlPos $pos (optional) Pos to which this
     *          object should be initialized.
     */
    public function __construct($pos = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Geo::$namespaces);
        parent::__construct();
        $this->setPos($pos);
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
        if ($this->_pos !== null) {
            $element->appendChild($this->_pos->getDOM($element->ownerDocument));
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
            case $this->lookupNamespace('gml') . ':' . 'pos';
                $pos = new GmlPos();
                $pos->transferFromDOM($child);
                $this->_pos = $pos;
                break;
        }
    }

    /**
     * Get the value for this element's pos attribute.
     *
     * @see setPos
     * @return \Zend\GData\Geo\Extension\GmlPos The requested attribute.
     */
    public function getPos()
    {
        return $this->_pos;
    }

    /**
     * Set the value for this element's distance attribute.
     *
     * @param \Zend\GData\Geo\Extension\GmlPos $value The desired value for this attribute
     * @return \Zend\GData\Geo\Extension\GmlPoint Provides a fluent interface
     */
    public function setPos($value)
    {
        $this->_pos = $value;
        return $this;
    }


}
