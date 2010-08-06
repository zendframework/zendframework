<?php
namespace ZendTest\Form\TestAsset;

class FormExtension extends \Zend\Form\Form
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}
