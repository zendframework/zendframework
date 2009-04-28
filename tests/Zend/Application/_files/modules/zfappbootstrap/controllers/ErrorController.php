<?php
class Zfappbootstrap_ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->getInvokeArg('bootstrap')->getContainer()->error = $this->_getParam('error_handler');
    }
}
