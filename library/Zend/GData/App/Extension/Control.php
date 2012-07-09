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
 * Represents the app:control element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
class Control extends AbstractExtension
{

    protected $_rootNamespace = 'app';
    protected $_rootElement = 'control';
    protected $_draft = null;

    public function __construct($draft = null)
    {
        parent::__construct();
        $this->_draft = $draft;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_draft != null) {
            $element->appendChild($this->_draft->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('app') . ':' . 'draft':
            $draft = new Draft();
            $draft->transferFromDOM($child);
            $this->_draft = $draft;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * @return \Zend\GData\App\Extension\Draft
     */
    public function getDraft()
    {
        return $this->_draft;
    }

    /**
     * @param \Zend\GData\App\Extension\Draft $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setDraft($value)
    {
        $this->_draft = $value;
        return $this;
    }

}
