<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Exception,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator;

class DirectoryScanner implements Scanner
{
    protected $isScanned            = false;
    protected $directories          = array();
    protected $fileScanners         = array();
    protected $classToFileScanner   = null;
    
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
    
    public function addDirectory($directory)
    {
        if ($directory instanceof DirectoryScanner) {
            $this->directories[] = $directory;
        } elseif (is_string($directory)) {
            $realDir = realpath($directory);
            if (!$realDir || !is_dir($realDir)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Directory "%s" does not exist', $realDir
                ));
            }
            $this->directories[] = $realDir;
        } else {
            throw new Exception\InvalidArgumentException(
                'The argument provided was neither a DirectoryScanner or directory path'
            );
        }
    }
    
    public function addDirectoryScanner(DirectoryScanner $directoryScanner)
    {
        $this->addDirectory($directoryScanner);
    }
    
    public function addFileScanner(FileScanner $fileScanner)
    {
        $this->fileScanners[] = $fileScanner;
    }
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }
        
        // iterate directories creating file scanners
        foreach ($this->directories as $directory) {
            if ($directory instanceof DirectoryScanner) {
                $directory->scan();
                if ($directory->fileScanners) {
                    $this->fileScanners = array_merge($this->fileScanners, $directory->fileScanners);
                }
            } else {
                $rdi = new RecursiveDirectoryIterator($directory);
                foreach (new RecursiveIteratorIterator($rdi) as $item) {
                    if ($item->isFile() && preg_match('#\.php$#', $item->getRealPath())) {
                        $this->fileScanners[] = new FileScanner($item->getRealPath());
                    }
                }
            }
        }
        
        $this->isScanned = true;
    }
    
    public function getNamespaces()
    {
    }

    public function getFiles($returnFileScanners = false)
    {
        $this->scan();

        $return = array();
        foreach ($this->fileScanners as $fileScanner) {
            $return[] = ($returnFileScanners) ? $fileScanner : $fileScanner->getFile();
        }

        return $return;
    }

    public function getClasses($returnScannerClass = false, $returnDerivedScannerClass = false)
    {
        $this->scan();
        
        if ($this->classToFileScanner === null) {
            $this->createClassToFileScannerCache();
        }
        
        if ($returnScannerClass == false) {
            return array_keys($this->classToFileScanner);
        }
        
        $returnClasses = array();
        foreach ($this->classToFileScanner as $className => $fsIndex) {
            $classScanner = $this->fileScanners[$fsIndex]->getClass($className, $returnScannerClass);
            if ($returnDerivedScannerClass) {
                $classScanner = new DerivedClassScanner($classScanner, $this);
            }
            $returnClasses[] = $classScanner;
        }
        
        return $returnClasses;
    }
    
    public function hasClass($class)
    {
        $this->scan();
        
        if ($this->classToFileScanner === null) {
            $this->createClassToFileScannerCache();
        }
        
        return (isset($this->classToFileScanner[$class]));
    }
    
    public function getClass($class, $returnScannerClass = true, $returnDerivedScannerClass = false)
    {
        $this->scan();
        
        if ($this->classToFileScanner === null) {
            $this->createClassToFileScannerCache();
        }
        
        if (!isset($this->classToFileScanner[$class])) {
            throw new Exception\InvalidArgumentException('Class not found.');
        }
        
        $fs = $this->fileScanners[$this->classToFileScanner[$class]];
        $returnClass = $fs->getClass($class, $returnScannerClass);
        
        if (($returnClass instanceof ClassScanner) && $returnDerivedScannerClass) {
            return new DerivedClassScanner($returnClass, $this);
        }

        return $returnClass;
    }

    protected function createClassToFileScannerCache()
    {
        if ($this->classToFileScanner !== null) {
            return;
        }
        
        $this->classToFileScanner = array();
        foreach ($this->fileScanners as $fsIndex => $fileScanner) {
            $fsClasses = $fileScanner->getClasses();
            foreach ($fsClasses as $fsClassName) {
                $this->classToFileScanner[$fsClassName] = $fsIndex;
            }
        }
    }
    
    public static function export() {}
    public function __toString() {} 
}
