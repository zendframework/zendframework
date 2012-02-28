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
 * @package    Zend_Config
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config\Reader;

use XMLReader,
    Zend\Config\Reader,    
    Zend\Config\Exception;

/**
 * XML config reader.
 *
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Xml implements Reader
{
    /**
     * XML Reader instance.
     *
     * @var XMLReader
     */
    protected $reader;

    /**
     * Directory of the file to process.
     *
     * @var string
     */
    protected $directory;

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
     * fromFile(): defined by Reader interface.
     *
     * @see    Reader::fromFile()
     * @param  string $filename
     * @return array
     */
    public function fromFile($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception\RuntimeException("The file $filename doesn't exists.");
        }
        $this->reader = new XMLReader();

        $this->reader->open($filename, null, LIBXML_XINCLUDE);   

        $this->directory = dirname($filename);

        set_error_handler(
            function($error, $message = '', $file = '', $line = 0) use ($filename) {
                throw new Exception\RuntimeException(sprintf(
                    'Error reading XML file "%s": %s',
                    $filename, $message
                ), $error);
            }, E_WARNING
        );
        $return = $this->process();
        restore_error_handler();
        
        return $return;
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    Reader::fromString()
     * @param  string $string
     * @return array
     */
    public function fromString($string)
    {
        if (empty($string)) {
            return array();
        }
        $this->reader = new XMLReader();
        
        $this->reader->xml($string, null, LIBXML_XINCLUDE);

        $this->directory = null;

        set_error_handler(
            function($error, $message = '', $file = '', $line = 0) {
                throw new Exception\RuntimeException(sprintf(
                    'Error reading XML string: %s',
                    $message
                ), $error);
            }, E_WARNING
        );
        $return = $this->process();
        restore_error_handler();
        
        return $return;
    }

    /**
     * Process data from the created XMLReader.
     *
     * @return array
     */
    protected function process()
    {
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
                $name       = $this->reader->name;

                if ($this->reader->isEmptyElement) {
                    $child = array();
                } else {
                    $child = $this->processNextElement();
                }

                if ($attributes) {
                    if (!is_array($child)) {
                        $child = array();
                    }

                    $child = array_merge($child, $attributes);
                }

                if (isset($children[$name])) {
                    if (!is_array($children[$name]) || !$children[$name]) {
                        $children[$name] = array($children[$name]);
                    }

                    $children[$name][] = $child;
                } else {
                    $children[$name] = $child;
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
        $attributes = array();

        if ($this->reader->hasAttributes) {
            while ($this->reader->moveToNextAttribute()) {
                $attributes[$this->reader->localName] = $this->reader->value;
            }

            $this->reader->moveToElement();
        }

        return $attributes;
    }
}
