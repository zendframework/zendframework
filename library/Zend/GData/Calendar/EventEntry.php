<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Calendar;

use Zend\GData\Calendar;

/**
 * Data model class for a Google Calendar Event Entry
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Calendar
 */
class EventEntry extends \Zend\GData\Kind\EventEntry
{

    protected $_entryClassName = 'Zend\GData\Calendar\EventEntry';
    protected $_sendEventNotifications = null;
    protected $_timezone = null;
    protected $_quickadd = null;

    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Calendar::$namespaces);
        parent::__construct($element);
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_sendEventNotifications != null) {
            $element->appendChild($this->_sendEventNotifications->getDOM($element->ownerDocument));
        }
        if ($this->_timezone != null) {
            $element->appendChild($this->_timezone->getDOM($element->ownerDocument));
        }
        if ($this->_quickadd != null) {
            $element->appendChild($this->_quickadd->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;

        switch ($absoluteNodeName) {
            case $this->lookupNamespace('gCal') . ':' . 'sendEventNotifications';
                $sendEventNotifications = new Extension\SendEventNotifications();
                $sendEventNotifications->transferFromDOM($child);
                $this->_sendEventNotifications = $sendEventNotifications;
                break;
            case $this->lookupNamespace('gCal') . ':' . 'timezone';
                $timezone = new Extension\Timezone();
                $timezone->transferFromDOM($child);
                $this->_timezone = $timezone;
                break;
            case $this->lookupNamespace('atom') . ':' . 'link';
                $link = new Extension\Link();
                $link->transferFromDOM($child);
                $this->_link[] = $link;
                break;
            case $this->lookupNamespace('gCal') . ':' . 'quickadd';
                $quickadd = new Extension\QuickAdd();
                $quickadd->transferFromDOM($child);
                $this->_quickadd = $quickadd;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    public function getSendEventNotifications()
    {
        return $this->_sendEventNotifications;
    }

    public function setSendEventNotifications($value)
    {
        $this->_sendEventNotifications = $value;
        return $this;
    }

    public function getTimezone()
    {
        return $this->_timezone;
    }

    /**
     * @param Extension\Timezone $value
     * @return EventEntry Provides a fluent interface
     */
    public function setTimezone($value)
    {
        $this->_timezone = $value;
        return $this;
    }

    public function getQuickAdd()
    {
        return $this->_quickadd;
    }

    /**
     * @param Extension\QuickAdd $value
     * @return ListEntry Provides a fluent interface
     */
    public function setQuickAdd($value)
    {
        $this->_quickadd = $value;
        return $this;
    }

}
