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
 * @subpackage YouTube
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\YouTube;

use Zend\GData\YouTube;

/**
 * Represents the YouTube video playlist flavor of an Atom entry
 *
 * @uses       \Zend\GData\YouTube\Extension\Position
 * @uses       \Zend\GData\YouTube
 * @uses       \Zend\GData\YouTube\VideoEntry
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PlaylistVideoEntry extends VideoEntry
{

    protected $_entryClassName = 'Zend\GData\YouTube\PlaylistVideoEntry';

    /**
     * Position of the entry in the feed, as specified by the user
     *
     * @var \Zend\GData\YouTube\Extension\Position
     */
    protected $_position = null;

    /**
     * Creates a Playlist video entry, representing an individual video
     * in a list of videos contained within a specific playlist
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(YouTube::$namespaces);
        parent::__construct($element);
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
        if ($this->_position !== null) {
            $element->appendChild($this->_position->getDOM($element->ownerDocument));
        }
        return $element;
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('yt') . ':' . 'position':
            $position = new Extension\Position();
            $position->transferFromDOM($child);
            $this->_position = $position;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }


    /**
     * Sets the array of embedded feeds related to the video
     *
     * @param \Zend\GData\YouTube\Extension\Position $position
     *     The position of the entry in the feed, as specified by the user.
     * @return \Zend\GData\YouTube\PlaylistVideoEntry Provides a fluent interface
     */
    public function setPosition($position = null)
    {
        $this->_position = $position;
        return $this;
    }

    /**
     * Returns the position of the entry in the feed, as specified by the user
     *
     * @return \Zend\GData\YouTube\Extension\Position The position
     */
    public function getPosition()
    {
        return $this->_position;
    }

}
