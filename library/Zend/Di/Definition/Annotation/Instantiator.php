<?php

namespace Zend\Di\Definition\Annotation;

use Zend\Code\Annotation\Annotation;

class Instantiator implements Annotation
{

    protected $content = null;

    public function initialize($content)
    {
        $this->content = $content;
    }
}