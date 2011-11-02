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
 * @category  Zend
 * @package   Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config\Reader;

use \XMLReader,
    \Zend\Config\Exception;

/**
 * XML config reader.
 *
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Xml extends AbstractReader
{
    /**
     * XML namespace for ZF-related tags and attributes.
     */
    const XML_NAMESPACE = 'http://framework.zend.com/xml/zend-config-xml/1.0/';

    /**
     * XML Reader instance.
     * 
     * @var XMLReader
     */
    protected $reader;

    /**
     * Nodes to handle as plain text.
     * 
     * @var array
     */
    protected $textNodes = array(
        XMLReader::TEXT, XMLReader::CDATA, XMLReader::WHITESPACE,
        XMLReader::SIGNIFICANT_WHITESPACE
    );

    /**
     * processFile(): defined by Reader interface.
     * 
     * @see    Reader::processFile()
     * @param  string $filename
     * @return Config
     */
    protected function processFile($filename)
    {
        $this->reader = new XMLReader();
        $this->reader->open($filename, null, LIBXML_XINCLUDE);
        
        return $this->process();
    }
    
    /**
     * processString(): defined by Reader interface.
     * 
     * @see    Reader::processString()
     * @param  string $data
     * @return Config
     */
    protected function processString($data)
    {
        $this->reader = new XMLReader();
        $this->reader->xml($data, null, LIBXML_XINCLUDE);

        return $this->process();
    }
    
    /**
     * Process data from the created XMLReader.
     * 
     * @return Config
     */
    protected function process()
    {
        $this->extends = array();
        $this->depth   = 0;
        
        return $this->processNextElement();
    }
    
    /**
     * Process the next inner element.
     * 
     * @return mixed
     */
    protected function processNextElement()
    {
        $children = array();
        $text     = '';

        while ($this->reader->read()) {
            if ($this->reader->nodeType === XMLReader::ELEMENT) {               
                if ($this->reader->depth === 0) {
                    return $this->processNextElement();
                }

                $attributes = $this->getAttributes();
                $depth      = $this->reader->depth;
                $name       = $this->reader->name;

                if ($depth === 1 && isset($attributes['zf']['extends'])) {
                    $this->extends[$name] = $attributes['zf']['extends'];
                }
                
                if ($this->reader->namespaceURI === self::XML_NAMESPACE) {
                    switch ($this->reader->localName) {
                        case 'const':
                            if (!isset($attributes['default']['name'])) {
                                throw new Exception\RuntimeException('Misssing "name" attribute in "zf:const" node');
                            }
    
                            $constantName = $attributes['default']['name'];
    
                            if (!defined($constantName)) {
                                throw new Exception\RuntimeException(sprintf('Constant with name "%s" was not defined', $constantName));
                            }
        
                            $text .= constant($constantName);
                            break;
    
                        default:
                            throw new Exception\RuntimeException(sprintf('Unknown zf:node with name "%s" found', $name));
                    }
                } else {
                    if (isset($attributes['zf']['value'])) {
                        $children[$name] = $attributes['zf']['value'];
                    } else {
                        if ($this->reader->isEmptyElement) {
                            $child = array();
                        } else {
                            $child = $this->processNextElement();
                        }
                        
                        if ($attributes['default']) {
                            if (!is_array($child)) {
                                $child = array();
                            }
                            
                            $child = array_merge($child, $attributes['default']);
                        }
        
                        if (isset($children[$name])) {
                            if (!is_array($children[$name]) || !$children[$name]) {
                                $children[$name] = array($children[$name]);
                            }
                            
                            $children[$name][] = $child;
                        } else {
                            $children[$name] = $child;
                        }
                    }
                }
            } elseif ($this->reader->nodeType === XMLReader::END_ELEMENT) {
                break;
            } elseif (in_array($this->reader->nodeType, $this->textNodes)) {
                $text .= $this->reader->value;
            }
        }

        return $children ?: $text;
    }

    /**
     * Get all attributes on the current node.
     * 
     * @return array
     */
    protected function getAttributes()
    {
        $attributes = array('default' => array(), 'zf' => array());
        
        if ($this->reader->hasAttributes) {       
            while ($this->reader->moveToNextAttribute()) {
                if ($this->reader->namespaceURI === self::XML_NAMESPACE) {
                    $attributes['zf'][$this->reader->localName] = $this->reader->value;
                } else {
                    $attributes['default'][$this->reader->localName] = $this->reader->value;   
                }
            }
            
            $this->reader->moveToElement();
        }
        
        return $attributes;
    }
}
