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
 * @subpackage Gapps
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: EmailList.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @namespace
 */
namespace Zend\GData\Docs\Extension;

/**
 * Represents the gAcl:scope element used by the Docs data API.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gapps
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ACLScope extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gAcl';
    protected $_rootElement = 'scope';

    /**
     * The type of the user to share with.  Possible options include,
     * 'group', 'domain', 'defualt', or 'user'.
     *
     * @var string
     */
    protected $_type = null;

    /**
     * The value of the user (email address, domain or null if default (public))
     * @var string
     */
    protected $_value = null;

    /**
     * Constructs a new \Zend\Gdata\Docs\Extension\ACLScope object.
     *
     * @param string $type The type of entity to share with
     * @param string $value the entity to share with (usually email address)
     */
    public function __construct($type = null, $value = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Docs::$namespaces);
        parent::__construct();
        $this->_type = $type;
        $this->_value = $value;
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
    public function getDOM($doc = null, $majorVersion = 3, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_type !== null) {
            $element->setAttribute('type', $this->_type);
        }
        if ($this->_value !== null) {
            $element->setAttribute('value', $this->_value);
        }
        
        return $element;
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
        case 'type':
            $this->_type = $attribute->nodeValue;
            break;
        case 'value':
            $this->_value = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Get the value for this element's type attribute.
     *
     * @see setType
     * @return string The requested attribute.
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set the value for this element's type attribute.
     * @param string $value The desired value for this attribute.
     * @return \Zend\Gdata\Docs\Extension\ACLScope The element being modified.
     */
    public function setType($value)
    {
        $this->_type = $value;
        return $this;
    }

    /**
     * Get the value for this element's value attribute.
     *
     * @see setValue
     * @return string The requested attribute.
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the value for this element's value attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\Gdata\Docs\Extension\ACLScope The element being modified.
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Magic toString method allows using this directly via echo
     * Works best in PHP >= 4.2.0
     *
     * @return string
     */
    public function __toString()
    {
        return "Type Name: " . $this->getType() .
               "\nValue: " . $this->getValue();
    }
}
?>