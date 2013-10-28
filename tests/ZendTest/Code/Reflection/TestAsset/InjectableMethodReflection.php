<?php

namespace ZendTest\Code\Reflection\TestAsset;

use Zend\Code\Reflection\MethodReflection;

class InjectableMethodReflection extends MethodReflection
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
