<?php
require_once 'Zend/Controller/Action/Helper/Url.php';

class My_UrlHelper extends Zend_Controller_Action_Helper_Url
{
    const RETURN_URL = 'spotify:track:2nd6CTjR9zjHGT0QtpfLHe';

    public function url($urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        return self::RETURN_URL;
    }
}