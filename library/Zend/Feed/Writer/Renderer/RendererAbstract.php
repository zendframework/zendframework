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
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
 
require_once 'Zend/Feed/Writer.php';

require_once 'Zend/Version.php';
 
/**
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Renderer_RendererAbstract
{

    protected $_extensions = array();
    
    protected $_container = null;

    protected $_dom = null;

    protected $_ignoreExceptions = false;

    protected $_exceptions = array();
    
    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';
    
    /**
     * Holds the value "atom" or "rss" depending on the feed type set when
     * when last exported.
     *
     * @var string
     */
    protected $_type = null;
    
    protected $_rootElement = null;

    public function __construct($container)
    {
        $this->_container = $container;
        $this->setType($container->getType());
        $this->_loadExtensions();
    }
    
    public function saveXml()
    {
        return $this->getDomDocument()->saveXml();
    }

    public function getDomDocument()
    {
        return $this->_dom;
    }

    public function getElement()
    {
        return $this->getDomDocument()->documentElement;
    }

    public function getDataContainer()
    {
        return $this->_container;
    }
    
    public function setEncoding($enc)
    {
        $this->_encoding = $enc;
    }
    
    public function getEncoding()
    {
        return $this->_encoding;
    }

    public function ignoreExceptions($bool = true)
    {
        if (!is_bool($bool)) {
            require_once 'Zend/Feed/Exception.php';
            throw new Zend_Feed_Exception('Invalid parameter: $bool. Should be TRUE or FALSE (defaults to TRUE if null)');
        }
        $this->_ignoreExceptions = $bool;
    }

    public function getExceptions()
    {
        return $this->_exceptions;
    }
    
    /**
     * Set the current feed type being exported to "rss" or "atom". This allows
     * other objects to gracefully choose whether to execute or not, depending
     * on their appropriateness for the current type, e.g. renderers.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    /**
     * Retrieve the current or last feed type exported.
     *
     * @return string Value will be "rss" or "atom"
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Sets the absolute root element for the XML feed being generated. This
     * helps simplify the appending of namespace declarations, but also ensures
     * namespaces are added to the root element - not scattered across the entire
     * XML file - may assist namespace unsafe parsers and looks pretty ;).
     *
     * @param DOMElement $root
     */
    public function setRootElement(DOMElement $root)
    {
        $this->_rootElement = $root;
    }
    
    /**
     * Retrieve the absolute root element for the XML feed being generated.
     *
     * @return DOMElement
     */
    public function getRootElement()
    {
        return $this->_rootElement;
    }
    
    /**
     * Load extensions from Zend_Feed_Writer
     *
     * @return void
     */
    protected function _loadExtensions()
    {
        Zend_Feed_Writer::registerCoreExtensions();
        $all = Zend_Feed_Writer::getExtensions();
        if (stripos(get_class($this), 'entry')) {
            $exts = $all['entryRenderer'];
        } else {
            $exts = $all['feedRenderer'];
        }
        foreach ($exts as $extension) {
            $className = Zend_Feed_Writer::getPluginLoader()->getClassName($extension);
            $this->_extensions[$extension] = new $className(
                $this->getDataContainer()
            );
            $this->_extensions[$extension]->setEncoding($this->getEncoding());
        }
    }

}
