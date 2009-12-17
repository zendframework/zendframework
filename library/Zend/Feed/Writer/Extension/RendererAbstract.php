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
 * to padraic dot brady at yahoo dot com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Feed_Writer_Entry_Rss
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * @see Zend_Feed_Writer_Extension_RendererInterface
 */
require_once 'Zend/Feed/Writer/Extension/RendererInterface.php';
 
 /**
 * @category   Zend
 * @package    Zend_Feed_Writer_Entry_Rss
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Feed_Writer_Extension_RendererAbstract
implements Zend_Feed_Writer_Extension_RendererInterface
{

    protected $_dom = null;
    
    protected $_entry = null;
    
    protected $_base = null;
    
    protected $_container = null;
    
    protected $_type = null;
    
    protected $_rootElement = null;
    
    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';

    public function __construct($container)
    {
        $this->_container = $container;
    }
    
    public function setEncoding($enc)
    {
        $this->_encoding = $enc;
    }
    
    public function getEncoding()
    {
        return $this->_encoding;
    }
    
    public function setDomDocument(DOMDocument $dom, DOMElement $base)
    {
        $this->_dom = $dom;
        $this->_base = $base;
    }
    
    public function getDataContainer()
    {
        return $this->_container;
    }
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function setRootElement(DOMElement $root)
    {
        $this->_rootElement = $root;
    }
    
    public function getRootElement()
    {
        return $this->_rootElement;
    }
    
    abstract protected function _appendNamespaces();

}
