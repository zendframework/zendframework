<?php

namespace Zend\Di\Definition\Annotation;

use Zend\Code\Annotation\Annotation;

class InjectAnnotation implements Annotation
{

    protected $content = null;

    public function getName()
    {
        return 'inject';
    }

    public function createAnnotation($annotationContent)
    {
        $this->content = $annotationContent;
    }
}