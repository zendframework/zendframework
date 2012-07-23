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
 * Represents the atom:content element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
class Content extends AbstractText
{

    protected $_rootElement = 'content';
    protected $_src = null;

    public function __construct($text = null, $type = 'text', $src = null)
    {
        parent::__construct($text, $type);
        $this->_src = $src;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_src !== null) {
            $element->setAttribute('src', $this->_src);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'src':
            $this->_src = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return string
     */
    public function getSrc()
    {
        return $this->_src;
    }

    /**
     * @param string $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setSrc($value)
    {
        $this->_src = $value;
        return $this;
    }

}
