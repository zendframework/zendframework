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
 * Represents a Calendar entry in the Calendar data API meta feed of a user's
 * calendars.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Calendar
 */
class ListEntry extends \Zend\GData\Entry
{

    protected $_color = null;
    protected $_accessLevel = null;
    protected $_hidden = null;
    protected $_selected = null;
    protected $_timezone = null;
    protected $_where = array();

    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Calendar::$namespaces);
        parent::__construct($element);
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_accessLevel != null) {
            $element->appendChild($this->_accessLevel->getDOM($element->ownerDocument));
        }
        if ($this->_color != null) {
            $element->appendChild($this->_color->getDOM($element->ownerDocument));
        }
        if ($this->_hidden != null) {
            $element->appendChild($this->_hidden->getDOM($element->ownerDocument));
        }
        if ($this->_selected != null) {
            $element->appendChild($this->_selected->getDOM($element->ownerDocument));
        }
        if ($this->_timezone != null) {
            $element->appendChild($this->_timezone->getDOM($element->ownerDocument));
        }
        if ($this->_where != null) {
            foreach ($this->_where as $where) {
                $element->appendChild($where->getDOM($element->ownerDocument));
            }
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gCal') . ':' . 'accesslevel';
            $accessLevel = new Extension\AccessLevel();
            $accessLevel->transferFromDOM($child);
            $this->_accessLevel = $accessLevel;
            break;
        case $this->lookupNamespace('gCal') . ':' . 'color';
            $color = new Extension\Color();
            $color->transferFromDOM($child);
            $this->_color = $color;
            break;
        case $this->lookupNamespace('gCal') . ':' . 'hidden';
            $hidden = new Extension\Hidden();
            $hidden->transferFromDOM($child);
            $this->_hidden = $hidden;
            break;
        case $this->lookupNamespace('gCal') . ':' . 'selected';
            $selected = new Extension\Selected();
            $selected->transferFromDOM($child);
            $this->_selected = $selected;
            break;
        case $this->lookupNamespace('gCal') . ':' . 'timezone';
            $timezone = new Extension\Timezone();
            $timezone->transferFromDOM($child);
            $this->_timezone = $timezone;
            break;
        case $this->lookupNamespace('gd') . ':' . 'where';
            $where = new \Zend\GData\Extension\Where();
            $where->transferFromDOM($child);
            $this->_where[] = $where;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    public function getAccessLevel()
    {
        return $this->_accessLevel;
    }

    /**
     * @param Extension\AccessLevel $value
     * @return ListEntry Provides a fluent interface
     */
    public function setAccessLevel($value)
    {
        $this->_accessLevel = $value;
        return $this;
    }
    public function getColor()
    {
        return $this->_color;
    }

    /**
     * @param Extension\Color $value
     * @return ListEntry Provides a fluent interface
     */
    public function setColor($value)
    {
        $this->_color = $value;
        return $this;
    }

    public function getHidden()
    {
        return $this->_hidden;
    }

    /**
     * @param Extension\Hidden $value
     * @return ListEntry Provides a fluent interface
     */
    public function setHidden($value)
    {
        $this->_hidden = $value;
        return $this;
    }

    public function getSelected()
    {
        return $this->_selected;
    }

    /**
     * @param Extension\Selected $value
     * @return ListEntry Provides a fluent interface
     */
    public function setSelected($value)
    {
        $this->_selected = $value;
        return $this;
    }

    public function getTimezone()
    {
        return $this->_timezone;
    }

    /**
     * @param Extension\Timezone $value
     * @return ListEntry Provides a fluent interface
     */
    public function setTimezone($value)
    {
        $this->_timezone = $value;
        return $this;
    }

    public function getWhere()
    {
        return $this->_where;
    }

    /**
     * @param \Zend\GData\Extension\Where $value
     * @return ListEntry Provides a fluent interface
     */
    public function setWhere($value)
    {
        $this->_where = $value;
        return $this;
    }

}
