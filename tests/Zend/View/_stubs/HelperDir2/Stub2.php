<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_View_Helper_Stub2 
{
    public $view;

	public function stub2()
	{
		return 'bar';
	}

    public function setView(Zend_View $view)
    {
        $this->view = $view;
        return $this;
    }
}
