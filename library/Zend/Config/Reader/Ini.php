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

use \Zend\Config\Exception;

/**
 * XML config reader.
 *
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ini extends AbstractReader
{
    /**
     * Separator for nesting levels of configuration data identifiers.
     * 
     * @var string
     */
    protected $nestSeparator = '.';
    
    /**
     * Separator for parent section names.
     * 
     * @var string
     */
    protected $sectionSeparator = ':';

    /**
     * Directory of the file to process.
     * 
     * @var string
     */
    protected $directory;

    /**
     * Set nest separator.
     * 
     * @param  stirng $separator
     * @return self
     */
    public function setNestSeparator($separator)
    {
        $this->nestSeparator = $separator;
        return $this;
    }
    
    /**
     * Get nest separator.
     * 
     * @return string
     */
    public function getNestSeparator()
    {
        return $this->nestSeparator;
    }
    
    /**
     * Set section separator.
     * 
     * @param  string $separator
     * @return self
     */
    public function setSectionSeparator($separator)
    {
        $this->sectionSeparator = $separator;
        return $this;
    }
    
    /**
     * Get section separator.
     * 
     * @return string
     */
    public function getSectionSeparator()
    {
        return $this->sectionSeparator;
    }
    
    /**
     * processFile(): defined by AbstractReader.
     * 
     * @see    AbstractReader::processFile()
     * @param  string $filename
     * @return array
     */
    protected function processFile($filename)
    {
        $this->directory = dirname($filename);
        
        return $this->process(parse_ini_file($filename, true));
    }
    
    /**
     * processString(): defined by AbstractReader.
     * 
     * @see    AbstractReader::processString()
     * @param  string $string
     * @return array
     */
    protected function processString($string)
    {
        $this->directory = __DIR__;
        
        return $this->process(parse_ini_string($string, true));
    }
    
    /**
     * Process data from the parsed ini file.
     * 
     * @param  array $data
     * @return array
     */
    protected function process(array $data)
    {
        $this->extends = array();
        $config        = array();

        foreach ($data as $key => $value) {
            $pieces  = explode($this->sectionSeparator, $key);
            $section = trim($pieces[0]);
            
            switch (count($pieces)) {
                case 2:
                    $this->extends[$section] = trim($pieces[1]);
                    // Break intentionally omitted.
                    
                case 1:
                    if (is_array($value)) {
                        $config[$section] = $this->processSection($value);
                    } else {
                        $config[$section] = $value;
                    }
                    break;
                
                default:
                    throw new Exception\RuntimeException(sprintf('Section "%s" may not extend multiple sexctions', $section));
            }
        }
        
        return $config;
    }
    
    /**
     * Process a section.
     * 
     * @param  array $section 
     * @return array
     */
    protected function processSection(array $section)
    {
        $config = array();
        
        foreach ($section as $key => $value) {
            $this->processKey($key, $value, $config);
        }
        
        return $config;
    }
    
    /**
     * Process a key.
     * 
     * @param  string $key
     * @param  string $value
     * @param  array  $config
     * @return array
     */
    protected function processKey($key, $value, array &$config)
    {
        if (strpos($key, $this->nestSeparator) !== false) {
            $pieces = explode($this->nestSeparator, $key, 2);
            
            if (!strlen($pieces[0]) || !strlen($pieces[1])) {
                throw new Exception\RuntimeException(sprintf('Invalid key "%s"', $key));
            } elseif (!isset($config[$pieces[0]])) {
                if ($pieces[0] === '0' && !empty($config)) {
                    $config = array($pieces[0] => $config);
                } else {
                    $config[$pieces[0]] = array();
                }
            } elseif (!is_array($config[$pieces[0]])) {
                throw new Exception\RuntimeException(sprintf('Cannot create sub-key for "%s", as key already exists', $pieces[0]));
            }
            
            $this->processKey($pieces[1], $value, $config[$pieces[0]]);
        } else {
            if ($key === '@include') {
                $reader  = clone $this;
                $include = $reader->readFile($this->directory . '/' . $value)->toArray();
                $config  = array_replace_recursive($config, $include);
            } else {
                $config[$key] = str_replace('{DIR}', $this->directory, $value);
            }
        }
    }
}
