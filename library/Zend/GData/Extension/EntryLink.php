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
 * Represents the gd:entryLink element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 */
class EntryLink extends Extension
{

    protected $_rootElement = 'entryLink';
    protected $_href = null;
    protected $_readOnly = null;
    protected $_rel = null;
    protected $_entry = null;

    public function __construct($href = null, $rel = null,
            $readOnly = null, $entry = null)
    {
        parent::__construct();
        $this->_href = $href;
        $this->_readOnly = $readOnly;
        $this->_rel = $rel;
        $this->_entry = $entry;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_href !== null) {
            $element->setAttribute('href', $this->_href);
        }
        if ($this->_readOnly !== null) {
            $element->setAttribute('readOnly', ($this->_readOnly ? "true" : "false"));
        }
        if ($this->_rel !== null) {
            $element->setAttribute('rel', $this->_rel);
        }
        if ($this->_entry !== null) {
            $element->appendChild($this->_entry->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case $this->lookupNamespace('atom') . ':' . 'entry';
                $entry = new \Zend\GData\Entry();
                $entry->transferFromDOM($child);
                $this->_entry = $entry;
                break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'href':
            $this->_href = $attribute->nodeValue;
            break;
        case 'readOnly':
            if ($attribute->nodeValue == "true") {
                $this->_readOnly = true;
            } elseif ($attribute->nodeValue == "false") {
                $this->_readOnly = false;
            } else {
                throw new \Zend\GData\App\InvalidArgumentException("Expected 'true' or 'false' for gCal:selected#value.");
            }
            break;
        case 'rel':
            $this->_rel = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_href;
    }

    public function setHref($value)
    {
        $this->_href = $value;
        return $this;
    }

    public function getReadOnly()
    {
        return $this->_readOnly;
    }

    public function setReadOnly($value)
    {
        $this->_readOnly = $value;
        return $this;
    }

    public function getRel()
    {
        return $this->_rel;
    }

    public function setRel($value)
    {
        $this->_rel = $value;
        return $this;
    }

    public function getEntry()
    {
        return $this->_entry;
    }

    public function setEntry($value)
    {
        $this->_entry = $value;
        return $this;
    }

}
