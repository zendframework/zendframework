<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\GApps\Extension;

/**
 * Represents the apps:nickname element used by the Apps data API. This
 * is used to describe a nickname's properties, and is usually contained
 * within instances of Zend_Gdata_GApps_NicknameEntry.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 */
class Nickname extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'apps';
    protected $_rootElement = 'nickname';

    /**
     * The name of the nickname. This name is used as the email address
     * for this nickname.
     *
     * @var string
     */
    protected $_name = null;

    /**
     * Constructs a new Zend_Gdata_GApps_Extension_Nickname object.
     * @param string $name (optional) The nickname being represented.
     */
    public function __construct($name = null)
    {
        $this->registerAllNamespaces(\Zend\GData\GApps::$namespaces);
        parent::__construct();
        $this->_name = $name;
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
        if ($this->_name !== null) {
            $element->setAttribute('name', $this->_name);
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
        case 'name':
            $this->_name = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Get the value for this element's name attribute.
     *
     * @see setName
     * @return string The requested attribute.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the value for this element's name attribute. This name uniquely
     * describes this nickname within the domain. Emails addressed to this
     * name will be delivered to the user who owns this nickname.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\GApps\Extension\Nickname Provides a fluent
     *          interface.
     */
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }

    /**
     * Magic toString method allows using this directly via echo
     * Works best in PHP >= 4.2.0
     */
    public function __toString()
    {
        return $this->getName();
    }

}
