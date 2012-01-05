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
 * @subpackage Gdata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\Kind;

/**
 * Data model for the Gdata Event "Kind".  Google Calendar has a separate
 * EventEntry class which extends this.
 *
 * @uses       \Zend\GData\App\Extension
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\Extension\Comments
 * @uses       \Zend\GData\Extension\EntryLink
 * @uses       \Zend\GData\Extension\EventStatus
 * @uses       \Zend\GData\Extension\ExtendedProperty
 * @uses       \Zend\GData\Extension\OriginalEvent
 * @uses       \Zend\GData\Extension\Recurrence
 * @uses       \Zend\GData\Extension\RecurrenceException
 * @uses       \Zend\GData\Extension\Transparency
 * @uses       \Zend\GData\Extension\Visibility
 * @uses       \Zend\GData\Extension\When
 * @uses       \Zend\GData\Extension\Where
 * @uses       \Zend\GData\Extension\Who
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class EventEntry extends \Zend\GData\Entry
{
    protected $_who = array();
    protected $_when = array();
    protected $_where = array();
    protected $_recurrence = null;
    protected $_eventStatus = null;
    protected $_comments = null;
    protected $_transparency = null;
    protected $_visibility = null;
    protected $_recurrenceException = array();
    protected $_extendedProperty = array();
    protected $_originalEvent = null;
    protected $_entryLink = null;

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_who != null) {
            foreach ($this->_who as $who) {
                $element->appendChild($who->getDOM($element->ownerDocument));
            }
        }
        if ($this->_when != null) {
            foreach ($this->_when as $when) {
                $element->appendChild($when->getDOM($element->ownerDocument));
            }
        }
        if ($this->_where != null) {
            foreach ($this->_where as $where) {
                $element->appendChild($where->getDOM($element->ownerDocument));
            }
        }
        if ($this->_recurrenceException != null) {
            foreach ($this->_recurrenceException as $recurrenceException) {
                $element->appendChild($recurrenceException->getDOM($element->ownerDocument));
            }
        }
        if ($this->_extendedProperty != null) {
            foreach ($this->_extendedProperty as $extProp) {
                $element->appendChild($extProp->getDOM($element->ownerDocument));
            }
        }

        if ($this->_recurrence != null) {
            $element->appendChild($this->_recurrence->getDOM($element->ownerDocument));
        }
        if ($this->_eventStatus != null) {
            $element->appendChild($this->_eventStatus->getDOM($element->ownerDocument));
        }
        if ($this->_comments != null) {
            $element->appendChild($this->_comments->getDOM($element->ownerDocument));
        }
        if ($this->_transparency != null) {
            $element->appendChild($this->_transparency->getDOM($element->ownerDocument));
        }
        if ($this->_visibility != null) {
            $element->appendChild($this->_visibility->getDOM($element->ownerDocument));
        }
        if ($this->_originalEvent != null) {
            $element->appendChild($this->_originalEvent->getDOM($element->ownerDocument));
        }
        if ($this->_entryLink != null) {
            $element->appendChild($this->_entryLink->getDOM($element->ownerDocument));
        }


        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gd') . ':' . 'where';
            $where = new \Zend\GData\Extension\Where();
            $where->transferFromDOM($child);
            $this->_where[] = $where;
            break;
        case $this->lookupNamespace('gd') . ':' . 'when';
            $when = new \Zend\GData\Extension\When();
            $when->transferFromDOM($child);
            $this->_when[] = $when;
            break;
        case $this->lookupNamespace('gd') . ':' . 'who';
            $who = new \Zend\GData\Extension\Who();
            $who ->transferFromDOM($child);
            $this->_who[] = $who;
            break;
        case $this->lookupNamespace('gd') . ':' . 'recurrence';
            $recurrence = new \Zend\GData\Extension\Recurrence();
            $recurrence->transferFromDOM($child);
            $this->_recurrence = $recurrence;
            break;
        case $this->lookupNamespace('gd') . ':' . 'eventStatus';
            $eventStatus = new \Zend\GData\Extension\EventStatus();
            $eventStatus->transferFromDOM($child);
            $this->_eventStatus = $eventStatus;
            break;
        case $this->lookupNamespace('gd') . ':' . 'comments';
            $comments = new \Zend\GData\Extension\Comments();
            $comments->transferFromDOM($child);
            $this->_comments = $comments;
            break;
        case $this->lookupNamespace('gd') . ':' . 'transparency';
            $transparency = new \Zend\GData\Extension\Transparency();
            $transparency ->transferFromDOM($child);
            $this->_transparency = $transparency;
            break;
        case $this->lookupNamespace('gd') . ':' . 'visibility';
            $visiblity = new \Zend\GData\Extension\Visibility();
            $visiblity ->transferFromDOM($child);
            $this->_visibility = $visiblity;
            break;
        case $this->lookupNamespace('gd') . ':' . 'recurrenceException';
            $recurrenceException = new \Zend\GData\Extension\RecurrenceException();
            $recurrenceException ->transferFromDOM($child);
            $this->_recurrenceException[] = $recurrenceException;
            break;
        case $this->lookupNamespace('gd') . ':' . 'originalEvent';
            $originalEvent = new \Zend\GData\Extension\OriginalEvent();
            $originalEvent ->transferFromDOM($child);
            $this->_originalEvent = $originalEvent;
            break;
        case $this->lookupNamespace('gd') . ':' . 'extendedProperty';
            $extProp = new \Zend\GData\Extension\ExtendedProperty();
            $extProp->transferFromDOM($child);
            $this->_extendedProperty[] = $extProp;
            break;
        case $this->lookupNamespace('gd') . ':' . 'entryLink':
            $entryLink = new \Zend\GData\Extension\EntryLink();
            $entryLink->transferFromDOM($child);
            $this->_entryLink = $entryLink;
            break;

        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    public function getWhen()
    {
        return $this->_when;
    }

    /**
     * @param array $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setWhen($value)
    {
        $this->_when = $value;
        return $this;
    }

    public function getWhere()
    {
        return $this->_where;
    }

    /**
     * @param array $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setWhere($value)
    {
        $this->_where = $value;
        return $this;
    }

    public function getWho()
    {
        return $this->_who;
    }

    /**
     * @param array $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setWho($value)
    {
        $this->_who = $value;
        return $this;
    }

    public function getRecurrence()
    {
        return $this->_recurrence;
    }

    /**
     * @param array $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setRecurrence($value)
    {
        $this->_recurrence = $value;
        return $this;
    }

    public function getEventStatus()
    {
        return $this->_eventStatus;
    }

    /**
     * @param array $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setEventStatus($value)
    {
        $this->_eventStatus = $value;
        return $this;
    }

    public function getComments()
    {
        return $this->_comments;
    }

    /**
     * @param array $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setComments($value)
    {
        $this->_comments = $value;
        return $this;
    }

    public function getTransparency()
    {
        return $this->_transparency;
    }

    /**
     * @param Zend_Gdata_Transparency $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setTransparency($value)
    {
        $this->_transparency = $value;
        return $this;
    }

    public function getVisibility()
    {
        return $this->_visibility;
    }

    /**
     * @param Zend_Gdata_Visibility $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setVisibility($value)
    {
        $this->_visibility = $value;
        return $this;
    }

    public function getRecurrenceExcption()
    {
        return $this->_recurrenceException;
    }

    /**
     * @param array $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setRecurrenceException($value)
    {
        $this->_recurrenceException = $value;
        return $this;
    }

    public function getExtendedProperty()
    {
        return $this->_extendedProperty;
    }

    /**
     * @param array $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setExtendedProperty($value)
    {
        $this->_extendedProperty = $value;
        return $this;
    }

    public function getOriginalEvent()
    {
        return $this->_originalEvent;
    }

    /**
     * @param \Zend\GData\Extension\OriginalEvent $value
     * @return \Zend\GData\Kind\EventEntry Provides a fluent interface
     */
    public function setOriginalEvent($value)
    {
        $this->_originalEvent = $value;
        return $this;
    }

    /**
     * Get this entry's EntryLink element.
     *
     * @return \Zend\GData\Extension\EntryLink The requested entry.
     */
    public function getEntryLink()
    {
        return $this->_entryLink;
    }

    /**
     * Set the child's EntryLink element.
     *
     * @param \Zend\GData\Extension\EntryLink $value The desired value for this attribute.
     * @return \Zend\GData\Extension\Who The element being modified.
     */
    public function setEntryLink($value)
    {
        $this->_entryLink = $value;
        return $this;
    }


}
