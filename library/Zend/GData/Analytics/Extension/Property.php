<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Analytics\Extension;

use Zend\GData;

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Analytics
 */
class Property extends GData\Extension
{
    protected $_rootNamespace = 'ga';
    protected $_rootElement = 'property';
    protected $_value = null;
    protected $_name = null;

    /**
     * @param string $value (optional) The text content of the element.
     * @param string $name (optional)
     */
    public function __construct($value = null, $name = null)
    {
        $this->registerAllNamespaces(GData\Analytics::$namespaces);
        parent::__construct();
        $this->_value = $value;
        $this->_name = $name;
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
            case 'name':
                $this->_name = substr($attribute->nodeValue, 3);
                break;
            case 'value':
                $this->_value = $attribute->nodeValue;
                break;
            default:
                parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Get the value for this element's value attribute.
     *
     * @return string The value associated with this attribute.
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the value for this element's value attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return Property The element being modified.
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return Property
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Magic toString method allows using this directly via echo
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
