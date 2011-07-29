<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Exception;

class FileScanner extends TokenArrayScanner implements Scanner
{
    protected $isScanned = false;
    
    protected $file      = null;
    
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
            throw new Exception\InvalidArgumentException(sprintf(
                'File "%s" not found', $file
            ));
        }
        $this->reset();
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }

        if (!$this->file) {
            throw new Exception\RuntimeException('File was not provided');
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
