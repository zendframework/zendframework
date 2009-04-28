<?php

require_once 'Zend/Controller/Action.php';

class Foo_BazController extends Zend_Controller_Action
{
    
    public function barOneAction()
    {
        // this is for testActionCalledWithinActionResetsResponseState
    }
    
    public function barTwoAction()
    {
        // this is for testActionCalledWithinActionResetsResponseState
    }
    
    public function barThreeAction()
    {
        // this is for testActionCalledWithinActionResetsResponseState
    }    
    
}

