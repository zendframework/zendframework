<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_View_Helper_Datetime
{
    public function datetime()
    {
        return $this;
    }
}
