<?php
class ZfAppBootstrapCustomView extends Zend_Application_Bootstrap_Bootstrap {
    public function _initView()
    {
		$view = new Zend_View();
		$view->setInMethodByTest = true;
		return $view;
    }
}
