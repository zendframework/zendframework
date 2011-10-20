<?php

namespace ZendTest\Code\Scanner\TestAsset\Annotation;

use Zend\Code\Annotation\Annotation;

class Foo implements Annotation
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
