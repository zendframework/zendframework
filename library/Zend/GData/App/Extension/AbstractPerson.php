<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\App\Extension;

/**
 * Base class for people (currently used by atom:author, atom:contributor)
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
abstract class AbstractPerson extends AbstractExtension
{

    protected $_rootElement = null;
    protected $_name = null;
    protected $_email = null;
    protected $_uri = null;

    public function __construct($name = null, $email = null, $uri = null)
    {
        parent::__construct();
        $this->_name = $name;
        $this->_email = $email;
        $this->_uri = $uri;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_name != null) {
            $element->appendChild($this->_name->getDOM($element->ownerDocument));
        }
        if ($this->_email != null) {
            $element->appendChild($this->_email->getDOM($element->ownerDocument));
        }
        if ($this->_uri != null) {
            $element->appendChild($this->_uri->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('atom') . ':' . 'name':
            $name = new Name();
            $name->transferFromDOM($child);
            $this->_name = $name;
            break;
        case $this->lookupNamespace('atom') . ':' . 'email':
            $email = new Email();
            $email->transferFromDOM($child);
            $this->_email = $email;
            break;
        case $this->lookupNamespace('atom') . ':' . 'uri':
            $uri = new Uri();
            $uri->transferFromDOM($child);
            $this->_uri = $uri;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * @return \Zend\GData\App\Extension\Name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param \Zend\GData\App\Extension\Name $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }

    /**
     * @return \Zend\GData\App\Extension\Email
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param \Zend\GData\App\Extension\Email $value
     * @return \Zend\GData\App\Extension\AbstractPerson Provides a fluent interface
     */
    public function setEmail($value)
    {
        $this->_email = $value;
        return $this;
    }

    /**
     * @return \Zend\GData\App\Extension\Uri
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * @param \Zend\GData\App\Extension\Uri $value
     * @return \Zend\GData\App\Extension\AbstractPerson Provides a fluent interface
     */
    public function setUri($value)
    {
        $this->_uri = $value;
        return $this;
    }

}
