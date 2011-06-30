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
    
    public function setFileScannerClass($fileScannerClass)
    {
        $this->fileScannerClass = $fileScannerClass;
    }
    
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
            $rdi = new RecursiveDirectoryIterator($directory);
            foreach (new RecursiveIteratorIterator($rdi) as $item) {
                if ($item->isFile() && preg_match('#\.php$#', $item->getRealPath())) {
                    $this->scannerFiles[] = new FileScanner($item->getRealPath());
                }
            }
        }
    }
    
    public function getNamespaces()
    {
    }
    
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

    public static function export() {}
    public function __toString() {} 
}
