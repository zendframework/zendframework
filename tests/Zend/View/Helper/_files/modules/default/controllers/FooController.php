<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

class FooController extends Zend_Controller_Action
{
    public function barAction()
    {
    }

    public function bazAction()
    {
        $this->view->message = $this->_getParam('bat', 'BOGUS');
    }

    public function forwardAction()
    {
        $this->_forward('bar');
    }

    public function redirectAction()
    {
        $this->_helper->redirector->setExit(false);
        $this->_redirect('/foo/bar');
    }
}
