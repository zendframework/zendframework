<?php

require_once 'Zend/Tool/Framework/Loader/Abstract.php';

class Zend_Tool_Framework_EmptyLoader extends Zend_Tool_Framework_Loader_Abstract
{
    protected function _getFiles()
    {
        return array();
    }
}