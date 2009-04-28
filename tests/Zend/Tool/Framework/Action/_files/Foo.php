<?php

require_once 'Zend/Tool/Framework/Action/Interface.php';

class Zend_Tool_Framework_Action_Foo implements Zend_Tool_Framework_Action_Interface
{
    public function getName()
    {
        return 'Foo';
    }
    

}
