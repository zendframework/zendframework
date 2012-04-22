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
 * @package    Zend_I18n_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\Translator\Loader;

use Zend\I18n\Translator\Exception;

/**
 * Loader interface.
 *
 * @package    Zend_I18n_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Gettext implements LoaderInterface
{
    /**
     * Current file pointer.
     * 
     * @var resource
     */
    protected $_file;
    
    /**
     * Whether the current file is little endian.
     * 
     * @var boolean
     */
    protected $_littleEndian;
    
    /**
     * load(): defined by LoaderInterface.
     * 
     * @see    LoaderInterface::load()
     * @param  string $filename
     * @return array 
     */
    public function load($filename)
    {
        $translations = array();
        $this->file   = @fopen($filename, 'rb');
        
        if (!$this->file) {
            throw new Exception\InvalidArgumentException(
                sprintf('Could not open file %s for reading', $filename)
            );
        }
        
        // Verify magic number
        $magic = fread($this->file, 4);
        
        if ($magic === "\x95\x04\x12\xde") {
            $this->littleEndian = true;
        } elseif ($magic === "\xde\x12\x04\x95") {
            $this->littleEndian = false;
        } else {
            fclose($this->file);
            throw new Exception\InvalidArgumentException(
                sprintf('%s is not a valid gettext file', $filename)
            );
        }
        
        // Verify major revision (only 0 and 1 supported)
        $majorRevision = ($this->readInteger() >> 16);
        
        if ($majorRevision !== 0 && $majorRevision !== 1) {
            fclose($this->file);
            throw new Exception\InvalidArgumentException(
                sprintf('%s has an unknown major revision', $filename)
            );
        }
        
        // Gather main information
        $numStrings                   = $this->readInteger();
        $originalStringTableOffset    = $this->readInteger();
        $translationStringTableOffset = $this->readInteger();
        
        // Usually there follow size and offset of the hash table, but we have
        // no need for it, so we skip them.
        fseek($originalStringTableOffset);
        $originalStringTable = $this->readIntegerList(2 * $numStrings);
        
        fseek($translationStringTableOffset);
        $translationStringTable = $this->readIntegerList(2 * $numStrings);
        
        // Read in all translations
        for ($current = 0; $current < $numStrings; $current++) {
            $originalStringSize      = $originalStringTable[$current * 2 + 1];
            $originalStringOffset    = $originalStringTable[$current * 2 + 2];
            $translationStringSize   = $translationStringTable[$current * 2 + 1];
            $translationStringOffset = $translationStringTable[$current * 2 + 2];
            
            if ($originalStringSize > 0) {
                fseek($originalStringOffset);
                $originalString = explode("\0", fread($this->file, $originalStringSize));
            } else {
                $originalString = array('');
            }

            if ($translationStringSize > 0) {
                fseek($translationStringOffset);
                $translationString = explode("\0", fread($this->file, $translationStringSize));
                
                if (count($originalString) > 1 && count($translationString) > 1) {
                    $translations[$original[0]] = $translationString;
                    
                    array_shift($originalString);
                    
                    foreach ($originalString as $string) {
                        $translations[$string] = '';
                    }
                } else {
                    $translations[$original[0]] = $translationString[0];
                }
            }
        }
        
        // Read header entries
        if (isset($translations[''])) {
            $rawHeaders = explode("\n", $translations['']);
            $headers    = array();
            
            foreach ($rawHeaders as $header => $content) {
                if (strtolower($header) === 'plural-forms') {
                    $headers['plural_forms'] = $content;
                }
            }
            
            $translations[''] = $headers;
        }
        
        fclose($this->file);
        
        return $translations;
    }
    
    /**
     * Read a single integer from the current file.
     * 
     * @return integer
     */
    protected function readInteger()
    {
        if ($this->littleEndian) {
            $result = unpack('Vint', fread($this->file, 4));
        } else {
            $result = unpack('Nint', fread($this->file, 4));
        }
        
        return $result['int'];
    }
    
    /**
     * Read an integer from the current file.
     * 
     * @param  integer $num
     * @return integer
     */
    protected function readIntegerList($num)
    {
        if ($this->littleEndian) {
            return unpack('V' . $num, fread($this->file, 4 * $num));
        } else {
            return unpack('N' . $num, fread($this->file, 4 * $num));
        }
    }
}
