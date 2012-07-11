<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Extension;

use Zend\GData\Extension;

/**
 * Represents the gd:when element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 */
class When extends Extension
{

    protected $_rootElement = 'when';
    protected $_reminders = array();
    protected $_startTime = null;
    protected $_valueString = null;
    protected $_endTime = null;

    public function __construct($startTime = null, $endTime = null,
            $valueString = null, $reminders = null)
    {
        parent::__construct();
        $this->_startTime = $startTime;
        $this->_endTime = $endTime;
        $this->_valueString = $valueString;
        $this->_reminders = $reminders;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_startTime !== null) {
            $element->setAttribute('startTime', $this->_startTime);
        }
        if ($this->_endTime !== null) {
            $element->setAttribute('endTime', $this->_endTime);
        }
        if ($this->_valueString !== null) {
            $element->setAttribute('valueString', $this->_valueString);
        }
        if ($this->_reminders !== null) {
            foreach ($this->_reminders as $reminder) {
                $element->appendChild(
                        $reminder->getDOM($element->ownerDocument));
            }
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case $this->lookupNamespace('gd') . ':' . 'reminder';
                $reminder = new Reminder();
                $reminder->transferFromDOM($child);
                $this->_reminders[] = $reminder;
                break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
            case 'startTime':
                $this->_startTime = $attribute->nodeValue;
                break;
            case 'endTime':
                $this->_endTime = $attribute->nodeValue;
                break;
            case 'valueString':
                $this->_valueString = $attribute->nodeValue;
                break;
            default:
                parent::takeAttributeFromDOM($attribute);
        }
    }

    public function __toString()
    {
        if ($this->_valueString)
            return $this->_valueString;
        else {
            return 'Starts: ' . $this->getStartTime() . ' ' .
                   'Ends: ' .  $this->getEndTime();
        }
    }

    public function getStartTime()
    {
        return $this->_startTime;
    }

    public function setStartTime($value)
    {
        $this->_startTime = $value;
        return $this;
    }

    public function getEndTime()
    {
        return $this->_endTime;
    }

    public function setEndTime($value)
    {
        $this->_endTime = $value;
        return $this;
    }

    public function getValueString()
    {
        return $this->_valueString;
    }

    public function setValueString($value)
    {
        $this->_valueString = $value;
        return $this;
    }

    public function getReminders()
    {
        return $this->_reminders;
    }

    public function setReminders($value)
    {
        $this->_reminders = $value;
        return $this;
    }

}
