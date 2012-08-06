<?php

namespace ZendTest\Code\Scanner\TestAsset\Annotation;

use Zend\Code\Annotation\AnnotationInterface;

class Bar implements AnnotationInterface
{
    protected $content = null;

    public function initialize($content)
    {
        $this->content = $content;
    }
}
