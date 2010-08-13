<?php
use Zend\Controller\Action\Helper\AbstractHelper;

class TestHelper extends AbstractHelper
{
    public $count = 0;

    public function init()
    {
        ++$this->count;
    }
}
