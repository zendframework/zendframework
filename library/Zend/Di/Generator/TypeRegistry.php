<?php

namespace Zend\Di\Generator;

class TypeRegistry implements \Iterator
{
    const ITERATE_ALL = 'all';
    const ITERATE_NEW = 'new';
    
    protected $iteratorMode = self::ITERATE_ALL;
    protected $iteratorValid = false;
    
    protected $fileStatInformation = array();
    protected $types = array();
    
    
    
    public function __construct() {}
    
    public function setFileStatInformation(array $fileStatInformation)
    {
        $this->fileStatInformation = $fileStatInformation;
    }
    
    public function hasFileStatUpdates()
    {
        return true;
    }
    
    public function getFileStatInformation()
    {
        return $this->fileStatInformation;
    }
    
    public function isFileStatFresh($file, $mtime)
    {
        return true;
    }
    
    public function register($type, $file = null, $fileMtime = null)
    {
        if ($file == null || $fileMtime == null) {
            list($file, $fileMtime) = $this->discoverClassFile($type);
        }
        
        $this->fileStatInformation[$file] = $fileMtime;
        
        /**
         * @todo If class is already here, and mtime has not changed, do not update entry, return
         */
        $isNew = true;
        
        $this->types[] = array('class' => $type, 'file' => $file, 'mtime' => $fileMtime, 'isNew' => true);
    }

    public function setIteratorMode($iteratorMode)
    {
        $this->iteratorMode = $iteratorMode;
    }
    
    public function rewind()
    {
        reset($this->types);
        if (count($this->types) > 0) {
            $this->iteratorValid = true;
        }
    }
    
    public function valid()
    {
        return $this->iteratorValid;
    }
    
    public function next()
    {
        $return = next($this->types);
        $this->iteratorValid = ($return !== false);
    }
    
    public function current()
    {
        $classInfo = current($this->types);
        return $classInfo['class'];
    }
    
    public function key()
    {
        return key($this->types);
    }
    
    /**
     * discoverClassFile()
     * 
     * This should technically never run since in most cases someone else (some iterator)
     * will determin the file and fileMtime.  But in cases where its not provided in register(),
     * this will run.
     * 
     * @param string $class
     * @return array ($file, $mtime)
     */
    protected function discoverClassFile($class)
    {
        $classRefl = new \ReflectionClass($class);
        $file = $classRefl->getFileName();
        $fmtime = filemtime($file);
        unset($classRefl);
        return array($file, $fmtime);
    }
    
    public function toArray()
    {
        $classes = array();
        foreach ($this as $class) {
            $classes[] = $class;
        }
        return $classes;
    }
    
}