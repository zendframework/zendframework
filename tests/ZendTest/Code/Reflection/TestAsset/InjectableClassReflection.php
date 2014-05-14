<?php

namespace ZendTest\Code\Reflection\TestAsset;

use Zend\Code\Reflection\ClassReflection;

class InjectableClassReflection extends ClassReflection
{
    protected $fileScanner;

    public function setFileScanner($fileScanner)
    {
        $this->fileScanner = $fileScanner;
    }

    protected function createFileScanner($filename)
    {
        return $this->fileScanner;
    }
}
