<?php

class Zend_Acl_UseCase1_Acl extends Zend_Acl
{

    public $customAssertion = null;

    public function __construct()
    {
        $this->customAssertion = new Zend_Acl_UseCase1_UserIsBlogPostOwnerAssertion();

        $this->addRole(new Zend_Acl_Role('guest'));                    // $acl->addRole('guest');
        $this->addRole(new Zend_Acl_Role('contributor'), 'guest');     // $acl->addRole('contributor', 'guest');
        $this->addRole(new Zend_Acl_Role('publisher'), 'contributor'); // $acl->addRole('publisher', 'contributor');
        $this->addRole(new Zend_Acl_Role('admin'));                    // $acl->addRole('admin');
        $this->add(new Zend_Acl_Resource('blogPost'));                 // $acl->addResource('blogPost');
        $this->allow('guest', 'blogPost', 'view');
        $this->allow('contributor', 'blogPost', 'contribute');
        $this->allow('contributor', 'blogPost', 'modify', $this->customAssertion);
        $this->allow('publisher', 'blogPost', 'publish');
    }

}