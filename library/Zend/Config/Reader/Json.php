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

use Zend\Config\Exception,
    Zend\Json\Json as JsonFormat;

/**
 * Json config reader.
 *
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Json implements ReaderInterface
{
    /**
     * Directory of the JSON file
     *
     * @var string
     */
    protected $directory;
    /**
     * fromFile(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromFile()
     * @param  string $filename
     * @return array
     */
    public function fromFile($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception\RuntimeException("The file $filename doesn't exists.");
        }
        
        $this->directory = dirname($filename);
        
        try {
            $config = JsonFormat::decode(file_get_contents($filename), JsonFormat::TYPE_ARRAY);
        } catch (\Zend\Json\Exception\RuntimeException $e) {
            throw new Exception\RuntimeException($e->getMessage());
        }    
        
        return $this->process($config);
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromString()
     * @param  string $string
     * @return array
     */
    public function fromString($string)
    {
        if (empty($string)) {
            return array();
        }
        $this->directory = null;
        
        try {
            $config = JsonFormat::decode($string, JsonFormat::TYPE_ARRAY);
        } catch (\Zend\Json\Exception\RuntimeException $e) {
            throw new Exception\RuntimeException($e->getMessage());
        }    
        
        return $this->process($config);
    }
    /**
     * Process the array for @include
     * 
     * @param  array $data
     * @return array 
     */
    protected function process(array $data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->process($value);
            }
            if (trim($key)==='@include') {
                if ($this->directory === null) {
                    throw new Exception\RuntimeException('Cannot process @include statement for a json string');
                }
                $reader = clone $this;
                unset($data[$key]);
                $data = array_replace_recursive($data, $reader->fromFile($this->directory . '/' . $value));
            } 
        }
        return $data;
    }
}
