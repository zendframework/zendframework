<?php

namespace ZendTest\Form\TestAsset;

use Zend\Form\Element;

class ElementWithNoDecorators extends Element
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}
