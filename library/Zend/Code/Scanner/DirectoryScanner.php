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
    protected $fileScannerFileClass = 'Zend\Code\Scanner\FileScanner';
    protected $fileScanners         = array();
    
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
    
    public function setFileScannerClass($fileScannerClass)
    {
        $this->fileScannerClass = $fileScannerClass;
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
    }
    
    public function getNamespaces()
    {
    }
    
    public function getClasses($returnScannerClass = false, $returnDerivedScannerClass = false)
    {
        $this->scan();
        
        $classes = array();
        foreach ($this->fileScanners as $scannerFile) {
            /* @var $scannerFile Zend\Code\Scanner\FileScanner */
            $classes = array_merge($classes, $scannerFile->getClasses($returnScannerClass));
        }
        
        if ($returnDerivedScannerClass) {
            foreach ($classes as $index => $class) {
                if ($class instanceof ClassScanner) {
                    $classes[$index] = new DerivedClassScanner($class, $this);
                }
            }
        }
        
        return $classes;
    }
    
    public function hasClass($class)
    {
        return (in_array($class, $this->getClasses()));
    }
    
    public function getClass($class, $returnScannerClass = true, $returnDerivedScannerClass = false)
    {
        $this->scan();
        
        foreach ($this->fileScanners as $scannerFile) {
            /* @var $scannerFile Zend\Code\Scanner\FileScanner */
            $classesInFileScanner = $scannerFile->getClasses(false);
            if (in_array($class, $classesInFileScanner)) {
                $returnClass = $scannerFile->getClass($class, $returnScannerClass);
            }
        }
        
        if (isset($returnClass) && $returnClass instanceof ClassScanner && $returnDerivedScannerClass) {
            return new DerivedClassScanner($returnClass, $this);
        } elseif (isset($returnClass)) {
            return $returnClass;
        }
        
        throw new Exception\InvalidArgumentException('Class not found.');
    }

    public static function export() {}
    public function __toString() {} 
}
