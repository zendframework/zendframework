<?php

namespace ZendTest\Code\Reflection\TestAsset;

use Zend\Code\Annotation\AnnotationInterface;

class SampleAnnotation implements AnnotationInterface
{
    public $content;

    public function initialize($content)
    {
        $this->content = __CLASS__ . ': ' . $content;
    }
}
