<?php

namespace ZendTest\Form\TestAsset;

use Zend\Form;

class SubForm extends Form\SubForm
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}
