<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';

class Zend_Tool_Framework_Manifest_ActionOne implements Zend_Tool_Framework_Action_Interface
{
    public function getName()
    {
        return 'ActionOne';
    }
}