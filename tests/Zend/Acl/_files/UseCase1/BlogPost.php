<?php

class Zend_Acl_UseCase1_BlogPost implements Zend_Acl_Resource_Interface
{
    public $owner = null;
    public function getResourceId()
    {
        return 'blogPost';
    }
}