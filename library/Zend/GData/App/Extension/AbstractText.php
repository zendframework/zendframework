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
 * Abstract class for data models that require only text and type-- such as:
 * title, summary, etc.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
abstract class AbstractText extends AbstractExtension
{

    protected $_rootElement = null;
    protected $_type = 'text';

    public function __construct($text = null, $type = 'text')
    {
        parent::__construct();
        $this->_text = $text;
        $this->_type = $type;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_type !== null) {
            $element->setAttribute('type', $this->_type);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'type':
            $this->_type = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /*
     * @return Zend\GData\App\Extension\Type
     */
    public function getType()
    {
        return $this->_type;
    }

    /*
     * @param string $value
     * @return Zend\GData\App\Extension\AbstractText Provides a fluent interface
     */
    public function setType($value)
    {
        $this->_type = $value;
        return $this;
    }

}
