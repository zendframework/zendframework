<?php

require_once 'Zend/Tool/Framework/Provider/Interface.php';

class Zend_Tool_Framework_System_Provider_Phpinfo implements Zend_Tool_Framework_Provider_Interface
{

    public function showAction()
    {
        phpinfo();
    }

}