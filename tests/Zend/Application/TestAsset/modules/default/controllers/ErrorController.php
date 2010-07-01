<?php
use Zend\Controller\Action as ActionController;

class ErrorController extends ActionController
{
    public function errorAction()
    {
        $this->_helper->getHelper('ViewRenderer')->setNoRender(true);
    }
}
