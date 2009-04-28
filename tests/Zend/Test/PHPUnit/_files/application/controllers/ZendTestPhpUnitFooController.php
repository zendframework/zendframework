<?php
class ZendTestPhpUnitFooController extends Zend_Controller_Action
{
    public function barAction()
    {
    }

    public function bazAction()
    {
    }

    public function sessionAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $_SESSION = array('foo' => 'bar', 'bar' => 'baz');
    }
}
