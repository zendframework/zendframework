<?php
require_once 'Zend/Navigation/Container.php';

class My_Container extends Zend_Navigation_Container
{
    public function addPage($page)
    {
        parent::addPage($page);
        $this->_pages = array();
    }
}