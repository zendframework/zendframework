<?php
namespace ZendTest\Controller\Action\TestAsset;

use Zend\Controller\Action\Helper\AbstractHelper;

class TestHelper extends AbstractHelper
{
    public $count = 0;

    public $preDispatch  = false;
    public $postDispatch = false;

    public function init()
    {
        ++$this->count;
    }

    public function preDispatch()
    {
        $this->preDispatch = true;
    }

    public function postDispatch()
    {
        $this->postDispatch = true;
    }
}
