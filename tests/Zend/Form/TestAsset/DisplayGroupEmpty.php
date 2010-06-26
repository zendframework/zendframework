<?php

namespace ZendTest\Form\TestAsset;

use Zend\Form\DisplayGroup;

class DisplayGroupEmpty extends DisplayGroup
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}
