<?php

namespace Zend\Di\Definition\Annotation;

use Zend\Code\Annotation\AnnotationInterface;

class Instantiator implements AnnotationInterface
{

    protected $content = null;

    public function initialize($content)
    {
        $this->content = $content;
    }
}
