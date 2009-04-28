<?php

require_once 'Zend/Navigation/Page.php';

class My_Page extends Zend_Navigation_Page
{
    /**
     * Returns the page's href
     *
     * @return string
     */
    public function getHref()
    {
        return '#';
    }
}