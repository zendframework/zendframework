<?php

namespace Zend\Code\Scanner;

use Zend\Code\Scanner,
    Zend\Code\Exception,
    Zend\Code\Annotation\AnnotationManager;

class FileScanner extends TokenArrayScanner implements Scanner
{
    /**
     * @var bool
     */
    protected $isScanned = false;

    /**
     * @var string
     */
    protected $file = null;

    public function __construct($file, AnnotationManager $annotationManager = null)
    {
        $this->setFile($file);
        if ($annotationManager) {
            $this->setAnnotationManager($annotationManager);
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

        $this->setTokens(token_get_all(file_get_contents($this->file)));
        parent::scan();
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
