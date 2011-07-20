<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Scanner\TokenArrayScanner,
    Zend\Code\Exception;

class AggregateDirectoryScanner extends DirectoryScanner
{
    
    protected $isScanned = false;
    protected $scanners = array();
    
    public function addScanner(Scanner $scanner)
    {
        if (!$scanner instanceof DirectoryScanner && !$scanner instanceof TokenArrayScanner) {
            throw new Exception\InvalidArgumentException('Not a valid scanner to aggregate');
        }
        
        $this->scanners[] = $scanner;
        
        if ($this->isScanned) {
            $scanner->scan();
        }
    }
    
    public function getNamespaces($returnScannerClass = false)
    {}
    
    /*
    public function getUses($returnScannerClass = false)
    {}
    */
    
    public function getIncludes($returnScannerClass = false)
    {}
    
    public function getClasses($returnScannerClass = false)
    {
        $classes = array();
        foreach ($this->scanners as $scanner) {
            $classes += $scanner->getClasses();
        }
        return $classes;
    }
    
    public function hasClass($class)
    {
        foreach ($this->scanners as $scanner) {
            if ($scanner->hasClass($class)) {
                break;
            } else {
                unset($scanner);
            }
        }
        
        return (isset($scanner));
    }
    
    public function getClass($class, $returnScannerClass = 'Zend\Code\Scanner\ClassScanner')
    {
        foreach ($this->scanners as $scanner) {
            if ($scanner->hasClass($class)) {
                break;
            } else {
                unset($scanner);
            }
        }
        
        if (!isset($scanner)) {
            throw new Exception\RuntimeException('Class by that name was not found.');
        }
        
        
        $classScanner = $scanner->getClass($class);
        return new DerivedClassScanner($classScanner, $this);
    }
    
    public function getFunctions($returnScannerClass = false)
    {
        $this->scan();
        
        if (!$returnScannerClass) {
            $functions = array();
            foreach ($this->infos as $info) {
                if ($info['type'] == 'function') {
                    $functions[] = $info['name'];
                }
            }
            return $functions;
        } else {
            if ($returnScannerClass === true) {
                $returnScannerClass = 'Zend\Code\Scanner\FunctionScanner';
            }
            $scannerClass = new $returnScannerClass;
            // @todo
        }
    }
    
    public static function export()
    {
        // @todo
    }
    
    public function __toString()
    {
        // @todo
    }
    
}