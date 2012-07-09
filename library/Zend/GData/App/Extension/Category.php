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
 * Represents the atom:category element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
class Category extends AbstractExtension
{

    protected $_rootElement = 'category';
    protected $_term = null;
    protected $_scheme = null;
    protected $_label = null;

    public function __construct($term = null, $scheme = null, $label=null)
    {
        parent::__construct();
        $this->_term = $term;
        $this->_scheme = $scheme;
        $this->_label = $label;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_term !== null) {
            $element->setAttribute('term', $this->_term);
        }
        if ($this->_scheme !== null) {
            $element->setAttribute('scheme', $this->_scheme);
        }
        if ($this->_label !== null) {
            $element->setAttribute('label', $this->_label);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'term':
            $this->_term = $attribute->nodeValue;
            break;
        case 'scheme':
            $this->_scheme = $attribute->nodeValue;
            break;
        case 'label':
            $this->_label = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return string|null
     */
    public function getTerm()
    {
        return $this->_term;
    }

    /**
     * @param string|null $value
     * @return \Zend\GData\App\Extension\Category Provides a fluent interface
     */
    public function setTerm($value)
    {
        $this->_term = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getScheme()
    {
        return $this->_scheme;
    }

    /**
     * @param string|null $value
     * @return \Zend\GData\App\Extension\Category Provides a fluent interface
     */
    public function setScheme($value)
    {
        $this->_scheme = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @param string|null $value
     * @return \Zend\GData\App\Extension\Category Provides a fluent interface
     */
    public function setLabel($value)
    {
        $this->_label = $value;
        return $this;
    }

}
