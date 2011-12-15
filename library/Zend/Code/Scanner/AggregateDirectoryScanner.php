<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Scanner\TokenArrayScanner,
    Zend\Code\Exception;

class AggregateDirectoryScanner extends DirectoryScanner
{
    
    protected $isScanned = false;

    public function getNamespaces($returnScannerClass = false)
    {}
    
    /*
    public function getUses($returnScannerClass = false)
    {}
    */
    
    public function getIncludes($returnScannerClass = false)
    {}
    
    public function getClasses($returnScannerClass = false, $returnDerivedScannerClass = false)
    {
        $classes = array();
        foreach ($this->directories as $scanner) {
            $classes += $scanner->getClasses();
        }
        if ($returnScannerClass) {
            foreach ($classes as $index => $class) {
                $classes[$index] = $this->getClass($class, $returnScannerClass, $returnDerivedScannerClass);
            }
        }
        return $classes;
    }
    
    public function hasClass($class)
    {
        foreach ($this->directories as $scanner) {
            if ($scanner->hasClass($class)) {
                break;
            } else {
                unset($scanner);
            }
        }
        
        return (isset($scanner));
    }
    
    public function getClass($class, $returnScannerClass = true, $returnDerivedScannerClass = false)
    {
        foreach ($this->directories as $scanner) {
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
        }
        $scannerClass = new FunctionScanner();
        // @todo
    }

    /*
    public static function export()
    {
        // @todo
    }
    
    public function __toString()
    {
        // @todo
    }
    */
    
}