<?php

namespace ZendTest\Code\Scanner\TestAsset\Annotation;

use Zend\Code\Annotation\AnnotationInterface;

class Foo implements AnnotationInterface
{
    protected $content = null;

    public function initialize($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

}
