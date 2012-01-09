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
 * @subpackage Media
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\Media;

use Zend\GData\Media;

/**
 * Represents the Gdata flavor of an Atom entry
 *
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\Media
 * @uses       \Zend\GData\Media\Extension\MediaGroup
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Media
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Entry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\Media\Entry';

    /**
     * media:group element
     *
     * @var \Zend\GData\Media\Extension\MediaGroup
     */
    protected $_mediaGroup = null;

    /**
     * Create a new instance.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Media::$namespaces);
        parent::__construct($element);
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
        if ($this->_mediaGroup != null) {
            $element->appendChild($this->_mediaGroup->getDOM($element->ownerDocument));
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
        case $this->lookupNamespace('media') . ':' . 'group':
            $mediaGroup = new Extension\MediaGroup();
            $mediaGroup->transferFromDOM($child);
            $this->_mediaGroup = $mediaGroup;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Returns the entry's mediaGroup object.
     *
     * @return \Zend\GData\Media\Extension\MediaGroup
    */
    public function getMediaGroup()
    {
        return $this->_mediaGroup;
    }

    /**
     * Sets the entry's mediaGroup object.
     *
     * @param \Zend\GData\Media\Extension\MediaGroup $mediaGroup
     * @return \Zend\GData\Media\Entry Provides a fluent interface
     */
    public function setMediaGroup($mediaGroup)
    {
        $this->_mediaGroup = $mediaGroup;
        return $this;
    }


}
