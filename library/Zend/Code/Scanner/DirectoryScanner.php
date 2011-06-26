<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Exception,
    RecursiveDirectoryIterator;

class DirectoryScanner implements Scanner
{
    protected $isScanned            = false;
    protected $directories          = array();
    protected $fileScannerFileClass = 'Zend\Code\Scanner\FileScanner';
    protected $scannerFiles         = array();
    
    public function __construct($directory = null)
    {
        if ($directory) {
            if (is_string($directory)) {
                $this->addDirectory($directory);
            } elseif (is_array($directory)) {
                foreach ($directory as $d) {
                    $this->addDirectory($d);
                }
            }
        }
    }
    
    /*
    public function setFileScannerClass($fileScannerClass)
    {
        $this->fileScannerClass = $fileScannerClass;
    }
    */
    
    public function addDirectory($directory)
    {
        $realDir = realpath($directory);
        if (!$realDir) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Directory "%s" does not exist', $realDir
            ));
        }
        $this->directories[] = $realDir;
    }
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }
        
        
        
        // iterate directories creating file scanners
        foreach ($this->directories as $directory) {
            foreach (new RecursiveDirectoryIterator($directory) as $item) {
                if ($item->isFile() && preg_match('#\.php$#', $item->getRealPath())) {
                    $this->scannerFiles[] = new FileScanner($item->getRealPath());
                }
            }
        }
    }
    
    public function getNamespaces()
    {}
    
    public function getClasses($returnScannerClass = false)
    {
        $this->scan();
        
        $classes = array();
        foreach ($this->scannerFiles as $scannerFile) {
            /* @var $scannerFile Zend\Code\Scanner\FileScanner */
            $classes = array_merge($classes, $scannerFile->getClasses($returnScannerClass));
        }
        
        return $classes;
    }
    

    /**
     * 
     * Enter description here ...
     * @param string|int $classNameOrInfoIndex
     * @param string $returnScannerClass
     * @return Zend\Code\Scanner\ClassScanner
     */
    /*
    public function getClass($classNameOrInfoIndex, $returnScannerClass = 'Zend\Code\Scanner\ClassScanner')
    {
        $this->scan();
        
        // process the class requested
        static $baseScannerClass = 'Zend\Code\Scanner\ClassScanner';
        if ($returnScannerClass !== $baseScannerClass) {
            if (!is_string($returnScannerClass)) {
                $returnScannerClass = $baseScannerClass;
            }
            $returnScannerClass = ltrim($returnScannerClass, '\\');
            if ($returnScannerClass !== $baseScannerClass && !is_subclass_of($returnScannerClass, $baseScannerClass)) {
                throw new \RuntimeException('Class must be or extend ' . $baseScannerClass);
            }
        }
        
        if (is_int($classNameOrInfoIndex)) {
            $info = $this->infos[$classNameOrInfoIndex];
            if ($info['type'] != 'class') {
                throw new \InvalidArgumentException('Index of info offset is not about a class');
            }
        } elseif (is_string($classNameOrInfoIndex)) {
            $classFound = false;
            foreach ($this->infos as $infoIndex => $info) {
                if ($info['type'] === 'class' && $info['name'] === $classNameOrInfoIndex) {
                    $classFound = true;
                    break;
                }
            }
            if (!$classFound) {
                return false;
            }
        }
        
        $uses = array();
        for ($u = 0; $u < count($this->infos); $u++) {
            if ($this->infos[$u]['type'] == 'use') {
                foreach ($this->infos[$u]['statements'] as $useStatement) {
                    $useKey = ($useStatement['as']) ?: $useStatement['asComputed'];
                    $uses[$useKey] = $useStatement['use'];
                }
            }
        }
        
        return new $returnScannerClass(
            array_slice($this->tokens, $info['tokenStart'], ($info['tokenEnd'] - $info['tokenStart'] + 1)), // zero indexed array
            $info['namespace'],
            $uses
            );
    }
    */
    
    
    
    public static function export() {}
    public function __toString() {} 
    
}
