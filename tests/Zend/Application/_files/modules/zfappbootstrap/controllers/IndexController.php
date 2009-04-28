<?php
class Zfappbootstrap_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        echo "Inside index action!\n";
        $this->_helper->viewRenderer->setNoRender();
        $this->getInvokeArg('bootstrap')->getContainer()->zfappbootstrap = true;
    }
}
