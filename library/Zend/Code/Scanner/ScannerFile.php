<?php

namespace Zend\Code\Scanner;

class ScannerFile extends ScannerTokenArray implements ScannerInterface
{
    protected $isScanned = false;
    
    public function __construct($file = null, $options = null)
    {
        if ($file) {
            $this->setFile($file);
        }
    }
    
    public function setFile($file)
    {
        $this->file = $file;
        if (!file_exists($file)) {
            throw new \InvalidArgumentException('File not found');
        }
        $this->reset();
    }
    
    protected function scan()
    {
        if (!$this->file) {
            throw new \RuntimeException('File was not provided');
        }
        $this->setTokens(token_get_all(file_get_contents($this->file)));
        parent::scan();
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
