<?php
use Zend\Controller\Action as ActionController;

class ErrorController extends ActionController
{
    public function errorAction()
    {
        $this->broker('ViewRenderer')->setNoRender(true);
    }
}
