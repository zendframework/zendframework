<?php

namespace Zend\Code\Scanner\DerivedScanner;

use Zend\Code\Scanner\DirectoryScanner,
    Zend\Code\Scanner\ClassScanner;

class DerivedClassScanner extends ClassScanner
{
    /**
     * @var Zend\Code\Scanner\DirectoryScanner
     */
    protected $directoryScanner = null;
    
    /**
     * @var Zend\Code\Scanner\ClassScanner
     */  
    protected $classScanner = null;
    protected $parentClassScanners = array();
    
    public function __construct(ClassScanner $classScanner, DirectoryScanner $directoryScanner)
    {
        $this->classScanner = $classScanner;
        $this->directoryScanner = $directoryScanner;
        
        $currentScannerClass = $classScanner;
        
        while ($currentScannerClass && $currentScannerClass->hasParentClass()) {
            $currentParentClassName = $currentScannerClass->getParentClass(); 
            $this->parentClassScanners[$currentParentClassName] = null;
            if ($directoryScanner->hasClass($currentParentClassName)) {
                $currentParentClass = $directoryScanner->getClass($currentParentClassName);
                $this->parentClassScanners[$currentParentClassName] = $currentParentClass;
                $currentScannerClass = $currentParentClass;
            } else {
                $currentScannerClass = false;
            }
        }
    }
    
    protected function scan()
    {
        $this->classScanner->scan();
    }
    
    public function getName()
    {
        return $this->classScanner->getName();
    }
    
    
    
}