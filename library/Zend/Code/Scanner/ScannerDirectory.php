<?php

namespace Zend\Code\Scanner;

class ScannerDirectory implements ScannerInterface
{
    protected $fileScannerClass = 'Zend\Code\Scanner\ScannerFile';
    protected $fileScanners = array();
    
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
            throw new \InvalidArgumentException('Directory does not exist');
        }
        $this->directories[] = $realDir;
    }
    
    public function scan()
    {
        // iterate directories creating file scanners
    }
    
    
    
}
